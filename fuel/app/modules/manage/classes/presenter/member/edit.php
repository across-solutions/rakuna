<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 発注者アカウント管理編集プレゼンタクラス
 */
class Presenter_Member_Edit extends \Presenter_Base {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {

		$this->member_groups = \Model_Member_Group::list_select('id', 'name', array('code' => 'asc'));
		
		$sub_email = Arr::get($this->data, 'sub_email');
		if (!is_array($sub_email)) {
			$this->data['sub_email'] = explode(',', $sub_email);
		}
	}
}