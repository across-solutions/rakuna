<?php
namespace Manage;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * 商品発注タイプ管理一覧プレゼンタクラス
 */
class Presenter_Item_Order_Type_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = DB::select(array(DB::expr('count(1)'), 'count'))
			->from('item_order_types')
			->join('items', 'INNER')
				->on('item_order_types.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('members', 'INNER')
				->on('item_order_types.member_id', '=', 'members.id')
				->on('members.del_flg', '=', DB::escape(UNDELETED))
			->join('order_types', 'INNER')
				->on('item_order_types.order_type', '=', 'order_types.id')
				->on('order_types.del_flg', '=', DB::escape(UNDELETED));
		$this->add_condition($query, $data);

		$result = $query->execute()->as_array();

		return $result[0]['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('item_order_types.id', array('members.code', 'member_code'), array('members.name', 'member_name'),
				array('items.code', 'item_code'), array('items.name', 'item_name'), array('order_types.name', 'order_type_name'))
			->from('item_order_types')
			->join('items', 'INNER')
				->on('item_order_types.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('members', 'INNER')
				->on('item_order_types.member_id', '=', 'members.id')
				->on('members.del_flg', '=', DB::escape(UNDELETED))
			->join('order_types', 'INNER')
				->on('item_order_types.order_type', '=', 'order_types.id')
				->on('order_types.del_flg', '=', DB::escape(UNDELETED))
			->order_by('members.code', 'asc')
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
		$query->where('item_order_types.del_flg', '=', UNDELETED);

		$member_code = Arr::get($data, 'member_code');
		if (!is_null($member_code) && trim($member_code) != '') {
			$query->where('members.code', '=', $member_code);
		}

		$item_code = Arr::get($data, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('item_order_types.item_code', '=', $item_code);
		}
	}
}