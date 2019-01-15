<?php
namespace Manage;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * グループ割当管理一覧プレゼンタクラス
 */
class Presenter_Group_Assign_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = DB::select(array(DB::expr('count(1)'), 'count'))
			->from('group_assigns')
			->join('items', 'INNER')
				->on('group_assigns.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('member_groups', 'INNER')
				->on('group_assigns.member_group_code', '=', 'member_groups.code')
				->on('member_groups.del_flg', '=', DB::escape(UNDELETED));
		$this->add_condition($query, $data);

		$result = $query->execute()->as_array();

		return $result[0]['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('group_assigns.id', array('member_groups.code', 'member_group_code'),
				array('member_groups.name', 'member_group_name'), array('items.code', 'item_code'),
				array('items.name', 'item_name'), array('group_assigns.price', 'price'),
				array('group_assigns.price_case', 'price_case'))
			->from('group_assigns')
			->join('items', 'INNER')
				->on('group_assigns.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('member_groups', 'INNER')
				->on('group_assigns.member_group_code', '=', 'member_groups.code')
				->on('member_groups.del_flg', '=', DB::escape(UNDELETED))
			->order_by('member_groups.code', 'asc')
			->order_by('items.code', 'asc')
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
		$query->where('group_assigns.del_flg', '=', UNDELETED);

		$member_group_code = Arr::get($data, 'member_group_code');
		if (!is_null($member_group_code) && trim($member_group_code) != '') {
			$query->where('group_assigns.member_group_code', '=', $member_group_code);
		}

		$item_code = Arr::get($data, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('group_assigns.item_code', '=', $item_code);
		}
	}
}