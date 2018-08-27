<?php
namespace Order;

use Fuel\Core\DB;
/**
 * お知らせ取得クラス
 */
class Common_Notice {

	/**
	 * 検索条件を付与する
	 *
	 * @param Model_Member $member
	 * @param Query $query Query
	 */

	public static function add_condition($member, $query) {

		$query->where_open()
			->where('notices.member_group_id', '=', $member->member_group_id)
			->or_where('notices.member_group_id', '=', DB::expr(ALL_MEMBER_GROUP))
			->where_close()
			->where('notices.del_flg', '=', DB::expr(UNDELETED));

		if (Common_Assign::has_assign($member->id)) {
			$query->where_open();
			$query->where('notices.item_code', '=', null);
			$query->or_where('notices.item_code', '=', '');
			$query->or_where('notices.item_code', 'in',
							 DB::expr('(SELECT item_code FROM item_assigns WHERE member_id = ' . $member->id . ' AND item_assigns.del_flg = ' . UNDELETED . ' )'));
			$query->where_close();
		}

		return $query;
	}
}