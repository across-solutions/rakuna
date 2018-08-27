<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * お知らせ管理一覧プレゼンタクラス
 */
class Presenter_Notice_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		$this->member_groups = \Model_Member_Group::list_select('id', 'name', array('code' => 'asc'), '');

		parent::view();
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Notice::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Notice::query();
		$query->related('member_groups');
		$this->add_condition($query, $data);
		$query->order_by('entry_datetime', 'desc')->order_by('id', 'desc');

		return $query->limit($limit)->offset($offset)->get();
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {

		// グループ
		$member_group_id = Arr::get($data, 'member_group_id');
		if (!is_null($member_group_id) && trim($member_group_id) != '') {
			$query->where('member_group_id', '=', $member_group_id);
		}

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