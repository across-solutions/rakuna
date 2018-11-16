<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 商品管理一覧プレゼンタクラス
 */
class Presenter_Item_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		$this->categories = \Model_Item_Category::list_select('id', 'name', array('code' => 'asc'));

		parent::view();
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Item::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Item::query()->related('item_categories');
		$this->add_condition($query, Input::get());
		$query->order_by('code', 'asc');

		return $query->limit($limit)->offset($offset)->get();
	}

	/**
	 * @see Presenter_Pagination::modifier()
	 */
	protected function modifier(&$row) {
		$row['price'] = \Common_Util::add_tax($row['price']);
		$row['price_case'] = \Common_Util::add_tax($row['price_case']);
		$row['amount'] = is_null($row['amount']) ? 0 : $row['amount'];
		$row['amount_case'] = is_null($row['amount_case']) ? 0 : $row['amount_case'];
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		// カテゴリ
		$item_category_id = Arr::get($data, 'item_category_id');
		if (!is_null($item_category_id) && trim($item_category_id) != '') {
			$query->where('item_category_id', '=', $item_category_id);
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

		// バラ単位が空
		$empty_unit_name = Arr::get($data, 'empty_unit_name');
		if (!is_null($empty_unit_name) && trim($empty_unit_name) == '1') {
			$query->where_open();
			$query->where('unit_name', '=', NULL);
			$query->or_where('unit_name', '=', '');
			$query->where_close();
		}

		// ケース単位が空
		$empty_unit_name_case = Arr::get($data, 'empty_unit_name_case');
		if (!is_null($empty_unit_name_case) && trim($empty_unit_name_case) == '1') {
			$query->where_open();
			$query->where('unit_name_case', '=', NULL);
			$query->or_where('unit_name_case', '=', '');
			$query->where_close();
		}
	}
}