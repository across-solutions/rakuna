<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 商品管理追加プレゼンタクラス
 */
class Presenter_Recommended_Item_Add extends \Presenter_Base {
	
	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();
		
		$this->recommended_groups = \Model_Recommended_Group::list_select('code', 'name', array('code' => 'asc'), false);
	}
}