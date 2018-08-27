<?php
use Fuel\Core\DB;
/**
 * 割当CSVダウンロードクラス
 */
class Download_Csv_Recommended_Group_Assign extends Download_Csv_Base {
	
	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.recommended_group_assign');
	}
	
	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('recommended_groups.code', 'recommended_group_code'),
			array('members.code', 'member_code')
		)
		->from('recommended_group_assigns')
			->join('recommended_groups', 'INNER')
			->on('recommended_groups.id', '=', 'recommended_group_assigns.recommended_group_id')
			->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
		->join('members', 'INNER')
			->on('members.id', '=', 'recommended_group_assigns.member_id')
			->and_on('members.del_flg', '=', DB::expr(UNDELETED));
		
		$this->add_condition($query, $params);
		$query->order_by('members.code', 'recommended_group_assigns.item_code');
		
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
		$query->where('recommended_group_assigns.del_flg', '=', false);

		$member_code = Arr::get($params, 'member_code');
		if (!is_null($member_code) && trim($member_code) != '') {
			$query->where('members.code', '=', $member_code);
		}
		
		$recommended_group_code = Arr::get($params, 'recommended_group_code');
		if (!is_null($recommended_group_code) && trim($recommended_group_code) != '') {
			$query->where('recommended_group_assigns.code', '=', $recommended_group_code);
		}
	}
}