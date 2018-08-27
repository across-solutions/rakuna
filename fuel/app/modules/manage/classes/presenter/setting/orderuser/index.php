<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;

/**
 * 受注担当者一覧プレゼンタクラス
 */
class Presenter_Setting_Orderuser_Index extends \Presenter_Pagination {

	/**
	 * 管理者ユーザのグループ
	 */
	protected $search_not_group = 'Administrator';

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_User::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_User::query();
		$this->add_condition($query, $data);
		$query->order_by('id', 'asc');

		return $query->limit($limit)->offset($offset)->get();
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {

		$query->where('mosgroup', 'NOT LIKE', '%' . $this->search_not_group . '%'); //管理者ユーザは除外

		// フリーワード
		$name = Arr::get($data, 'name');
		if (!is_null($name) && trim($name) != '') {
			$query->where('name', 'LIKE', '%' . $name . '%');
		}
	}
}