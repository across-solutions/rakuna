<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 発注タイプ設定一覧プレゼンタクラス
 */
class Presenter_Setting_Order_Type_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Order_Type::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Order_Type::query();
		$this->add_condition($query, Input::get());
		$query->order_by('id', 'asc');

		return $query->limit($limit)->offset($offset)->get();
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
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}