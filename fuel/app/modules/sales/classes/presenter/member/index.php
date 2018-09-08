<?php
namespace Sales;
use Fuel\Core\DB;
use Fuel\Core\Arr;
use Fuel\Core\Config;
/**
 * [代理発注]発注者選択一覧プレゼンタクラス
 */
class Presenter_Member_Index extends \Presenter_Pagination {

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
		$query = DB::select(DB::expr('COUNT(*) as count'))
			->from('members')
			->order_by('members.code', 'ASC');

		$this->add_condition($query, $data);

		$result = $query->execute()->current();

		return $result['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('members.code', 'members.name')
			->from('members')
			->order_by('members.code', 'ASC')
			->limit($limit)
			->offset($offset);

		$this->add_condition($query, $data);

		$results = $query->execute();

		return Arr::assoc_to_keyval($results, 'code', 'name');
	}

	/**
	 * @see Presenter_Pagination::per_page()
	 */
	protected function per_page() {
		return 30;
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data GETデータ
	 */
	protected function add_condition(&$query, $data) {
		$member = \Model_Sales_Representative::find($this->get_member_id());

		$query->where('members.del_flg', '=', DB::escape(UNDELETED));
		$query->where('members.sales_person_code', '=', $member->sales_person_code);

		// フリーワード
		$search_field = Arr::get($data, 'freeword');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('members.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}