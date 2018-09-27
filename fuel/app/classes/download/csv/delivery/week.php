<?php
use Fuel\Core\DB;
/**
 * 配達曜日CSVダウンロードクラス
 */
class Download_Csv_Delivery_Week extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.delivery_week');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('delivery_weeks.code' , 'delivery_week_code'),
			array('delivery_weeks.delivery_flg_mon' , 'delivery_flg_mon'),
			array('delivery_weeks.delivery_flg_tue' , 'delivery_flg_tue'),
			array('delivery_weeks.delivery_flg_wed' , 'delivery_flg_wed'),
			array('delivery_weeks.delivery_flg_thu' , 'delivery_flg_thu'),
			array('delivery_weeks.delivery_flg_fri' , 'delivery_flg_fri'),
			array('delivery_weeks.delivery_flg_sun' , 'delivery_flg_sun'),
			array('delivery_weeks.delivery_flg_sat' , 'delivery_flg_sat')
		)
		->from('delivery_weeks');

		$this->add_condition($query, $params);
		$query->order_by('delivery_weeks.code', 'asc');

		return $query->execute();
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
		$query->where('delivery_weeks.del_flg', '=', false);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('delivery_weeks.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}