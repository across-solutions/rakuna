<?php
use Fuel\Core\Config;
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\Arr;
use Auth\Auth;
/**
 * 納品先CSVアップロードクラス
 */
class Upload_Csv_Delivery extends Upload_Csv_Base {

	/**
	 * 発注者コードリスト
	 */
	private $members = array();

	/**
	 * 納品先コードリスト
	 */
	private $deliveries = array();

	/**
	 * 配達曜日コードリスト
	 */
	private $delivery_weeks = array();

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * CSV設定の状態フラグの有無
	 * 状態フラグが未設定の場合はアップロード処理をしない
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
			array('delivery_address1', 'address1', ''),
			array('delivery_tel', 'tel', ''),
			array('delivery_fax', 'fax', ''),
	);

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->members = $this->list_member_code();
		$this->delivery_weeks = $this->list_delivery_week_code();
		$this->deliveries = $this->list_delivery_code();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.delivery');
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
					case 'member_code':
						$this->validate_member_code($value, $num);
						break;
					case 'delivery_code':
						$this->validate_delivery_code($value, $num);
						break;
					case 'delivery_name':
						$this->validate_delivery_name($value, $num);
						break;
					case 'delivery_name_kana':
						$this->validate_delivery_name_kana($value, $num);
						break;
					case 'delivery_receiver_name1':
						$this->validate_delivery_receiver_name1($value, $num);
						break;
					case 'delivery_receiver_name2':
						$this->validate_delivery_receiver_name2($value, $num);
						break;
					case 'delivery_zip':
						$this->validate_delivery_zip($value, $num);
						break;
					case 'delivery_address1':
						$this->validate_delivery_address1($value, $num);
						break;
					case 'delivery_address2':
						$this->validate_delivery_address2($value, $num);
						break;
					case 'delivery_address3':
						$this->validate_delivery_address3($value, $num);
						break;
					case 'delivery_tel':
						$this->validate_delivery_tel($value, $num);
						break;
					case 'delivery_fax':
						$this->validate_delivery_fax($value, $num);
						break;
					case 'delivery_week_code':
						$this->validate_delivery_week_code($value, $num);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['member_code'] . $data['delivery_code'], $this->deliveries)) {
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
			if (array_key_exists($data['member_code'] . $data['delivery_code'], $this->deliveries)) {
				if (!$this->update_delivery($this->deliveries[$data['member_code'] . $data['delivery_code']], $data)) {
					return false;
				}
			} else {
				if (!$this->insert_delivery($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (array_key_exists($data['member_code'] . $data['delivery_code'], $this->deliveries)) {
				if (!$this->delete_delivery($this->deliveries[$data['member_code'] . $data['delivery_code']])) {
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
		return $data['member_code'] . $data['delivery_code'];
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

		if (!array_key_exists($value, $this->members)) {
			parent::set_error($num, '発注者コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 納品先コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '納品先コードを入力してください');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '納品先コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 16) {
			parent::set_error($num, '納品先コードは16文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 納品先名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_name($value, $num) {
		if ($value == '') {
			parent::set_error($num, '納品先名を入力してください');
			return false;
		}

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '納品先名は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 納入先カナ名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_name_kana($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 50;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '納入先カナ名は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 荷受け人名1バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_receiver_name1($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '荷受け人名1は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 荷受け人名2バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_receiver_name2($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$max_length = 40;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '荷受け人名2は'.$max_length.'文字以下で入力してください[' . $value . ']');
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
	private function validate_delivery_zip($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}
		$max_length = 8;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '郵便番号は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!Common_Validation::_validation_numhyphen($value)) {
			parent::set_error($num, '郵便番号は数字、または、ハイフンで入力してください[' . $value . ']');
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
	private function validate_delivery_address1($value, $num) {
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
	private function validate_delivery_address2($value, $num) {
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
	private function validate_delivery_address3($value, $num) {
		$max_length = 50;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '住所3は'.$max_length.'文字以下で入力してください[' . $value . ']');
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
	private function validate_delivery_tel($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}
		$max_length = 14;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, '電話番号は'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!Common_Validation::_validation_numhyphen($value)) {
			parent::set_error($num, '電話番号は数字、または、ハイフンで入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * FAXバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_fax($value, $num) {
		if (is_null($value) || $value == '') {
			return true;
		}
		$max_length = 14;
		if (Str::length($value) > $max_length ) {
			parent::set_error($num, 'FAXは'.$max_length.'文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!Common_Validation::_validation_numhyphen($value)) {
			parent::set_error($num, 'FAXは数字、または、ハイフンで入力してください[' . $value . ']');
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
		if ($value == '') {
			return true;
		}

		if (!array_key_exists($value, $this->delivery_weeks)) {
			parent::set_error($num, '配達曜日コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 発注者コードリストを取得する
	 */
	private function list_member_code() {
		return DB::select('code', 'id')
			->from('members')
			->where('del_flg', '=', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 納品先コードリストを取得する
	 */
	private function list_delivery_code() {
		$deliveries = DB::select('id', 'member_code', 'code', 'name', 'name_kana', 'receiver_name1', 'receiver_name2',
								'zip', 'address1', 'address2', 'address3', 'tel', 'fax', 'delivery_week_code')
				->from('deliveries')
				->where('del_flg', UNDELETED)
				->order_by('member_code', 'asc')
				->execute();

		$list = array();
		foreach ($deliveries as $delivery) {
			$list[$delivery['member_code'] . $delivery['code']] = $delivery;
		}
		return $list;
	}

	/**
	 * 納品先を登録する
	 *
	 * @param array $data データ
	 */
	private function insert_delivery($data) {
		$values = array();
		$values['member_code'] = $data['member_code'];
		$values['code'] = $data['delivery_code'];
		$values['name'] = $data['delivery_name'];
		$values['name_kana'] = $data['delivery_name_kana'];
		$values['receiver_name1'] = $data['delivery_receiver_name1'];
		$values['receiver_name2'] = $data['delivery_receiver_name2'];
		$values['zip'] = $data['delivery_zip'];
		$values['address1'] = $data['delivery_address1'];
		$values['address2'] = $data['delivery_address2'];
		$values['address3'] = $data['delivery_address3'];
		$values['tel'] = $data['delivery_tel'];
		$values['fax'] = $data['delivery_fax'];
		$values['delivery_week_code'] = $data['delivery_week_code'];
		$values['search_field'] = Common_Util::mb_converts($data, array('member_code', 'delivery_code', 'delivery_name',
										'delivery_name_kana', 'delivery_receiver_name1', 'delivery_receiver_name2'));
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('deliveries')->set($values)->execute();
	}

	/**
	 * 納品先を更新する
	 *
	 * @param array $member 元データ
	 * @param array $data データ
	 */
	private function update_delivery($delivery, $data) {
		if ($delivery['member_code'] == $data['member_code']
				&& $delivery['code'] == $data['delivery_code']
				&& $delivery['name'] == $data['delivery_name']
				&& $delivery['name_kana'] == $data['delivery_name_kana']
				&& $delivery['receiver_name1'] == $data['delivery_receiver_name1']
				&& $delivery['receiver_name2'] == $data['delivery_receiver_name2']
				&& $delivery['zip'] == $data['delivery_zip']
				&& $delivery['address1'] == $data['delivery_address1']
				&& $delivery['address2'] == $data['delivery_address2']
				&& $delivery['address3'] == $data['delivery_address3']
				&& $delivery['tel'] == $data['delivery_tel']
				&& $delivery['fax'] == $data['delivery_fax']
				&& $delivery['delivery_week_code'] == $data['delivery_week_code']) {
			return true;
		}

		$query = DB::update('deliveries')
			->value('member_code', $data['member_code'])
			->value('name', $data['delivery_name'])
			->value('name_kana', $data['delivery_name_kana'])
			->value('receiver_name1', $data['delivery_receiver_name1'])
			->value('receiver_name2', $data['delivery_receiver_name2'])
			->value('zip', $data['delivery_zip'])
			->value('address1', $data['delivery_address1'])
			->value('address2', $data['delivery_address2'])
			->value('address3', $data['delivery_address3'])
			->value('tel', $data['delivery_tel'])
			->value('fax', $data['delivery_fax'])
			->value('delivery_week_code', $data['delivery_week_code'])
			->value('search_field', Common_Util::mb_converts($data, array('member_code', 'delivery_code', 'delivery_name',
										'delivery_name_kana', 'delivery_receiver_name1', 'delivery_receiver_name2')))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $delivery['id']);

		return $query->execute() !== false;
	}

	/**
	 * 納品先を削除する
	 *
	 * @param array $member 元データ
	 */
	private function delete_delivery($delivery) {
		$query = DB::update('deliveries')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('code', '=', $delivery['code'])
			->where('member_code', '=', $delivery['member_code'])
			->where('del_flg', '=', UNDELETED);

		return $query->execute() !== false;
	}
}
