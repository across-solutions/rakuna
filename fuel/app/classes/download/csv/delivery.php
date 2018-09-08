<?php
use Fuel\Core\DB;
/**
 * 納品先CSVダウンロードクラス
 */
class Download_Csv_Delivery extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.delivery');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('deliveries.member_code' , 'member_code'),
			array('deliveries.code' , 'delivery_code'),
			array('deliveries.name' , 'delivery_name'),
			array('deliveries.name_kana' , 'delivery_name_kana'),
			array('deliveries.receiver_name1' , 'delivery_receiver_name1'),
			array('deliveries.receiver_name2' , 'delivery_receiver_name2'),
			array('deliveries.zip' , 'delivery_zip'),
			array('deliveries.address1' , 'delivery_address1'),
			array('deliveries.address2' , 'delivery_address2'),
			array('deliveries.address3' , 'delivery_address3'),
			array('deliveries.tel' , 'delivery_tel'),
			array('deliveries.fax' , 'delivery_fax'),
			array('deliveries.delivery_flg_mon' , 'delivery_delivery_flg_mon'),
			array('deliveries.delivery_flg_tue' , 'delivery_delivery_flg_tue'),
			array('deliveries.delivery_flg_wed' , 'delivery_delivery_flg_wed'),
			array('deliveries.delivery_flg_thu' , 'delivery_delivery_flg_thu'),
			array('deliveries.delivery_flg_fri' , 'delivery_delivery_flg_fri'),
			array('deliveries.delivery_flg_sun' , 'delivery_delivery_flg_sun'),
			array('deliveries.delivery_flg_sat' , 'delivery_delivery_flg_sat')
		)
		->from('deliveries');

		$this->add_condition($query, $params);
		$query->order_by('deliveries.member_code', 'asc');

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
		$query->where('deliveries.del_flg', '=', false);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('deliveries.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}