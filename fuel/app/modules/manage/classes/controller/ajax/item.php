<?php
namespace Manage;

use Auth\Auth;
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

		$detail = $this->create_order_detail($item, 0, 0);
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
		return DB::select('items.id', 'items.code', 'items.name',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size',
				array(DB::expr('IFNULL(item_assigns.price_case, items.price_case)'), 'price_case'),
				array(DB::expr('IFNULL(item_assigns.price, items.price)'), 'price'),
				array('item_categories.code', 'category_code'), array('item_categories.name', 'category_name'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED))
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
		$values['price'] = $item['price'] * $item['size'];
		$values['price_tax'] = \Common_Util::add_tax($values['price']);
		$values['amount'] = 0;
		$values['price_case'] = $item['price_case'] * $item['size_case'];
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = 0;
		$values['total'] = 0;
		$values['total_tax'] = 0;

		return $values;
	}
}