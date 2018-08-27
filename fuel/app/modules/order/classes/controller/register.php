<?php
namespace Order;

use Order\Controller_Base;
use Fuel\Core\Response;
use Fuel\Core\Config;
use Fuel\Core\DB;
use Fuel\Core\Session;
use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\HttpServerErrorException;
/**
 * カートコントローラクラス
 */
class Controller_Register extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'レジ';

	/**
	 * 初期表示
	 */
	public function action_index() {
		$member_id = $this->get_member_id();

		$cart = Common_Cart::instance($member_id);

		Session::set(SESSION_KEY_CART, $cart);

		$this->render($cart);
	}

	/**
	 * 確定処理
	 */
	public function action_save() {
		$cart = Session::get(SESSION_KEY_CART);
		if (empty($cart)) {
			throw new \Exception_403();
		}
		if ($cart->count_item() == 0) {
			Response::redirect('/order/register');
		}

		if (!$cart->check()) {
			throw new \Exception_cartupdated();
		}

		$data = Input::post();
		if (Arr::get($data, 'delivery_date') != '') {
			$cart->set_delivery_date(Arr::get($data, 'delivery_date'));
		}
		$cart->set_comment(Arr::get($data, 'comment'));

		if (!$this->validate_add($data)) {
			$this->render($cart, 'register/index');
			return;
		}

		$member = $this->get_member($this->get_member_id());

		$order_id = $this->create_order($member, $cart);
		if (!$order_id) {
			throw new HttpServerErrorException();
		}
		Session::delete(SESSION_KEY_CART);

		$sendmail = new \Sendmail_Order();
		if (!$sendmail->send($order_id)) {
			// TODO
		}

		Response::redirect('/order/register/complete');
	}

	private function get_delivery_dates() {
		$limit = 10;
		$start = date('Y-m-d', strtotime('+1 day'));
		$end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));

		$dates = \Common_Util::range_date($start, $limit, false);

		$holidays = \Model_Holiday::query()
				  ->where('date', '>=', $start)
				  ->where('date', '<=', $end)
				  ->get();

		foreach($holidays as $holiday){
			$key = date('Ymd', strtotime($holiday->date));
			unset($dates[$key]);
		}

		return $dates;
	}


	/**
	 * 完了
	 */
	public function action_complete() {
		$this->render();
	}

	/**
	 * 発注履歴からカートに入れる処理
	 */
	public function action_into_cart($order_id) {
		$member_id = $this->get_member_id();

		$order = \Model_Order::find($order_id, array(
				'where' => array(
						'member_id' => $member_id
				)
		));
		if (empty($order)) {
			Response::redirect('/order/home');
		}

		if (!$this->replace_carts($order, $member_id)) {
			// TODO
		}

		Response::redirect('/order/register');
	}


	/**
	 * 発注者情報を取得する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function get_member($member_id) {
		return \Model_Member::query()
			->where('id', '=', $member_id)
			->where('status', '=', Config::get('define.member_status.enable'))
			->get_one();
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		if (!empty($data['delivery_date'])) {
			$validation->add('delivery_date', '納品希望日')
			->add_rule('valid_date', 'Ymd')
			->add_rule('match_collection', array_keys($this->get_delivery_dates()));
		}

		$validation->add('comment', '備考')
			->add_rule('max_length', 1000);

		return $this->validate($validation, $data);
	}

	/**
	 * 受注データを生成する
	 *
	 * @param Model_Member $member 発注者アカウント情報
	 * @param Common_Cart $cart カート情報
	 */
	private function create_order($member, $cart) {
		$order_id = false;
		try {
			DB::start_transaction();

			$order_id = $this->insert_order($member, $cart);
			if (empty($order_id)) {
				DB::rollback_transaction();
				return false;
			}

			foreach ($cart->get_carts() as $detail) {
				if (!$this->insert_order_detail($order_id, $detail, $member->id)) {
					DB::rollback_transaction();
					return false;
				}
				if(!$this->update_order_frequency($member->id, $detail['code'])){
					DB::rollback_transaction();
					return false;
				}
			}

			if (!$this->delete_carts($member->id)) {
				DB::rollback_transaction();
				return false;
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return $order_id;
	}

	/**
	 * カート内を受注内容で置き換える
	 *
	 * @param Model_Order $order 受注情報
	 * @param int $member_id 発注者アカウントID
	 */
	private function replace_carts($order, $member_id) {
		try {
			DB::start_transaction();

			if (!$this->delete_carts($member_id)) {
				DB::rollback_transaction();
				return false;
			}

			foreach ($order->order_details as $detail) {
				$item = $this->get_item($detail->item_code, $member_id);
				if (empty($item)) {
					continue;
				}

				if (!$this->insert_cart($member_id, $item['id'], $item['item_renewal_datetime'], Arr::get($item, 'assign_renewal_datetime'),
						$detail->amount, $detail->amount_case)) {
					DB::rollback_transaction();
					return false;
				}
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return true;
	}

	/**
	 * 商品を取得する
	 *
	 * @param string $code 商品コード
	 * @param int $member_id 発注者ID
	 */
	private function get_item($code, $member_id) {
		$query = DB::select('items.id', 'items.code', 'items.name', 'items.size',
				'items.jan_code', 'items.price',  'items.price_case',
				array('item_categories.code', 'category_code'),
				array('item_categories.name', 'category_name'),
				array('items.renewal_datetime', 'item_renewal_datetime'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->where('items.code', '=', $code)
			->where('items.del_flg', '=', UNDELETED);

		if (Common_Assign::has_assign($member_id)) {
			$query->select(array('item_assigns.renewal_datetime', 'assign_renewal_datetime'));
			$query->join('item_assigns', 'INNER')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		}

		return $query->execute()->current();
	}

	/**
	 * 受注を登録する
	 *
	 * @param Model_Member $member 発注者アカウント情報
	 * @param Common_Cart $cart カート情報
	 */
	private function insert_order($member, $cart) {
		$values = array();
		$values['member_id'] = $member->id;
		$values['member_code'] = $member->code;
		$values['member_name'] = $member->name;
		$values['member_email'] = $member->email;
		$values['order_datetime'] = date('Y-m-d H:i:s');
		$values['amount'] = $cart->get_total_amount();
		$values['amount_case'] = $cart->get_total_amount_case();
		$values['payment'] = $cart->get_payment();
		$values['payment_tax'] = $cart->get_payment_tax();
		$values['tax'] = $cart->get_tax();
		$values['tax_rate'] = $cart->get_tax_rate();
		$values['delivery_date'] = $cart->get_delivery_date();
		$values['cancel_flg'] = false;
		$values['order_download_id'] = null;
		$values['comment'] = $cart->get_comment();

		$model = \Model_Order::forge($values);
		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * 受注明細を登録する
	 *
	 * @param int $order_id 受注ID
	 * @param array $cart カート情報
	 * @param int $member_id 発注者ID
	 */
	private function insert_order_detail($order_id, $cart, $member_id) {
		$item = $this->get_item($cart['code'], $member_id);

		$values = array();
		$values['order_id'] = $order_id;
		$values['category_code'] = $item['category_code'];
		$values['category_name'] = $item['category_name'];
		$values['item_id'] = $item['id'];
		$values['item_code'] = $item['code'];
		$values['item_name'] = $item['name'];
		$values['item_size'] = $item['size'];
		$values['jan_code'] = $item['jan_code'];
		$values['price'] = $item['price'];
		$values['price_tax'] = \Common_Util::add_tax($values['price']);
		$values['amount'] = $cart['amount'];
		$values['price_case'] = $item['price_case'];
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = $cart['amount_case'];
		$values['total'] = $values['price'] * $values['amount'] + $values['price_case'] * $values['amount_case'];
		$values['total_tax'] = $values['price_tax'] * $values['amount'] + $values['price_case_tax'] * $values['amount_case'];

		$model = \Model_Order_Detail::forge($values);

		return $model->save() !== false;
	}

	/**
	 * カートを登録する
	 *
	 * @param int $member_id 発注者ID
	 * @param int $item_id 商品ID
	 * @param string $item_renewal_datetime 商品更新日時
	 * @param string $item_assign_renewal_datetime 割当商品更新日時
	 * @param int $amount 数量(バラ)
	 * @param int $amount_case 数量(ケース)
	 */
	private function insert_cart($member_id, $item_id, $item_renewal_datetime, $item_assign_renewal_datetime, $amount, $amount_case) {
		$values = array();
		$values['member_id'] = $member_id;
		$values['item_id'] = $item_id;
		$values['item_renewal_datetime'] = $item_renewal_datetime;
		$values['item_assign_renewal_datetime'] = $item_assign_renewal_datetime;
		$values['amount'] = $amount;
		$values['amount_case'] = $amount_case;
		$values['update_user_id'] = $member_id;
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('carts')->values($values)->execute() !== false;
	}

	private function update_order_frequency($member_id, $item_code){
		$order_frequency = \Model_Order_Frequency::query()
			->where('member_id', '=', $member_id)
			->where('item_code', '=', $item_code)
			->get_one();

		if(empty($order_frequency)){
			$order_frequency = \Model_Order_Frequency::forge(array(
				'member_id' =>  $member_id,
				'item_code' =>  $item_code,
				'frequency' => 0
			));
		}

		$order_frequency->frequency = $order_frequency->frequency + 1;
		return $order_frequency->save() !== false;
	}

	/**
	 * カート情報を削除する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function delete_carts($member_id) {
		return DB::delete('carts')
			->where('member_id', '=', $member_id)
			->execute() !== false;
	}
}