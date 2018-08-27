<?php
use Fuel\Core\DB;
/**
 * 商品CSVダウンロードクラス
 */
class Download_Csv_Item extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.item');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('item_categories.code' , 'category_code'),
			array('items.code' , 'item_code'),
			array('items.name' , 'item_name'),
			array('items.yomigana' , 'item_yomigana'),
			array('items.size' , 'item_size'),
			array('items.comment' , 'item_comment'),
			array('items.price_case' , 'item_price_case'),
			array('items.price' , 'item_price'),
			array('items.jan_code', 'jan_code')
		)
		->from('items')
		->join('item_categories', 'LEFT')
			->on('item_categories.id', '=', 'items.item_category_id')
			->and_on('item_categories.del_flg', '=', DB::expr(UNDELETED));

		$this->add_condition($query, $params);
		$query->order_by('items.code', 'asc');

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
		$query->where('items.del_flg', '=', false);

		// カテゴリ
		$item_category_id = Arr::get($data, 'item_category_id');
		if (!is_null($item_category_id) && trim($item_category_id) != '') {
			$query->where('items.item_category_id', '=', $item_category_id);
		}

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('items.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}