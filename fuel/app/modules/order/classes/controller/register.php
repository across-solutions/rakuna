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

		$member = $this->get_member($this->get_member_id());
		$cart->set_delivery_kind(1);
		$cart->set_member_code(Arr::get($member, 'code'));
		$cart->set_member_name(Arr::get($member, 'name'));
		$cart->set_member_zip(Arr::get($member, 'zip'));
		$cart->set_member_address1(Arr::get($member, 'address1'));
		$cart->set_member_address2(Arr::get($member, 'address2'));
		$cart->set_member_address3(Arr::get($member, 'address3'));
		$cart->set_member_tel(Arr::get($member, 'tel'));
		$cart->set_member_fax(Arr::get($member, 'fax'));

		$cart->set_order_type('1');
		$cart->set_shipping_div('80');
		$cart->set_warehouse_div('000900');

		$nearest_shipping_date = \Common_Util::get_nearest_shipping_date('members', Arr::get($member, 'code'));
		if (!empty($nearest_shipping_date)) {
			$cart->set_shipping_date($nearest_shipping_date);
			$cart->set_delivery_date($nearest_shipping_date);
		}

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
		$cart->set_delivery_kind(Arr::get($data, 'delivery_kind'));
		$cart->set_member_code(Arr::get($data, 'member_code'));
		$cart->set_member_name(Arr::get($data, 'member_name'));
		$cart->set_member_zip(Arr::get($data, 'member_zip'));
		$cart->set_member_address1(Arr::get($data, 'member_address1'));
		$cart->set_member_address2(Arr::get($data, 'member_address2'));
		$cart->set_member_address3(Arr::get($data, 'member_address3'));
		$cart->set_member_tel(Arr::get($data, 'member_tel'));
		$cart->set_member_fax(Arr::get($data, 'member_fax'));
		$cart->set_delivery_code(Arr::get($data, 'delivery_code'));
		$cart->set_delivery_name(Arr::get($data, 'delivery_name'));
		$cart->set_delivery_receiver_name1(Arr::get($data, 'delivery_receiver_name1'));
		$cart->set_delivery_receiver_name2(Arr::get($data, 'delivery_receiver_name2'));
		$cart->set_delivery_zip(Arr::get($data, 'delivery_zip'));
		$cart->set_delivery_address1(Arr::get($data, 'delivery_address1'));
		$cart->set_delivery_address2(Arr::get($data, 'delivery_address2'));
		$cart->set_delivery_address3(Arr::get($data, 'delivery_address3'));
		$cart->set_delivery_tel(Arr::get($data, 'delivery_tel'));
		$cart->set_delivery_fax(Arr::get($data, 'delivery_fax'));

		$cart->set_order_type(Arr::get($data, 'order_type'));
		$cart->set_shipping_div(Arr::get($data, 'shipping_div'));
		$cart->set_warehouse_div(Arr::get($data, 'warehouse_div'));
		$cart->set_order_no(Arr::get($data, 'order_no'));

		if (Arr::get($data, 'delivery_date') != '') {
			$cart->set_delivery_date(Arr::get($data, 'delivery_date'));
		}

		if (Arr::get($data, 'shipping_date') != '') {
			$cart->set_shipping_date(Arr::get($data, 'shipping_date'));
		}

		$cart->set_comment(Arr::get($data, 'comment'));

		if (!$this->validate_add($data)) {
			$this->render($cart, 'register/index');
			return;
		}

		$member = $this->get_member($this->get_member_id());

		if (!$this->check_order($member, $cart)) {
			$this->set_error_message('ケースとバラが混在しているか、在庫品と在庫品以外が混在してます');
			$this->render($cart, 'register/index');
			return;
		}

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

	/**
	 * 出荷日取得
	 *
	 * @return array $dates 出荷日
	 */
	private function get_shipping_dates() {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$lead_time = Arr::get($member, 'lead_time');

		$limit = 10;
		$day = 2;

		if (!is_null($lead_time)) {
			$day += intval($lead_time);
		}

		$start = date('Y-m-d', strtotime('+' . $day . ' day'));
		$end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));

		$dates = \Common_Util::range_date($start, $limit, false);

		return $dates;
	}

	/**
	 * 納期取得
	 *
	 * @return array $dates 納期
	 */
	private function get_delivery_dates() {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$lead_time = Arr::get($member, 'lead_time');

		$limit = 12;
		$day = 2;

		if (!is_null($lead_time)) {
			$day += intval($lead_time);
		}

		$start = date('Y-m-d', strtotime('+' . $day . ' day'));
		$end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));

		$dates = \Common_Util::range_date($start, $limit, false);

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
		$member = DB::select('members.id', 'members.code', 'members.name', 'members.zip',
				'members.address1', 'members.address2', 'members.address3', 'members.tel', 'members.fax', 'members.email',
				array('sales_representatives.sales_person_code', 'member_sales_person_code'),
				array('sales_representatives.sales_person_name', 'member_sales_person_name'),
				array('sales_representatives.department_code', 'department_code'))
			->from('members')
			->join('sales_representatives', 'LEFT')
				->on('sales_representatives.sales_person_code', '=', 'members.sales_person_code')
				->on('sales_representatives.del_flg', '=', DB::escape(UNDELETED))
			->where('members.id', '=', $member_id)
			->where('members.status', '=', Config::get('define.member_status.enable'))
			->execute()->current();
		return $member;
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$delivery_kind = Arr::get($data, 'delivery_kind');
		if ($delivery_kind == 1) {
			$validation->add('member_code', '納品先コード')
				->add_rule('required')
				->add_rule('alphanum')
				->add_rule('max_length', 20)
				->add_rule('exist', 'members', 'code');

			$validation->add('member_name', '納品先名')
				->add_rule('required')
				->add_rule('max_length', 40);

			$validation->add('member_zip', '納品先郵便番号')
				->add_rule('required')
				->add_rule('numhyphen')
				->add_rule('max_length', 8);

			$validation->add('member_address1', '納品先住所1')
				->add_rule('required')
				->add_rule('max_length', 50);

			$validation->add('member_address2', '納品先住所2')
				->add_rule('max_length', 50);

			$validation->add('member_address3', '納品先住所3')
				->add_rule('max_length', 50);

			$validation->add('member_tel', '納品先電話番号')
				->add_rule('numhyphen')
				->add_rule('max_length', 14);

			$validation->add('member_fax', '納品先FAX')
				->add_rule('numhyphen')
				->add_rule('max_length', 14);
		} else if ($delivery_kind == 2) {
			$validation->add('delivery_code', '納品先コード')
				->add_rule('required')
				->add_rule('alphanum')
				->add_rule('max_length', 20)
				->add_rule('exist', 'deliveries', 'code');

			$validation->add('delivery_name', '納品先名')
				->add_rule('required')
				->add_rule('max_length', 40);

			$validation->add('delivery_receiver_name1', '荷受け人名1')
				->add_rule('max_length', 40);

			$validation->add('delivery_receiver_name2', '荷受け人名2')
				->add_rule('max_length', 40);

			$validation->add('delivery_zip', '納品先郵便番号')
				->add_rule('required')
				->add_rule('numhyphen')
				->add_rule('max_length', 8);

			$validation->add('delivery_address1', '納品先住所1')
				->add_rule('required')
				->add_rule('max_length', 50);

			$validation->add('delivery_address2', '納品先住所2')
				->add_rule('max_length', 50);

			$validation->add('delivery_address3', '納品先住所3')
				->add_rule('max_length', 50);

			$validation->add('delivery_tel', '納品先電話番号')
				->add_rule('numhyphen')
				->add_rule('max_length', 14);

			$validation->add('delivery_fax', '納品先FAX')
				->add_rule('numhyphen')
				->add_rule('max_length', 14);
		}

		$validation->add('order_type', '発注タイプ')
			->add_rule('required')
			->add_rule('exist', 'order_types', 'id');

		$validation->add('shipping_div', '出荷区分')
			->add_rule('required')
			->add_rule('match_collection', Config::get('define.shipping_div'));

		$validation->add('warehouse_div', '倉庫')
			->add_rule('required')
			->add_rule('match_collection', Config::get('define.warehouse_div'));

		$validation->add('order_no', 'オーダーNo.')
			->add_rule('numeric')
			->add_rule('max_length', 10);

		$validation->add('shipping_date', '出荷予定日')
			->add_rule('required')
			->add_rule('valid_date', 'Ymd')
			->add_rule('match_collection', array_keys($this->get_shipping_dates()));

		$validation->add('delivery_date', '納期')
			->add_rule('required')
			->add_rule('valid_date', 'Ymd')
			->add_rule('match_collection', array_keys($this->get_delivery_dates()));

		$validation->add('comment', '備考')
			->add_rule('max_length', 1000);

		return $this->validate($validation, $data);
	}

	/**
	 * 在庫バリデート
	 *
	 * @param Model_Member $member 発注者アカウント情報
	 * @param Common_Cart $cart カート情報
	 */
	private function check_order($member, $cart) {
		$amount_flg = false;
		$amount_case_flg = false;
		$type_stock_flg = false;
		$type_not_stock_flg = false;
		foreach ($cart->get_carts() as $detail) {
			$item = $this->get_item($detail['code'], $member['id']);
			if (empty($item)) {
				throw new \Exception_Renewal();
			}

			if ($detail['amount'] > 0) {
				$amount_flg = true;
			}

			if ($detail['amount_case'] > 0) {
				$amount_case_flg = true;
			}

			if ($item['type'] == Config::get('define.item_type.stock')) {
				$type_stock_flg = true;
			} else {
				$type_not_stock_flg = true;
			}
		}

		if ($amount_flg && $amount_case_flg) {
			return false;
		}

		if ($type_stock_flg && $type_not_stock_flg) {
			return false;
		}

		return true;
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
				if (!$this->insert_order_detail($order_id, $detail, $member['id'])) {
					DB::rollback_transaction();
					return false;
				}
				if(!$this->update_order_frequency($member['id'], $detail['code'])){
					DB::rollback_transaction();
					return false;
				}
			}

			if (!$this->delete_carts($member['id'])) {
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

				$amount = 0;
				if ($item['hidden_flg_single'] == UNDELETED && !empty($item['unit_name'])) {
					$amount = $detail->amount;
				}

				$amount_case = 0;
				if ($item['hidden_flg_case'] == UNDELETED && !empty($item['unit_name_case'])) {
					$amount_case = $detail->amount_case;
				}

				if ($amount < 1 && $amount_case < 1) {
					continue;
				}

				if (!$this->insert_cart($member_id, $item['id'], $item['item_renewal_datetime'], Arr::get($item, 'assign_renewal_datetime'),
						$amount, $amount_case)) {
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
		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select('items.id', 'items.code', 'items.jan_code', 'items.name',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size', 'items.type',
				array('item_categories.code', 'category_code'),
				array('item_categories.name', 'category_name'),
				'items.price', 'items.price_case',
				array('item_assigns.price_case', 'assign_price_case'),
				array('item_assigns.price', 'assign_price'),
				array('group_assigns.price_case', 'group_price_case'),
				array('group_assigns.price', 'group_price'),
				'item_assigns.hidden_flg_single', 'item_assigns.hidden_flg_case',
				'items.cost',
				array('items.renewal_datetime', 'item_renewal_datetime'),
				array('item_assigns.renewal_datetime', 'assign_renewal_datetime'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->where('items.code', '=', $code)
			->where('items.hidden_flg', '=', UNDELETED)
			->where('items.del_flg', '=', UNDELETED);

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		return $query->execute()->current();
	}

	/**
	 * 発注タイプリストを取得する
	 *
	 * @param int $order_type_id 発注タイプID
	 */
	private function get_order_type($order_type_id) {
		$query = DB::select('order_types.id', 'order_types.name')
					->from('order_types')
					->where('order_types.id', '=', $order_type_id)
					->where('order_types.del_flg', '=', UNDELETED);

		return $query->execute()->current();
	}

	/**
	 * 受注を登録する
	 *
	 * @param Model_Member $member 発注者アカウント情報
	 * @param Common_Cart $cart カート情報
	 */
	private function insert_order($member, $cart) {
		$order_type_id = $cart->get_order_type();
		$order_type = $this->get_order_type($order_type_id);

		$values = array();
		$values['member_id'] = Arr::get($member, 'id');
		$values['member_code'] = Arr::get($member, 'code');
		$values['member_name'] = Arr::get($member, 'name');
		$values['member_email'] = Arr::get($member, 'email');
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

		$delivery_kind = $cart->get_delivery_kind();
		if ($delivery_kind == 1) {
			$delivery_code = $cart->get_member_code();
			$delivery_name = $cart->get_member_name();
			$delivery_receiver_name1 = null;
			$delivery_receiver_name2 = null;
			$delivery_zip = $cart->get_member_zip();
			$delivery_address1 = $cart->get_member_address1();
			$delivery_address2 = $cart->get_member_address2();
			$delivery_address3 = $cart->get_member_address3();
			$delivery_tel = $cart->get_member_tel();
			$delivery_fax = $cart->get_member_fax();
		} else if ($delivery_kind == 2) {
			$delivery_code = $cart->get_delivery_code();
			$delivery_name = $cart->get_delivery_name();
			$delivery_receiver_name1 = $cart->get_delivery_receiver_name1();
			$delivery_receiver_name2 = $cart->get_delivery_receiver_name2();
			$delivery_zip = $cart->get_delivery_zip();
			$delivery_address1 = $cart->get_delivery_address1();
			$delivery_address2 = $cart->get_delivery_address2();
			$delivery_address3 = $cart->get_delivery_address3();
			$delivery_tel = $cart->get_delivery_tel();
			$delivery_fax = $cart->get_delivery_fax();
		}
		$values['delivery_kind'] = $delivery_kind;
		$values['delivery_code'] = $delivery_code;
		$values['delivery_name'] = $delivery_name;
		$values['delivery_receiver_name1'] = $delivery_receiver_name1;
		$values['delivery_receiver_name2'] = $delivery_receiver_name2;
		$values['delivery_zip'] = $delivery_zip;
		$values['delivery_address1'] = $delivery_address1;
		$values['delivery_address2'] = $delivery_address2;
		$values['delivery_address3'] = $delivery_address3;
		$values['delivery_tel'] = $delivery_tel;
		$values['delivery_fax'] = $delivery_fax;

		$values['order_type_id'] = $order_type_id;
		$values['order_type_name'] = Arr::get($order_type, 'name');
		$values['shipping_date'] = $cart->get_shipping_date();
		$values['shipping_div'] = $cart->get_shipping_div();
		$values['shipping_div_name'] = Config::get('define.shipping_div_disp.' . $cart->get_shipping_div());
		$values['warehouse_div'] = $cart->get_warehouse_div();
		$values['warehouse_div_name'] = Config::get('define.warehouse_div_disp.' . $cart->get_warehouse_div());
		$values['order_no'] = $cart->get_order_no() === '' ? null : $cart->get_order_no();

		if (\Common_Member::is_agency()) {
			$values['sales_person_code'] = \Common_Member::get_agency_code();
			$values['sales_person_name'] = \Common_Member::get_agency_name();
			$values['department_code'] = \Common_Member::get_agency('department_code');
			$values['agency_order_flg'] = true;
		} else {
			$values['sales_person_code'] = Arr::get($member, 'member_sales_person_code');
			$values['sales_person_name'] = Arr::get($member, 'member_sales_person_name');
			$values['department_code'] = Arr::get($member, 'department_code');
			$values['agency_order_flg'] = false;
		}

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
		$values['item_unit_name_case'] = $item['unit_name_case'];
		$values['item_unit_name'] = $item['unit_name'];
		$values['item_size_case'] = $item['size_case'];
		$values['item_size'] = $item['size'];
		$values['item_type'] = $item['type'];
		$values['jan_code'] = $item['jan_code'];
		$price = $this->value($item, 'price', 'assign_price', 'group_price');
		$price_case = $this->value($item, 'price_case', 'assign_price_case', 'group_price_case');
		$values['price'] = $price;
		$values['price_tax'] = \Common_Util::add_tax($values['price']);
		$values['amount'] = $cart['amount'];
		$values['price_case'] = $price_case;
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = $cart['amount_case'];
		$values['total'] = $values['price'] * $values['item_size'] * $values['amount'] + $values['price_case'] * $values['item_size_case'] * $values['amount_case'];
		$values['total_tax'] = \Common_Util::add_tax($values['price'] * $values['item_size'] * $values['amount']) + \Common_Util::add_tax($values['price_case'] * $values['item_size_case'] * $values['amount_case']);
		$values['cost'] = $item['cost'];
		$total_amount = $values['item_size'] * $values['amount'] + $values['item_size_case'] * $values['amount_case'];
		$values['total_cost'] = $item['cost'] * $total_amount;

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

	/**
	 * 値を取得する(後のキーが優先される)
	 *
	 * @param array $data データ
	 * @param string $key1 キー1
	 * @param string $key2 キー2
	 * @param string $key3 キー3
	 */
	private function value($data, $key1, $key2 = null, $key3 = null) {
		$value = $data[$key1];

		if (!is_null($key2) && !is_null($data[$key2])) {
			$value = $data[$key2];
		}

		if (!is_null($key3) && !is_null($data[$key3])) {
			$value = $data[$key3];
		}

		return $value;
	}
}