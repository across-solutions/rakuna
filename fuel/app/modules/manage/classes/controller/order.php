<?php
namespace Manage;

use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\File;
use Fuel\Core\Format;
use Fuel\Core\Arr;
use Fuel\Core\Config;
/**
 * 受注管理コントローラクラス
 */
class Controller_Order extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('confirm', 'confirm_save', 'download',
							   'edit', 'edit_save',
							   'delete_save', 'view'
	);


	/**
	 * ページタイトル
	 */
	protected $title = '受注管理';

	/**
	 * 一覧画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 受注確定画面-初期表示
	 */
	public function action_confirm() {
		$this->render();
	}

	/**
	 * 受注確定画面-確定処理
	 */
	public function action_confirm_save() {
		$params = Input::get();
		$orders = $this->get_download_order($params);

		if (empty($orders)) {
			$this->set_error_message('受注データがみつかりませんでした');
			$this->render(null, 'order/confirm');
			return;
		}

		$order_download_id = $this->create_order_download($orders);
		if (!$order_download_id) {
			throw new \HttpServerErrorException();
		}

		Response::redirect('/manage/order/download/' . $order_download_id);
	}

	/**
	 * 受注ダウンロード画面-初期表示
	 */
	public function action_download($id) {
		$data = \Model_Order_Download::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}
		$this->render($data);
	}

	/**
	 * 受注ダウンロード画面-ダウンロード処理
	 */
	public function action_download_save() {
		$id = Input::post('id');
		if (empty($id)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$file = ORDER_CSV_PATH . $id . '.csv';
		if (!File::exists($file)) {
			$this->set_error_message('ファイルが存在しません');
			$this->render(null, 'history/download/index');
			return;
		}
		File::download($file, 'order.csv');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 受注ID
	 */
	public function action_edit($id) {
		$data = $this->get_order($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render(array("order" => $data,
							"new_order_details" => array()));
	}

	/**
	 * 編集画面-保存処理
	 */

	public function action_edit_save() {
		$data = Input::post();

		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$order = $this->get_order($data['id']);
		$new_order_details = array();

		if (empty($order)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!empty($order->order_download_id)) {
			$this->set_error_message('ダウンロード済みのため編集できません');
			$this->render(array("order" => $order,
								"new_order_details" => $new_order_details), 'order/edit');
			return;
		}

		$new_order_details = $this->get_new_order_details($data, $order->member_id);

		if (!$this->validate_edit($data)) {
			$this->marge_order($order, $data);
			$this->render(array("order" => $order,
								"new_order_details" => $new_order_details), 'order/edit');
			return;
		}

		if (!$this->exist_amount($data)) {

			if(!$this->cancel_order($order)){
				$this->marge_order($order, $data);
				$this->set_error_message('削除に失敗しました。');
				$this->render(array("order" => $order,
									"new_order_details" => $new_order_details), 'order/edit');
				return;
			}

			$this->set_info_message('削除しました');
			Response::redirect('/manage/dialog/complete');
		}


		if (!$this->edit_order($order, $new_order_details, $data)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$order = $this->get_order($data['id']);
		if (empty($order)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->cancel_order($order)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 詳細画面-初期表示
	 *
	 * @param int $id 受注ID
	 */
	public function action_view($id) {
		$data = \Model_Order::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 受注情報にフォームデータをマージする
	 *
	 * @param Model_Order $order 受注情報
	 * @param array $data フォームデータ
	 */
	private function marge_order($order, $data) {
		$amounts = $data['amount'];
		foreach ($amounts as $id => $amount) {
			$order->order_details[$id]->amount = $amount;
		}

		if (isset($data['amount_case'])) {
			$amount_cases = $data['amount_case'];
			foreach ($amount_cases as $id => $amount_case) {
				$order->order_details[$id]->amount_case = $amount_case;
			}
		}
	}

	/**
	 * 受注ダウンロード履歴登録処理
	 *
	 * @param array $orders 受注データ
	 */
	private function create_order_download($orders) {
		try {
			DB::start_transaction();

			$id = $this->insert_order_download(count($orders));
			if (!$id) {
				DB::rollback_transaction();
				return false;
			}

			foreach ($orders as $order) {
				if (!$this->update_download($order, $id)) {
					DB::rollback_transaction();
					return false;
				}
			}

			$csv = new \Download_Csv_Order();
			if (!$csv->output_csv($id, true)) {
				DB::rollback_transaction();
				return false;
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return $id;
	}

	/**
	 * 更新処理
	 *
	 * @param Model_Order $order 受注データ
	 * @param array $data フォームデータ
	 */
	private function edit_order($order, $new_order_details, $data) {
		try {
			DB::start_transaction();

			$total_amount = 0;
			$total_amount_case = 0;
			$total_payment = 0;
			$total_payment_tax = 0;

			foreach ($new_order_details as $detail) {

				$amount = $detail->amount;
				$amount_case = $detail->amount_case;

				if($amount == 0 && $amount_case == 0){
					continue;
				}

				$detail->order_id = $order->id;

				if(!$detail->save()){
					DB::rollback_transaction();
					return false;
				}

				$tax_price = \Common_Util::add_tax($detail->price * $detail->item_size, $order->tax_rate, 1);
				$tax_price_case = \Common_Util::add_tax($detail->price_case * $detail->item_size_case, $order->tax_rate, 1);

				$total_amount += $amount;
				$total_amount_case += $amount_case;
				$total_payment += $detail->price * $detail->item_size * $amount + $detail->price_case * $detail->item_size_case * $amount_case;
				$total_payment_tax += $tax_price * $amount + $tax_price_case * $amount_case;
			}

			foreach ($order->order_details as $detail) {
				$amount = isset($data['amount'][$detail->id]) ? $data['amount'][$detail->id] : 0;
				$amount_case = isset($data['amount_case'][$detail->id]) ? $data['amount_case'][$detail->id] : 0;

				if ($amount == 0 && $amount_case == 0) {
					if (!$detail->soft_delete()) {
						DB::rollback_transaction();
						return false;
					}
				} else {
					if (!$this->update_order_detail($detail, $amount, $amount_case)) {
						DB::rollback_transaction();
						return false;
					}
				}

				$tax_price = \Common_Util::add_tax($detail->price * $detail->item_size, $order->tax_rate, 1);
				$tax_price_case = \Common_Util::add_tax($detail->price_case * $detail->item_size_case, $order->tax_rate, 1);

				$total_amount += $amount;
				$total_amount_case += $amount_case;
				$total_payment += $detail->price * $detail->item_size * $amount + $detail->price_case * $detail->item_size_case * $amount_case;
				$total_payment_tax += $tax_price * $amount + $tax_price_case * $amount_case;
			}

			if (!$this->update_order($order, $total_amount, $total_amount_case, $total_payment, $total_payment_tax, $data)) {
				DB::rollback_transaction();
				return false;
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return true;
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$amounts = $data['amount'];
		foreach ($amounts as $id => $amount) {
			$validation->add('amount.' . $id, '数量')
				->add_rule('required')
				->add_rule('numeric')
				->add_rule('numeric_between', 0, 9999);
		}

		if (isset($data['amount_case'])) {
			$amount_cases = $data['amount_case'];
			foreach ($amount_cases as $id => $amount_case) {
				$validation->add('amount_case.' . $id, '数量')
					->add_rule('required')
					->add_rule('numeric')
					->add_rule('numeric_between', 0, 9999);
			}
		}

		if(isset($data['new_amount'])){
			$amounts = $data['new_amount'];
			foreach ($amounts as $id => $amount) {
				$validation->add('new_amount.' . $id, '数量')
											   ->add_rule('numeric')
											   ->add_rule('numeric_between', 0, 9999);
			}
		}

		if (isset($data['new_amount_case'])) {
			$amount_cases = $data['new_amount_case'];
			foreach ($amount_cases as $id => $amount_case) {
				$validation->add('new_amount_case.' . $id, '数量')
													->add_rule('numeric')
													->add_rule('numeric_between', 0, 9999);
			}
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
			->add_rule('valid_date', 'Y-m-d');

		return $this->validate($validation, $data);
	}

	/**
	 * 数量存在チェック
	 *
	 * @param array $data フォームデータ
	 */
	private function exist_amount($data) {
		if (isset($data['amount'])) {
			foreach ($data['amount'] as $amount) {
				if ($amount > 0) {
					return true;
				}
			}
		}

		if (isset($data['amount_case'])) {
			foreach ($data['amount_case'] as $amount_case) {
				if ($amount_case > 0) {
					return true;
				}
			}
		}

		if (isset($data['new_amount'])) {
			foreach ($data['new_amount'] as $new_amount) {
				if ($new_amount > 0) {
					return true;
				}
			}
		}

		if (isset($data['new_amount_case'])) {
			foreach ($data['new_amount_case'] as $new_amount_case) {
				if ($new_amount_case > 0) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * 受注情報を取得する
	 *
	 * @param int $order_id 受注ID
	 */
	private function get_order($order_id) {
		return \Model_Order::query()
			->related('order_details')
			->where('id', '=', $order_id)
			->where('cancel_flg', '=', 0)
			->get_one();
	}

	/**
	 * ダウンロード対象の受注データを取得する
	 */
	private function get_download_order($params) {
		$query = \Model_Order::query();
		$this->add_condition($query, $params);
		$query->order_by('order_datetime', 'asc');

		return $query->get();
	}

	/**
	 * 受注ダウンロード履歴を登録する
	 *
	 * @param int $count 件数
	 */
	private function insert_order_download($count) {
		$values = array();
		$values['user_id'] = $this->get_user_id();
		$values['download_datetime'] = date('Y-m-d H:i:s');
		$values['record_count'] = $count;

		$model = \Model_Order_Download::forge($values);

		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * キャンセルフラグを更新する
	 *
	 * @param Model_Order $order 受注
	 */
	private function cancel_order($order) {
		$order->cancel_flg = true;

		return $order->save() !== false;
	}

	/**
	 * 受注ダウンロードIDを更新する
	 *
	 * @param Model_Order $order 受注
	 * @param int $order_download_id 受注ダウンロードID
	 */
	private function update_download($order, $order_download_id) {
		$order->order_download_id = $order_download_id;

		return $order->save() !== false;
	}

	/**
	 * 商品を取得する
	 *
	 * @param int $item_id 商品ID
	 * @param int $member_id 発注者ID
	 */
	private function get_item($item_id, $member_id) {
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
				array('group_assigns.price', 'group_price'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->where('items.id', '=', $item_id)
			->where('items.hidden_flg', '=', UNDELETED)
			->where('items.del_flg', '=', UNDELETED);

		$query->join('item_assigns', 'LEFT')
			->on('item_assigns.item_code', '=', 'items.code')
			->on('item_assigns.member_id', '=', DB::escape($member_id))
			->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$item = $query->execute()->current();

		return $item;
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
	 * 受注を更新する
	 *
	 * @param Model_Order $order 受注
	 * @param int $amount 数量
	 * @param int $amount_case 数量(ケース)
	 * @param int $payment 合計額
	 * @param int $payment_tax 合計額(税込)
	 * @param array $data フォームデータ
	 */
	private function update_order($order, $amount, $amount_case, $payment, $payment_tax, $data) {
		if ($order->amount == $amount && $order->amount_case == $amount_case
				&& $order->payment == $payment && $order->payment_tax == $payment_tax
				&& $order->order_type_id == $data['order_type']
				&& $order->shipping_date == $data['shipping_date']
				&& $order->shipping_div == $data['shipping_div']
				&& $order->warehouse_div == $data['warehouse_div']
				&& $order->order_no == $data['order_no']) {
			return true;
		}

		$order->amount = $amount;
		$order->amount_case = $amount_case;
		$order->payment = $payment;
		$order->payment_tax = $payment_tax;
		$order->tax = $payment_tax - $payment;

		$order_type_id = $data['order_type'];
		$order_type = $this->get_order_type($order_type_id);

		$order->order_type_id = $order_type_id;
		$order->order_type_name = Arr::get($order_type, 'name');
		$order->shipping_date = $data['shipping_date'];
		$order->shipping_div = $data['shipping_div'];
		$order->shipping_div_name = Config::get('define.shipping_div_disp.' . $data['shipping_div']);
		$order->warehouse_div = $data['warehouse_div'];
		$order->warehouse_div_name = Config::get('define.warehouse_div_disp.' . $data['warehouse_div']);
		$order->order_no = $data['order_no'] === '' ? null : $data['order_no'];

		return $order->save() !== false;
	}

	/**
	 * 受注明細を更新する
	 *
	 * @param Model_Order_Detail $detail 受注明細
	 * @param int $amount 数量
	 * @param int $amount_case 数量(ケース)
	 */
	private function update_order_detail($detail, $amount, $amount_case) {
		if ($detail->amount == $amount && $detail->amount_case == $amount_case) {
			return true;
		}

		$detail->amount = $amount;
		$detail->amount_case = $amount_case;
		$detail->total = $detail->price * $amount + $detail->price_case * $amount_case;
		$detail->total_tax = $detail->price_tax * $amount + $detail->price_case_tax * $amount_case;

		return $detail->save() !== false;
	}

	private function get_new_order_details($data, $member_id){

		$new_order_details = array();

		if(!isset($data['new_amount'])){

			return $new_order_details;
		}

		$amounts = $data['new_amount'];

		foreach ($amounts as $id => $amount) {
			$item = $this->get_item($id, $member_id);

			if(!empty($item)){
				$amount_case = 0;
				if (isset($data['new_amount_case'][$id])){
					$amount_case = $data['new_amount_case'][$id];
				}

				$new_order_details[$id] = $this->create_order_detail($item, $amount, $amount_case);
			}
		}

		return $new_order_details;
	}

	private function create_order_detail($item, $amount, $amount_case) {
		$values = array();
		$values['category_code'] = $item['category_code'];
		$values['category_name'] = $item['category_name'];
		$values['item_id'] = $item['id'];
		$values['item_code'] = $item['code'];
		$values['item_name'] = $item['name'];
		$values['item_unit_name'] = $item['unit_name'];
		$values['item_unit_name_case'] = $item['unit_name_case'];
		$values['item_size'] = $item['size'];
		$values['item_size_case'] = $item['size_case'];
		$values['item_type'] = $item['type'];
		$values['jan_code'] = $item['jan_code'];
		$price = $this->value($item, 'price', 'assign_price', 'group_price');
		$price_case = $this->value($item, 'price_case', 'assign_price_case', 'group_price_case');
		$values['price'] = $price;
		$values['price_tax'] =\Common_Util::add_tax($values['price']);
		$values['amount'] = $amount;
		$values['price_case'] = $price_case;
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = $amount_case;
		$values['total'] = $values['price'] * $values['item_size'] * $values['amount'] + $values['price_case'] * $values['item_size_case'] * $values['amount_case'];
		$values['total_tax'] = \Common_Util::add_tax($values['price'] * $values['item_size'] * $values['amount']) + \Common_Util::add_tax($values['price_case'] * $values['item_size_case'] * $values['amount_case']);

		return \Model_Order_Detail::forge($values);
	}


	/**
	 * 固定条件を付与する
	 *
	 * @param Query $query Query
	 */
	private function add_default_condition(&$query) {
		$query->where('order_download_id', '=', null);
		$query->where('cancel_flg', '=', 0);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data ) {
		$this->add_default_condition($query);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}

		// 発注日付指定(From)
		$order_start_date = \Common_Util::get_date($data, 'order_start_year', 'order_start_month', 'order_start_day');
		if (!empty($order_start_date)) {
			$query->where('order_datetime', '>=' , $order_start_date);
		}

		// 発注日付指定(To)
		$order_end_date = \Common_Util::get_date($data, 'order_end_year', 'order_end_month', 'order_end_day');
		if (!empty($order_end_date)) {
			$query->where('order_datetime', '<', date('Y-m-d', strtotime($order_end_date . ' +1 day')));
		}

		// 納品日付指定(From)
		$delivery_start_date = \Common_Util::get_date($data, 'delivery_start_year', 'delivery_start_month', 'delivery_start_day');
		if (!empty($delivery_start_date)) {
			$query->where('delivery_date', '>=' , $delivery_start_date);
		}

		// 納品日付指定(To)
		$delivery_end_date = \Common_Util::get_date($data, 'delivery_end_year', 'delivery_end_month', 'delivery_end_day');
		if (!empty($delivery_end_date)) {
			$query->where('delivery_date', '<', date('Y-m-d', strtotime($delivery_end_date . ' +1 day')));
		}

		// 備考
		$comment = Arr::get($data, 'comment');
		if (!is_null($comment) && trim($comment) == '1') {
			$query->where('comment', '!=', NULL);
			$query->where('comment', '!=', '');
		}
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