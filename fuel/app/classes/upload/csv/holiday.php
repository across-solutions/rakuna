<?php
use Fuel\Core\Config;
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\Arr;
use Auth\Auth;
/**
 * 非営業日CSVアップロードクラス
 */
class Upload_Csv_Holiday extends Upload_Csv_Base {

	/**
	 * 非営業日リスト
	 */
	private $holidays = array();

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->holidays = \Model_Holiday::list_select('date', 'date', array('date' => 'asc'));
		$this->validation = Validation::forge();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.holiday');
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
					case 'date':
						$this->validate_date($value, $num);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['date'], $this->holidays)) {
				// 日付が存在しない場合は無視する
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
			if (!array_key_exists($data['date'], $this->holidays)) {
				if (!$this->insert_holiday($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (!$this->remove_holiday($data['date'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @see Upload_Csv_Base::get_unique_key()
	 */
	protected function get_unique_key($data) {
		return $data['date'];
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
	 * 非営業日バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_date($value, $num) {
		if ($value == '') {
			parent::set_error($num, '非営業日を入力してください');
			return false;
		}
		if (!$this->validation->_validation_valid_date($value, 'Y-m-d')) {
			parent::set_error($num, '非営業日が不正です[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 非営業日を登録する
	 *
	 * @param array $data データ
	 */
	private function insert_holiday($data) {
		$values = array();
		$values['date'] = $data['date'];
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('holidays')->set($values)->execute();
	}

	/**
	 * 非営業日を削除する
	 *
	 * @param string $date 非営業日
	 */
	private function remove_holiday($date) {
		return DB::update('holidays')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('date', $date)
			->where('del_flg', UNDELETED)
			->execute();
	}
}