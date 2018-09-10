<?php
use Fuel\Core\Config;
use Fuel\Core\DB;
use Auth\Auth;
/**
 * 営業担当アカウントCSVアップロードクラス
 */
class Upload_Csv_Sales_Representative extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * 更新レコードかどうか
	 */
	private $is_update_record = false;

	/**
	 * ログインIDリスト
	 */
	private $usernames = array();

	/**
	 * ログインIDチェック
	 */
	private $usernames_check = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->sales_representatives = $this->list_sales_person_code();
		$this->usernames = $this->list_username();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.sales_representative');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		$control_code = $data['control_code'];
		if (!$this->validate_control_code($control_code, $num)) {
			return false;
		}

		if ($control_code == '0') {
			foreach ($data as $key => $value) {
				switch ($key) {
					case 'sales_person_code':
						$this->validate_sales_person_code($value, $num);
						break;
					case 'sales_person_name':
						$this->validate_sales_person_name($value, $num);
						break;
					case 'username':
						$this->validate_username($value, $num, $data['sales_person_code']);
						break;
					case 'password':
						$this->validate_password($value, $num);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['sales_person_code'], $this->sales_representatives)) {
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
			if (array_key_exists($data['sales_person_code'], $this->sales_representatives)) {
				if (!$this->update_sales_representative($this->sales_representatives[$data['sales_person_code']], $data)) {
					return false;
				}
			} else {
				if (!$this->insert_sales_representative($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (array_key_exists($data['sales_person_code'], $this->sales_representatives)) {
				if (!$this->delete_sales_representative($this->sales_representatives[$data['sales_person_code']])) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @see Upload_Csv_Base::get_unique_key()
	 */
	protected function get_unique_key($data) {
		return $data['sales_person_code'];
	}

	/**
	 * 状態フラグバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_control_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '状態フラグを入力してください');
			return false;
		}
		if ($value != '0' && $value != '1') {
			parent::set_error($num, '状態フラグは0、または、1で入力してください[' .$value .']');
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
		if ($value == '') {
			parent::set_error($num, '営業担当者コードを入力してください');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '営業担当者コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 20) {
			parent::set_error($num, '営業担当者コードは20文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 営業担当者名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_sales_person_name($value, $num) {
		if ($value == '') {
			parent::set_error($num, '営業担当者名を入力してください');
			return false;
		}

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '営業担当者名は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * ログインIDバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $sales_person_code 営業担当者コード
	 */
	private function validate_username($value, $num, $sales_person_code) {
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

		if (array_key_exists($value, $this->usernames_check)) {
			parent::set_error($num, 'ログインIDが重複しています[' . $value . ']');
			return false;
		}
		$this->usernames_check[$value] = $value;

		if (isset($this->usernames[$value])) {
			$code = $this->usernames[$value];
			if ($code != $sales_person_code) {
				parent::set_error($num, 'ログインIDはすでに登録されています[' . $value . ']');
				return false;
			}
		}

		return true;
	}

	/**
	 * パスワードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_password($value, $num) {
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
	 * 営業担当アカウントコードドリストを取得する
	 */
	private function list_sales_person_code() {
		$results = DB::select('id', 'sales_person_code', 'sales_person_name', 'username', 'password')
			->from('sales_representatives')
			->where('del_flg', '=', UNDELETED)
			->order_by(DB::expr('Cast(sales_representatives.sales_person_code AS SIGNED)'), 'ASC')
			->execute();

		$list = array();
		foreach ($results as $result) {
			$list[$result['sales_person_code']] = $result;
		}

		return $list;
	}

	/**
	 * ログインIDリストを取得する
	 */
	private function list_username() {
		return DB::select('username', 'sales_person_code')
				->from('sales_representatives')
				->where('del_flg', UNDELETED)
				->order_by(DB::expr('Cast(sales_representatives.sales_person_code AS SIGNED)'), 'ASC')
				->execute()
				->as_array('username', 'sales_person_code');
	}

	/**
	 * ログインIDを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_username($data) {
		$username = $data['username'];
		if (!is_null($username) && $username != '') {
			return $username;
		}

		while (true) {
			$username = \Common_Util::random_string(RANDOM_USERNAME_NUM);

			if (!\Model_Sales_Representative::exists($username, 'username')) {
				return $username;
			}
		}
	}

	/**
	 * パスワードを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_password($data) {
		$password = $data['password'];
		if (!is_null($password) && $password != '') {
			return $password;
		}

		return \Common_Util::random_string(RANDOM_PASSWORD_NUM);
	}

	/**
	 * 営業担当アカウントを登録する
	 *
	 * @param array $data データ
	 */
	private function insert_sales_representative($data) {
		$username = $this->create_username($data);
		$password = $this->create_password($data);

		$values = array();
		$values['sales_person_code'] = $data['sales_person_code'];
		$values['sales_person_name'] = $data['sales_person_name'];
		$values['username'] = $username;
		$values['password'] = $password;
		$values['status'] = Config::get('define.sales_status.enable');
		$values['search_field'] = Common_Util::mb_converts($data, array('sales_person_code', 'sales_person_name', 'username'));
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('sales_representatives')->set($values)->execute();
	}

	/**
	 * 営業担当アカウントを更新する
	 *
	 * @param array $sales_representative 元データ
	 * @param array $data データ
	 */
	private function update_sales_representative($sales_representative, $data) {
		if ($sales_representative['sales_person_code'] == $data['sales_person_code'] &&
			$sales_representative['sales_person_name'] == $data['sales_person_name'] &&
			$sales_representative['username'] == $data['username'] &&
			$sales_representative['password'] == $data['password']) {
			return true;
		}

		$query = DB::update('sales_representatives')
			->value('sales_person_code', $data['sales_person_code'])
			->value('sales_person_name', $data['sales_person_name'])
			->value('username', $data['username'])
			->value('password', $data['password'])
			->value('search_field', Common_Util::mb_converts($data, array('sales_person_code', 'sales_person_name', 'username')))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('sales_person_code', '=', $data['sales_person_code'])
			->where('del_flg', '=', UNDELETED);

		return $query->execute() !== false;
	}

	/**
	 * 営業担当アカウントを削除する
	 *
	 * @param array $warehouse 元データ
	 */
	private function delete_sales_representative($sales_representative) {
		$query = DB::update('sales_representatives')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('sales_person_code', '=', $sales_representative['sales_person_code'])
			->where('del_flg', '=', UNDELETED);

		return $query->execute() !== false;
	}
}