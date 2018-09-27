<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 配達曜日一覧プレゼンタクラス
 */
class Presenter_Setting_Shipping_Week_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();

		$this->shipping_week = function($row) {
			return $this->shipping_week($row);
		};
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Delivery_Week::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Delivery_Week::query();
		$this->add_condition($query, $data);
		$query->order_by('code', 'asc');

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

	/**
	 * 配達曜日
	 *
	 * @param array $row 行データ
	 */
	private function shipping_week($row) {
		$week = '';

		$mon = Arr::get($row, 'delivery_flg_mon');
		if ($mon) {
			$week .= '　月';
		}
		$tue = Arr::get($row, 'delivery_flg_tue');
		if ($tue) {
			$week .= '　火';
		}
		$wed = Arr::get($row, 'delivery_flg_wed');
		if ($wed) {
			$week .= '　水';
		}
		$thu = Arr::get($row, 'delivery_flg_thu');
		if ($thu) {
			$week .= '　木';
		}
		$fri = Arr::get($row, 'delivery_flg_fri');
		if ($fri) {
			$week .= '　金';
		}
		$sat = Arr::get($row, 'delivery_flg_sat');
		if ($sat) {
			$week .= '　土';
		}
		$sun = Arr::get($row, 'delivery_flg_sun');
		if ($sun) {
			$week .= '　日';
		}

		return $week;
	}
}