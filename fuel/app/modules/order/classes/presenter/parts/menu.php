<?php
namespace Order;

use Fuel\Core\Request;
use Fuel\Core\DB;
/**
 * メニュープレゼンタクラス
 */
class Presenter_Parts_Menu extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->visible_support_search = Request::active()->controller_instance->visible_support_search;

		$this->visible_clear_carts = Request::active()->controller_instance->visible_clear_carts;

		$this->visible_cach = $this->exist_cart($this->get_member_id());
	}

	/**
	 * カート存在チェック
	 *
	 * @param int $member_id 発注者ID
	 */
	private function exist_cart($member_id) {
		$result = DB::select(array(DB::expr('COUNT(1)'), 'count'))
			->from('carts')
			->where('member_id', '=', $member_id)
			->execute()
			->current();

		return $result['count'] > 0;
	}
}