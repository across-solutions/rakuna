<?php
namespace Manage;
/**
 * 割当商品編集プレゼンタクラス
 */
class Presenter_Item_Assign_Edit extends \Presenter_Base {
	
	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();
		
		$this->member = \Model_Member::find($this->data->member_id);
	}
}