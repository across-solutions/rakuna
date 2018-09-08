<?php
namespace Manage;
use Fuel\Core\Arr;
use Fuel\Core\DB;
/**
 * 営業担当者一覧プレゼンタクラス
 */
class Presenter_Sales_Representative_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Sales_Representative::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Sales_Representative::query()
			->order_by(DB::expr('Cast(sales_person_code AS SIGNED)'), 'ASC')
			->limit($limit)
			->offset($offset);
		$this->add_condition($query, $data);

		return $query->get();
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = mb_convert_kana($search_field, 'KCVa');
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}