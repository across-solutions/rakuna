<?php
use Fuel\Core\DB;
/**
 * 非営業日CSVダウンロードクラス
 */
class Download_Csv_Holiday extends Download_Csv_Base {
	/**
	 * @see Download_Csv_Base::get_fomat_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.holiday');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = \Model_Holiday::query();
		$this->add_condition($query, $params);
		$query->order_by('date', 'desc');

		return $query->get();
	}

	/**
	 * @see Download_Csv_Base::modifier()
	 */
	protected function modifier($counter, $data, $key) {
		if ($key == 'control_code') {
			return '0';
		}
		return parent::modifier($counter, $data, $key);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		// 年月指定
		$year_month = $this->get_date($data, 'year', 'month');
		if (!empty($year_month)) {
			$query->where('date', 'LIKE', '%' . $year_month . '%');
		}
	}

	/**
	 * 年月を取得する
	 *
	 * @param array $data 検索条件
	 * @param string $key_year 年キー
	 * @param string $key_month 月キー
	 */
	private function get_date($data, $key_year, $key_month) {
		$year = Arr::get($data, $key_year);
		$month = Arr::get($data, $key_month);
		if (empty($year) && empty($month)) {
			return '';
		}

		return $year . '-' . $month;
	}
}