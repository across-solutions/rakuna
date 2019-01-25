<?php
use Fuel\Core\DB;
/**
 * グループ割当CSVダウンロードクラス
 */
class Download_Csv_Group_Assign extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.group_assign');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(array('group_assigns.item_code', 'item_code'), array('group_assigns.member_group_code', 'member_group_code'),
				array('group_assigns.price', 'group_price'), array('group_assigns.price_case', 'group_price_case'))
			->from('group_assigns')
			->join('items', 'INNER')
				->on('group_assigns.item_code', '=', 'items.code')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->join('member_groups', 'INNER')
				->on('group_assigns.member_group_code', '=', 'member_groups.code')
				->on('member_groups.del_flg', '=', DB::escape(UNDELETED))
			->order_by('member_groups.code', 'asc')
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

		if ($key == 'member_code') {
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
		$query->where('group_assigns.del_flg', '=', UNDELETED);

		$member_group_code = Arr::get($params, 'member_group_code');
		if (!is_null($member_group_code) && trim($member_group_code) != '') {
			$query->where('group_assigns.member_group_code', '=', $member_group_code);
		}

		$item_code = Arr::get($params, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('group_assigns.item_code', '=', $item_code);
		}
	}
}