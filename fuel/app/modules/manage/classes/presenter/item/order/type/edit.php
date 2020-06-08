<?php
namespace Manage;

use Model_Order_Type;

/**
 * 商品発注タイプ編集プレゼンタクラス
 */
class Presenter_Item_Order_Type_Edit extends \Presenter_Base {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();

		$this->member = \Model_Member::find($this->data->member_id);
		$this->order_types = Model_Order_Type::list_select('id', 'name', array('id' => 'asc'), null);
	}
}