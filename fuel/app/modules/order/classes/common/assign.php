<?php
namespace Order;
/**
 * 割当判定クラス
 */
class Common_Assign {
	
	private static $instance = null;
	
	private $assign = false;
	
	/**
	 * コンストラクタ
	 * 
	 * @param int $member_id 発注者アカウントID
	 */
	private function __construct($member_id) {
		$this->assign = $this->assigned($member_id);
	}
	
	/**
	 * 割当の有無を返す
	 * 
	 * @param int $member_id 発注者アカウントID
	 */
	public static function has_assign($member_id) {
		if (is_null(self::$instance)) {
			self::$instance = new self($member_id);
		}
		return self::$instance->assign;
	}

	/**
	 * 割当の有無を返す
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function assigned($member_id) {
		$count = \Model_Item_Assign::query()
			   ->where('member_id', $member_id)
			   ->related('items', array('join_type' => 'inner'))
			   ->count();

		return $count > 0;
	}
}