<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 商品管理追加プレゼンタクラス
 */
class Presenter_Item_Add extends \Presenter_Base {
	
	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();
		
		$this->categories = \Model_Item_Category::list_select('id', 'name', array('code' => 'asc'));
	}
}