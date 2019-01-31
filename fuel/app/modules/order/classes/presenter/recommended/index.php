<?php
namespace Order;

use Fuel\Core\DB;
/**
 * いつものグループ一覧プレゼンタクラス
 */
class Presenter_Recommended_Index extends \Presenter_Base {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();

		$this->rows = $this->get_recommend_group();
	}

	/**
	 * いつものグループを取得する
	 *
	 * @param string $item_code 商品コード
	 */
	protected function get_recommend_group() {
		$member_id = $this->get_member_id();

		$query = DB::select('recommended_groups.id', 'recommended_groups.name', DB::expr('COUNT(items.code) as count') )
			->from('recommended_groups')
			->join('recommended_group_assigns', 'INNER')
				->on('recommended_group_assigns.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_group_assigns.member_id', '=', DB::expr($member_id) )
				->and_on('recommended_group_assigns.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_items', 'LEFT')
				->on('recommended_items.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_items.del_flg', '=', DB::expr(UNDELETED))
			->join('items', 'LEFT')
				->on('items.code', '=', 'recommended_items.item_code')
				->and_on('items.hidden_flg', '=', DB::expr(UNDELETED))
				->and_on('items.del_flg', '=', DB::expr(UNDELETED));
/*
		if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'INNER')
				->on('items.code', '=', 'item_assigns.item_code')
				->and_on('item_assigns.member_id', '=', DB::expr($member_id))
				->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		}
*/
		$query->where('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->group_by('recommended_groups.id')
			->order_by('recommended_groups.id', 'asc');

		return $query->execute()->as_array();
	}
}