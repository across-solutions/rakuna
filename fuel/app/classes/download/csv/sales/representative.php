<?php
use Fuel\Core\DB;
/**
 * 営業担当アカウントCSVダウンロードクラス
 */
class Download_Csv_Sales_Representative extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.sales_representative');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
				array('sales_representatives.sales_person_code' , 'sales_person_code'),
				array('sales_representatives.sales_person_name' , 'sales_person_name'),
				array('sales_representatives.username' , 'username'),
				array('sales_representatives.password' , 'password')
			)
			->from('sales_representatives');

		$this->add_condition($query, $params);
		$query->order_by(DB::expr('Cast(sales_representatives.sales_person_code AS SIGNED)'), 'ASC');

		return $query->execute();
	}

	/**
	 * @see Download_Csv_Base::modifier()
	 */
	protected function modifier($counter, $data, $key) {
		return parent::modifier($counter, $data, $key);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('sales_representatives.del_flg', '=', false);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('sales_representatives.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}