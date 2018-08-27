<?php
namespace Order;
use Fuel\Core\Request;
use Fuel\Core\Uri;
/**
 * カテゴリ部プレゼンタクラス
 */
class Presenter_Parts_Category extends \Presenter_Base {
	
	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->categories = Common_Category::list_category($this->get_member_id());
	}
}