<?php
use Fuel\Core\DB;
/**
 * 発注者CSVダウンロードクラス
 */
class Download_Csv_Member extends Download_Csv_Base {

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.member');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('members.code' , 'member_code'),
			array('members.name' , 'member_name'),
			array('member_groups.code' , 'group_code'),
			array('members.sales_person_code' , 'sales_person_code'),
			array('members.corporation' , 'member_corporation'),
			array('members.store' , 'member_store'),
			array('members.zip' , 'member_zip'),
			array('members.address1' , 'member_address1'),
			array('members.address2' , 'member_address2'),
			array('members.address3' , 'member_address3'),
			array('members.tel' , 'member_tel'),
			array('members.fax' , 'member_fax'),
			array('members.delivery_flg_mon' , 'member_delivery_flg_mon'),
			array('members.delivery_flg_tue' , 'member_delivery_flg_tue'),
			array('members.delivery_flg_wed' , 'member_delivery_flg_wed'),
			array('members.delivery_flg_thu' , 'member_delivery_flg_thu'),
			array('members.delivery_flg_fri' , 'member_delivery_flg_fri'),
			array('members.delivery_flg_sun' , 'member_delivery_flg_sun'),
			array('members.delivery_flg_sat' , 'member_delivery_flg_sat'),
			array('members.username' , 'username'),
			array('members.password' , 'password'),
			array('members.email' , 'email'),
			array('members.sub_email' , 'sub_email')
		)
		->from('members')
		->join('member_groups', 'LEFT')
			  ->on('member_groups.id', '=', 'members.member_group_id')
			  ->and_on('member_groups.del_flg', '=', DB::expr(UNDELETED));

		$this->add_condition($query, $params);
		$query->order_by('members.code', 'asc');

		return $query->execute();
	}

	/**
	 * @see Download_Csv_Base::modifier()
	 */
	protected function modifier($counter, $data, $key) {
		if ($key == 'control_code') {
			return '0';
		}

		$sub_email = Arr::get($data, 'sub_email');
		$sub_email_divided = explode(',', $sub_email);

		if ( $key == 'sub_email1'  ){
			if ( isset($sub_email_divided[0]) && $sub_email_divided[0] !== '' ){
				return $sub_email_divided[0];
			}
			else {
				return '';
			}
		}

		if ( $key == 'sub_email2'  ){
			if ( isset($sub_email_divided[1]) && $sub_email_divided[1] !== '' ){
				return $sub_email_divided[1];
			}
			else {
				return '';
			}
		}

		if ( $key == 'sub_email3'  ){
			if ( isset($sub_email_divided[2]) && $sub_email_divided[2] !== '' ){
				return $sub_email_divided[2];
			}
			else {
				return '';
			}
		}

		if ( $key == 'sub_email4'  ){
			if ( isset($sub_email_divided[3]) && $sub_email_divided[3] !== '' ){
				return $sub_email_divided[3];
			}
			else {
				return '';
			}
		}

		if ( $key == 'sub_email5'  ){
			if ( isset($sub_email_divided[4]) && $sub_email_divided[4] !== '' ){
				return $sub_email_divided[4];
			}
			else {
				return '';
			}
		}


		return parent::modifier($counter, $data, $key);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('members.del_flg', '=', false);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('members.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}
}