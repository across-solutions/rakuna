<?php
use Fuel\Core\Config;
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\Arr;
use Auth\Auth;
/**
 * 発注者CSVアップロードクラス
 */
class Upload_Csv_Member extends Upload_Csv_Base {

	/**
	 * 発注者グループコードリスト
	 */
	private $member_groups = array();

	/**
	 * 発注者コードリスト
	 */
	private $members = array();

	/**
	 * 営業担当者コードリスト
	 */
	private $sales_representatives = array();

	/**
	 * 配達曜日コードリスト
	 */
	private $delivery_weeks = array();

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * CSV設定の制御コードの有無
	 * 制御コードが未設定の場合はアップロード処理をしない
	 */
	private $has_control_code = true;

	/**
	 * 更新レコードかどうか
	 */
	private $is_update_record = false;

	/**
	 * 必須ではないCSV項目の初期値
	 */
	protected $norequire_columns_default = array(
			array('member_corporation', 'corporation', ''),
			array('member_store', 'store', ''),
			array('member_address', 'address', ''),
			array('member_tel', 'tel', ''),
			array('member_fax', 'fax', ''),
			array('email', 'email', ''),
			array('username', 'username', ''),
			array('password', 'password', ''),
	);

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->member_groups = $this->list_member_group_code();
		$this->sales_representatives = $this->list_sales_person_code();
		$this->delivery_weeks = $this->list_delivery_week_code();
		$this->members = $this->list_member_code();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.member');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		if (!isset($data['control_code'])) {
			if ($this->has_control_code) {
				parent::add_error('発注者CSV設定に制御コードが設定されていないためアップロードできません');
				$this->has_control_code = false;
			}
			return false;
		}
		$control_code = $data['control_code'];
		if (!$this->validate_control_code($control_code, $num)) {
			return false;
		}
		$this->is_update_record = array_key_exists($data['member_code'], $this->members);

		if ($control_code == '0') {
			foreach ($data as $key => $value) {
				switch ($key) {
					case 'member_code':
						$this->validate_member_code($value, $num);
						break;
					case 'member_name':
						$this->validate_member_name($value, $num, $data['member_name2']);
						break;
					case 'group_code':
						$this->validate_group_code($value, $num);
						break;
					case 'sales_person_code':
						$this->validate_sales_person_code($value, $num);
						break;
					//case 'member_corporation':
					//	$this->validate_member_corporation($value, $num);
					//	break;
					//case 'member_store':
					//	$this->validate_member_store($value, $num);
					//	break;
					case 'member_zip':
						$this->validate_member_zip($value, $num);
						break;
					case 'member_address1':
						$this->validate_member_address1($value, $num);
						break;
					case 'member_address2':
						$this->validate_member_address2($value, $num);
						break;
					//case 'member_address3':
					//	$this->validate_member_address3($value, $num);
					//	break;
					case 'member_tel':
						$this->validate_member_tel($value, $num);
						break;
					//case 'member_fax':
					//	$this->validate_member_fax($value, $num);
					//	break;
					case 'delivery_week_code':
						$this->validate_delivery_week_code($value, $num);
						break;
					//case 'username':
					//	$this->validate_member_username($value, $num);
					//	break;
					//case 'password':
					//	$this->validate_member_password($value, $num);
					//	break;
					//case 'member_email':
					//	$this->validate_member_email($value, $num);
					//	break;
					//case 'sub_email1':
					//	$this->validate_member_sub_email($value, $num);
					//	break;
					//case 'sub_email2':
					//	$this->validate_member_sub_email($value, $num);
					//	break;
					//case 'sub_email3':
					//	$this->validate_member_sub_email($value, $num);
					//	break;
					//case 'sub_email4':
					//	$this->validate_member_sub_email($value, $num);
					//	break;
					//case 'sub_email5':
					//	$this->validate_member_sub_email($value, $num);
					//	break;
					case 'control_code':
						$this->validate_control_code($value, $num);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['member_code'], $this->members)) {
				// 発注者コードが存在しない場合は無視する
				return false;
			}
		}

		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_line()
	 */
	protected function save_line($data) {
		$control_code = $data['control_code'];
		if ($control_code == '0') {
			if (array_key_exists($data['member_code'], $this->members)) {
				if (!$this->update_member($data)) {
					return false;
				}
			} else {
				if (!$this->insert_member($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (!$this->remove_member($data['member_code'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_after()
	 */
	protected function save_after() {
		if($this->check_username_unique()){
			return true;
		}else{
			Session::set_flash('validate_upload_errors', $this->get_errors());
			return false;
		}
	}

	/**
	 * @see ログインIDに重複がないかチェックする
	 */
	protected function check_username_unique(){

		$duplicate_usernames = DB::select('username', DB::expr('COUNT(*) as count'))
			->from('members')
			->where('del_flg', UNDELETED)
			->group_by('username')
			->having('count', '>', 1)->execute()->as_array();

		if(count($duplicate_usernames) == 0){
			return true;

		} else {
			$data = $this->get_data();
			$num = 1;
			foreach($data as $line){
				foreach($duplicate_usernames as $duplicate){
					$value = $line['username'];
					if($duplicate['username'] == $value){
						parent::set_error($num, 'ログインIDが重複しています[' .$value .']');
					}
				}
				$num += 1;
			}
			return false;
		}
	}

	/**
	 * @see Upload_Csv_Base::get_unique_key()
	 */
	protected function get_unique_key($data) {
		return $data['member_code'];
	}

	/**
	 * 制御フラグバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_control_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '制御コードを入力してください');
			return false;
		}
		if ($value != '0' && $value != '1') {
			parent::set_error($num, '制御コードは0、または、1で入力してください[' .$value .']');
			return false;
		}
		return true;
	}

	/**
	 * 発注者コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '発注者コードを入力してください');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '発注者コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 20) {
			parent::set_error($num, '発注者コードは20文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 発注者名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $member_name2 発注者名2
	 */
	private function validate_member_name($value, $num, $member_name2) {
		$member_name = $value . $member_name2;
		if ($member_name == '') {
			parent::set_error($num, '発注者名を入力してください');
			return false;
		}

		$max_length = 100;
		if (Str::length($member_name) > $max_length ) {
			parent::set_error($num, '発注者名は'.$max_length.'文字以下で入力してください[' . $member_name . ']');
			return false;
		}
		return true;
	}

	/**
	 * 発注者グループコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_group_code($value, $num) {
		if ($value == '' || $value == '000000') {
			return true;
		}

		if ($value != '' && !array_key_exists($value, $this->member_groups)) {
			parent::set_error($num, 'グループが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 営業担当者コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_sales_person_code($value, $num) {
		if ($value == '' || $value == '000000') {
			return true;
		}

		if (!array_key_exists($value, $this->sales_representatives)) {
			parent::set_error($num, '営業担当者コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 企業名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_corporation($value, $num) {

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '企業名は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 店舗名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_store($value, $num) {

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '店舗名は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 郵便番号バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_zip($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}
		$max_length = 10;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '郵便番号は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 住所1バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_address1($value, $num) {
		$max_length = 50;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '住所1は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 住所2バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_address2($value, $num) {
		$max_length = 50;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '住所2は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 住所3バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_address3($value, $num) {

		$max_length = 50;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '住所は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 電話番号バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_tel($value, $num) {

		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 15;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '電話番号は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * 電話番号、FAXバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_fax($value, $num) {

		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 14;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, 'FAXは'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * 配達曜日コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_week_code($value, $num) {
		if ($value == '' || $value == '000000') {
			return true;
		}

		if (!array_key_exists($value, $this->delivery_weeks)) {
			parent::set_error($num, '配達曜日コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * ログインIDバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_username($value, $num) {

		if (is_null($value) || $value == '') {
			if ($this->is_update_record) {
				parent::set_error($num, 'ログインIDを入力してください');
				return false;
			} else {
			return true;
		}
		}

		$min_length = 5;
		$max_length = 10;
		if ( Str::length($value) < $min_length || Str::length($value) > $max_length ) {
			parent::set_error($num, 'ログインIDは'.$min_length.'文字以上'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, 'ログインIDは半角英数字で入力してください[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * パスワードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_password($value, $num) {

		if (is_null($value) || $value == '') {
			if ($this->is_update_record) {
				parent::set_error($num, 'パスワードを入力してください');
				return false;
			} else {
			return true;
		}
		}

		$min_length = 5;
		$max_length = 10;
		if ( Str::length($value) < $min_length || Str::length($value) > $max_length ) {
			parent::set_error($num, 'パスワードは'.$min_length.'文字以上'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, 'パスワードは半角英数字で入力してください[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * メールアドレスバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_email($value, $num) {

		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 255;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, 'メールアドレスは'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		if (!Common_Validation::_validation_simple_email($value)) {
			parent::set_error($num, 'メールアドレスが不正です[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * サブアドレスバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_sub_email($value, $num) {

		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 255;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, 'サブアドレスは'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}

		if (!Common_Validation::_validation_simple_email($value)) {
			parent::set_error($num, 'サブアドレスが不正です[' . $value . ']');
			return false;
		}

		return true;
	}

	/**
	 * 発注者グループコードリストを取得する
	 */
	private function list_member_group_code(){
		return DB::select('code', 'id')
			->from('member_groups')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 営業担当アカウントコードドリストを取得する
	 */
	private function list_sales_person_code() {
		return DB::select('sales_person_code', 'id')
			->from('sales_representatives')
			->where('del_flg', '=', UNDELETED)
			->order_by(DB::expr('Cast(sales_person_code AS SIGNED)'), 'ASC')
			->execute()
			->as_array('sales_person_code', 'id');
	}

	/**
	 * 配達曜日コードドリストを取得する
	 */
	private function list_delivery_week_code() {
		return DB::select('code', 'id')
			->from('delivery_weeks')
			->where('del_flg', '=', UNDELETED)
			->order_by(DB::expr('Cast(code AS SIGNED)'), 'ASC')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 発注者コードリストを取得する
	 */
	private function list_member_code() {
		$members = DB::select('id', 'member_group_id', 'sales_person_code', 'code', 'name', 'corporation', 'store',
								'zip', 'address1', 'address2', 'address3', 'tel', 'fax', 'delivery_week_code',
								'email', 'sub_email', 'username', 'password' )
				->from('members')
				->where('del_flg', UNDELETED)
				->order_by('code', 'asc')
				->execute();

		$list = array();
		foreach ($members as $member) {
			$list[$member['code']] = $member;
		}
		return $list;
	}

	/**
	 * 発注者を登録する
	 *
	 * @param array $data データ
	 */
	private function insert_member($data) {
		//parent::set_norequire_columns($data);
		/*
		if (isset($data['group_code'])) {
			$member_group_id = $data['group_code'] == '' ? null : $this->member_groups[$data['group_code']];
		} else {
			$member_group_id = null;
		}
		*/

		if ($data['sales_person_code'] == '' || $data['sales_person_code'] == '000000') {
			$data['sales_person_code'] = null;
		}

		if ($data['delivery_week_code'] == '' || $data['delivery_week_code'] == '000000') {
			$data['delivery_week_code'] = null;
		}

		if ($data['group_code'] == '' || $data['group_code'] == '000000') {
			$member_group_id = null;
		} else {
			$member_group_id = $this->member_groups[$data['group_code']];
		}
/*
		for ($i = 1; $i <= 5; $i++) {
			if (!isset($data['sub_email'.$i])) {
				$data['sub_email'.$i] = '';
			}
		}
*/
		$qr_key = \Common_Util::random_string(RANDOM_QR_KEY_NUM);
		$username = $this->create_username();
		$password = $this->create_password();

		$qr = \Common_Qr::forge();
		$qr->output(QR_IMAGE_PATH, $qr_key, $this->create_mail_auth_message($qr_key));

		$member_name = $data['member_name'] . $data['member_name2'];

		$values = array();
		$values['code'] = $data['member_code'];
		$values['name'] = $member_name;
		$values['member_group_id'] = $member_group_id;
		$values['sales_person_code'] = $data['sales_person_code'];
		//$values['corporation'] = $data['member_corporation'];
		//$values['store'] = $data['member_store'];
		$values['zip'] = $data['member_zip'];
		$values['address1'] = $data['member_address1'];
		$values['address2'] = $data['member_address2'];
		//$values['address3'] = $data['member_address3'];
		$values['tel'] = $data['member_tel'];
		//$values['fax'] = $data['member_fax'];
		$values['delivery_week_code'] = $data['delivery_week_code'];

		//$values['email'] = $data['email'];
		//$values['sub_email'] = implode(',', array( $data['sub_email1'], $data['sub_email2'], $data['sub_email3'], $data['sub_email4'], $data['sub_email5'] ) );

		$values['username'] = $username;
		$values['password'] = $password;
		$values['id_mail_sent_flg'] = 0;
		$values['qr_key'] = $qr_key;
		$values['status'] = Config::get('define.member_status.enable');

		$values['search_field'] = Common_Util::mb_converts($data, array('member_code', $member_name));
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('members')->set($values)->execute();
	}

	/**
	 * 発注者を更新する
	 *
	 * @param array $data データ
	 */
	private function update_member($data) {
		$member = $this->members[$data['member_code']];
		//parent::set_norequire_columns($data, $member);
		/*
		if (isset($data['group_code'])) {
			$member_group_id = $data['group_code'] == '' ? null : $this->member_groups[$data['group_code']];
		} else {
			$member_group_id = $member['member_group_id'];
		}
		*/

		if ($data['sales_person_code'] == '' || $data['sales_person_code'] == '000000') {
			$data['sales_person_code'] = null;
		}

		if ($data['delivery_week_code'] == '' || $data['delivery_week_code'] == '000000') {
			$data['delivery_week_code'] = null;
		}

		if ($data['group_code'] == '' || $data['group_code'] == '000000') {
			$member_group_id = null;
		} else {
			$member_group_id = $this->member_groups[$data['group_code']];
		}

		//サブメールアドレス列を分割
/*
		$sub_email_default = array('','','','','');
		$member_sub_email_divided = explode(',', $member['sub_email']) + $sub_email_default;

		for ($i = 1; $i <= 5; $i++) {
			if (!isset($data['sub_email'.$i])) {
				$data['sub_email'.$i] = $member_sub_email_divided[$i - 1];
			}
		}
*/

		$member_name = $data['member_name'] . $data['member_name2'];

		if ( $member['code'] == $data['member_code']
				&& $member['name'] == $member_name
			 	&& $member['member_group_id'] == $member_group_id
				&& $member['sales_person_code'] == $data['sales_person_code']
				//&& $member['corporation'] == $data['member_corporation']
				//&& $member['store'] == $data['member_store']
				&& $member['zip'] == $data['member_zip']
				&& $member['address1'] == $data['member_address1']
				&& $member['address2'] == $data['member_address2']
				//&& $member['address3'] == $data['member_address3']
				&& $member['tel'] == $data['member_tel']
				//&& $member['fax'] == $data['member_fax']
				&& $member['delivery_week_code'] == $data['delivery_week_code']) {
				//&& $member['email'] == $data['email']
				//&& $member_sub_email_divided[0] == $data['sub_email1']
				//&& $member_sub_email_divided[1] == $data['sub_email2']
				//&& $member_sub_email_divided[2] == $data['sub_email3']
				//&& $member_sub_email_divided[3] == $data['sub_email4']
				//&& $member_sub_email_divided[4] == $data['sub_email5']
				//&& $member['username'] == $data['username']
				//&& $member['password'] == $data['password']) {
			return true;
		}

		$query = DB::update('members')
			->value('name', $member_name)
			->value('member_group_id', $member_group_id)
			->value('sales_person_code', $data['sales_person_code'])
			//->value('corporation', $data['member_corporation'])
			//->value('store', $data['member_store'])
			->value('zip', $data['member_zip'])
			->value('address1', $data['member_address1'])
			->value('address2', $data['member_address2'])
			//->value('address3', $data['member_address3'])
			->value('tel', $data['member_tel'])
			//->value('fax', $data['member_fax'])
			->value('delivery_week_code', $data['delivery_week_code'])
			//->value('email', $data['email'])
			//->value('sub_email', implode(',', array( $data['sub_email1'], $data['sub_email2'], $data['sub_email3'], $data['sub_email4'], $data['sub_email5'] ) ) )
			//->value('username', $data['username'])
			//->value('password', $data['password'])
			->value('search_field', Common_Util::mb_converts($data, array('member_code', $member_name)))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $member['id']);

		return $query->execute() !== false;
	}

	/**
	 * 発注者を削除する
	 *
	 * @param string $code 発注者コード
	 */
	private function remove_member($code) {
		return DB::update('members')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('code', $code)
			->where('del_flg', UNDELETED)
			->execute();
	}

	/**
	 * ログインIDを生成する
	 */
	private function create_username() {
		$username = null;
		if (!is_null($username) && $username != '') {
			return $username;
		}

		while (true) {
			$username = \Common_Util::random_string(RANDOM_USERNAME_NUM);

			if (!\Model_Member::exists($username, 'username')) {
				return $username;
			}
		}
	}

	/**
	 * パスワードを生成する
	 */
	private function create_password() {
		$password = null;
		if (!is_null($password) && $password != '') {
			return $password;
		}

		return \Common_Util::random_string(RANDOM_PASSWORD_NUM);
	}

	/**
	 * メール認証用メッセージを生成する
	 * @param $qr_key QR認証キー
	 */
	private function create_mail_auth_message($qr_key) {
		return 'MATMSG:TO:' . MAIL_AUTH_MAIL . ';SUB:' . MAIL_AUTH_TITLE
		. ';BODY:' . $qr_key . ';;MAILTO:' . MAIL_AUTH_MAIL . 'SUBJECT:'
				. MAIL_AUTH_TITLE . 'BODY:' . $qr_key;
	}
}
