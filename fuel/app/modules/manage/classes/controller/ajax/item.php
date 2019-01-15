<?php
namespace Manage;

use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\DB;
/**
 * 商品非同期コントローラクラス
 */
class Controller_Ajax_Item extends Controller_Ajax_Base {

	public function get_info_for_edit_order(){
		$item_code = \Input::get('item_code');
		$order_id = \Input::get('order_id');

		$order = \Model_Order::find($order_id);

		$item = $this->get_item($item_code, $order->member_id);

		if(empty($item)){
			$this->response_error_not_found();
			return;
		}

		$detail = $this->create_order_detail($item);
		$row_html = \Presenter::forge('base', 'view', null,
									  \View::forge("order/edit_row", array(
										  'row' => $detail,
									  )));

		return $this->response(array(
			"item" => $item,
			"html" => htmlspecialchars($row_html)
		));
	}

	private function get_item($item_code, $member_id){
		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		return DB::select('items.id', 'items.code', 'items.name',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size',
				'items.price', 'items.price_case',
				array('item_assigns.price_case', 'assign_price_case'),
				array('item_assigns.price', 'assign_price'),
				array('group_assigns.price_case', 'group_price_case'),
				array('group_assigns.price', 'group_price'),
				array('item_categories.code', 'category_code'), array('item_categories.name', 'category_name'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED))
			->join('group_assigns', 'LEFT')
				->on('group_assigns.item_code', '=', 'items.code')
				->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
				->on('group_assigns.del_flg', '=', DB::escape(UNDELETED))
			->where('items.code', '=', $item_code)
			->where('items.del_flg', '=', UNDELETED)
			->execute()
			->current();
	}

	private function create_order_detail($item) {
		$values['item_id'] = $item['id'];
		$values['item_code'] = $item['code'];
		$values['item_name'] = $item['name'];
		$values['unit_name_case'] = $item['unit_name_case'];
		$values['unit_name'] = $item['unit_name'];
		$values['size_case'] = $item['size_case'];
		$values['size'] = $item['size'];
		$values['category_code'] = $item['category_code'];
		$values['category_name'] = $item['category_name'];
		$price = $this->value($item, 'price', 'assign_price', 'group_price');
		$price_case = $this->value($item, 'price_case', 'assign_price_case', 'group_price_case');
		$values['price'] = $price * $item['size'];
		$values['price_tax'] = \Common_Util::add_tax($values['price']);
		$values['amount'] = 0;
		$values['price_case'] = $price_case * $item['size_case'];
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = 0;
		$values['total'] = 0;
		$values['total_tax'] = 0;

		return \Model_Order_Detail::forge($values);
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