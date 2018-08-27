<?php
use Fuel\Core\DB;
/**
 * いつもの商品CSVダウンロードクラス
 */
class Download_Csv_Recommended_Item extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.recommended_item');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('recommended_items.item_code', 'item_code'),
			array('recommended_items.sort_num', 'sort_num'),
			array('recommended_groups.code', 'recommended_group_code')

		)
		->from('recommended_items')
		->join('items', 'INNER')
			->on('items.code', '=', 'recommended_items.item_code')
			->and_on('items.del_flg', '=', DB::expr(UNDELETED))
		->join('recommended_groups', 'INNER')
			->on('recommended_groups.id', '=', 'recommended_items.recommended_group_id')
			->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED));

		$this->add_condition($query, $params);
		$query->order_by('recommended_groups.code');
		$query->order_by('sort_num');

		return $query->execute();
	}

	/**
	 * @see Download_Csv_Base::modifier()
	 */
	protected function modifier($counter, $data, $key) {
		if ($key == 'control_code') {
			return '0';
		}
		if ($key == 'sort_num') {
			return '';
		}
		return parent::modifier($counter, $data, $key);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $params) {
		$query->where('recommended_items.del_flg', '=', false);

		$recommended_group_code = Arr::get($params, 'recommended_group_code');
		if (!is_null($recommended_group_code) && trim($recommended_group_code) != '') {
			$query->where('recommended_groups.code', '=', $recommended_group_code);
		}

		$item_code = Arr::get($params, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('recommended_items.item_code', '=', $item_code);
		}
	}
}