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
		\Module::load('Order');

		$query = \Model_Item::query()
			   ->related('item_categories')
			   ->where('code', '=', $item_code);

		if(\Order\Common_Assign::has_assign($member_id)){
			$query->related('item_assigns',
							array('join_type' => 'inner',
								  'join_on' => array(
									  array('member_id', '=', \DB::expr($member_id))
								  )));
		}

		$item = $query->get_one();

		return $item;
	}


	private function create_order_detail($item, $amount, $amount_case) {
		$item_categories = $item->item_categories;

		$values = array();

		if (!empty($item_categories)) {
			$values['category_code'] = $item_categories->code;
			$values['category_name'] = $item_categories->name;
		}
		$values['item_id'] = $item->id;
		$values['item_code'] = $item->code;
		$values['item_name'] = $item->name;
		$values['item_size'] = $item->size;
		$values['jan_code'] = $item->jan_code;
		$values['price'] = $item->price;
		$values['price_tax'] = \Common_Util::add_tax($values['price']);
		$values['amount'] = $amount;
		$values['price_case'] = $item->price_case;
		$values['price_case_tax'] = \Common_Util::add_tax($values['price_case']);
		$values['amount_case'] = $amount_case;
		$values['total'] = $values['price'] * $values['amount'] + $values['price_case'] * $values['amount_case'];
		$values['total_tax'] = $values['price_tax'] * $values['amount'] + $values['price_case_tax'] * $values['amount_case'];

		return \Model_Order_Detail::forge($values);
	}


}