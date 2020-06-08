<?php
use Fuel\Core\DB;
/**
 * 商品発注タイプCSVダウンロードクラス
 */
class Download_Csv_Item_Order_Type extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.item_order_type');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			'item_order_types.item_code',
			array('members.code', 'member_code'),
			'item_order_types.order_type')
			->from('item_order_types')
			->join('items', 'INNER')
				->on('item_order_types.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('members', 'INNER')
				->on('item_order_types.member_id', '=', 'members.id')
				->on('members.del_flg', '=', DB::escape(UNDELETED))
			->order_by('members.code', 'asc')
			->order_by('items.code', 'asc');

		$this->add_condition($query, $params);

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
	private function add_condition(&$query, $params) {
		$query->where('item_order_types.del_flg', '=', UNDELETED);

		$member_code = Arr::get($params, 'member_code');
		if (!is_null($member_code) && trim($member_code) != '') {
			$query->where('members.code', '=', $member_code);
		}

		$item_code = Arr::get($params, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('item_order_types.item_code', '=', $item_code);
		}
	}
}