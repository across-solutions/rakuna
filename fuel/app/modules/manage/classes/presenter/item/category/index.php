<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Pagination;
use Fuel\Core\Config;

/**
 * カテゴリ管理一覧プレゼンタクラス
 */
class Presenter_Item_Category_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = DB::select(array(DB::expr('count(1)'), 'count'))->from('item_categories');
		$this->add_condition($query, $data);

		$result = $query->execute()->as_array();

		return $result[0]['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('item_categories.id', 'item_categories.code', 'item_categories.name', array(DB::expr('count(items.id)'), 'item_count'))
			->from('item_categories')
			->join('items', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED))
			->group_by('id')
			->order_by('code', 'asc')
			->limit($limit)
			->offset($offset);
		$this->add_condition($query, $data);

		return $query->execute()->as_array();
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('item_categories.del_flg', '=', DB::expr(UNDELETED));

		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('item_categories.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}