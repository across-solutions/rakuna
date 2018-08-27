<?php
namespace Manage;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * いつものグループ割当管理一覧プレゼンタクラス
 */
class Presenter_Recommended_Group_Assign_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = DB::select(array(DB::expr('count(1)'), 'count'))
			->from('recommended_group_assigns')
			->join('recommended_groups', 'INNER')
				->on('recommended_group_assigns.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->join('members', 'INNER')
				->on('recommended_group_assigns.member_id', '=', 'members.id')
				->and_on('members.del_flg', '=', DB::expr(UNDELETED));
		$this->add_condition($query, $data);

		$result = $query->execute()->as_array();

		return $result[0]['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('recommended_group_assigns.id', array('members.code', 'member_code'), array('members.name', 'member_name'),
				array('recommended_groups.code', 'recommended_group_code'), array('recommended_groups.name', 'recommended_group_name'))
			->from('recommended_group_assigns')
			->join('recommended_groups', 'INNER')
				->on('recommended_group_assigns.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->join('members', 'INNER')
				->on('recommended_group_assigns.member_id', '=', 'members.id')
				->and_on('members.del_flg', '=', DB::expr(UNDELETED))
			->order_by('members.code', 'asc')
			->order_by('recommended_groups.code', 'asc')
			->limit($limit)
			->offset($offset);
		$this->add_condition($query, $data);

		return $query->execute()->as_array();
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('recommended_group_assigns.del_flg', '=', DB::expr(UNDELETED));

		$member_code = Arr::get($data, 'member_code');
		if (!is_null($member_code) && trim($member_code) != '') {
			$query->where('members.code', '=', $member_code);
		}

		$recommended_group_code = Arr::get($data, 'recommended_group_code');
		if (!is_null($recommended_group_code) && trim($recommended_group_code) != '') {
			$query->where('recommended_groups.code', '=', $recommended_group_code);
		}
	}
}