<?php
use Fuel\Core\Config;
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\Arr;
use Auth\Auth;
/**
 * 配達曜日CSVアップロードクラス
 */
class Upload_Csv_Delivery_Week extends Upload_Csv_Base {

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
	protected $norequire_columns_default = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->delivery_weeks = $this->list_delivery_week_code();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.delivery_week');
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
					case 'delivery_week_code':
						$this->validate_delivery_week_code($value, $num);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['delivery_week_code'], $this->delivery_weeks)) {
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
			if (array_key_exists($data['delivery_week_code'], $this->delivery_weeks)) {
				if (!$this->update_delivery_week($this->delivery_weeks[$data['delivery_week_code']], $data)) {
					return false;
				}
			} else {
				if (!$this->insert_delivery_week($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (array_key_exists($data['delivery_week_code'], $this->delivery_weeks)) {
				if (!$this->delete_delivery_week($this->delivery_weeks[$data['delivery_week_code']])) {
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
		return $data['delivery_week_code'];
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
	 * 配達曜日コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_delivery_week_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '配達曜日コードを入力してください');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '配達曜日コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 10) {
			parent::set_error($num, '配達曜日コードは10文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 配達曜日コードリストを取得する
	 */
	private function list_delivery_week_code() {
		$delivery_weeks = DB::select('id', 'code', 'delivery_flg_mon', 'delivery_flg_tue', 'delivery_flg_wed',
								'delivery_flg_thu', 'delivery_flg_fri', 'delivery_flg_sat', 'delivery_flg_sun')
				->from('delivery_weeks')
				->where('del_flg', UNDELETED)
				->order_by('code', 'asc')
				->execute();

		$list = array();
		foreach ($delivery_weeks as $delivery_week) {
			$list[$delivery_week['code']] = $delivery_week;
		}
		return $list;
	}

	/**
	 * 配達曜日を登録する
	 *
	 * @param array $data データ
	 */
	private function insert_delivery_week($data) {
		$delivery_flg_mon = isset($data['delivery_flg_mon']) && $data['delivery_flg_mon'] == '1';
		$delivery_flg_tue = isset($data['delivery_flg_tue']) && $data['delivery_flg_tue'] == '1';
		$delivery_flg_wed = isset($data['delivery_flg_wed']) && $data['delivery_flg_wed'] == '1';
		$delivery_flg_thu = isset($data['delivery_flg_thu']) && $data['delivery_flg_thu'] == '1';
		$delivery_flg_fri = isset($data['delivery_flg_fri']) && $data['delivery_flg_fri'] == '1';
		$delivery_flg_sat = isset($data['delivery_flg_sat']) && $data['delivery_flg_sat'] == '1';
		$delivery_flg_sun = isset($data['delivery_flg_sun']) && $data['delivery_flg_sun'] == '1';

		$values = array();
		$values['code'] = $data['delivery_week_code'];
		$values['delivery_flg_mon'] = $delivery_flg_mon;
		$values['delivery_flg_tue'] = $delivery_flg_tue;
		$values['delivery_flg_wed'] = $delivery_flg_wed;
		$values['delivery_flg_thu'] = $delivery_flg_thu;
		$values['delivery_flg_fri'] = $delivery_flg_fri;
		$values['delivery_flg_sat'] = $delivery_flg_sat;
		$values['delivery_flg_sun'] = $delivery_flg_sun;
		$values['search_field'] = Common_Util::mb_converts($data, array('delivery_week_code'));
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('delivery_weeks')->set($values)->execute();
	}

	/**
	 * 配達曜日を更新する
	 *
	 * @param array $member 元データ
	 * @param array $data データ
	 */
	private function update_delivery_week($delivery, $data) {
		$delivery_flg_mon = isset($data['delivery_flg_mon']) && $data['delivery_flg_mon'] == '1';
		$delivery_flg_tue = isset($data['delivery_flg_tue']) && $data['delivery_flg_tue'] == '1';
		$delivery_flg_wed = isset($data['delivery_flg_wed']) && $data['delivery_flg_wed'] == '1';
		$delivery_flg_thu = isset($data['delivery_flg_thu']) && $data['delivery_flg_thu'] == '1';
		$delivery_flg_fri = isset($data['delivery_flg_fri']) && $data['delivery_flg_fri'] == '1';
		$delivery_flg_sat = isset($data['delivery_flg_sat']) && $data['delivery_flg_sat'] == '1';
		$delivery_flg_sun = isset($data['delivery_flg_sun']) && $data['delivery_flg_sun'] == '1';

		if ($delivery['code'] == $data['delivery_week_code']
				&& $delivery['delivery_flg_mon'] == $delivery_flg_mon
				&& $delivery['delivery_flg_tue'] == $delivery_flg_tue
				&& $delivery['delivery_flg_wed'] == $delivery_flg_wed
				&& $delivery['delivery_flg_thu'] == $delivery_flg_thu
				&& $delivery['delivery_flg_fri'] == $delivery_flg_fri
				&& $delivery['delivery_flg_sat'] == $delivery_flg_sat
				&& $delivery['delivery_flg_sun'] == $delivery_flg_sun) {
			return true;
		}

		$query = DB::update('delivery_weeks')
			->value('code', $data['delivery_week_code'])
			->value('delivery_flg_mon', $delivery_flg_mon)
			->value('delivery_flg_tue', $delivery_flg_tue)
			->value('delivery_flg_wed', $delivery_flg_wed)
			->value('delivery_flg_thu', $delivery_flg_thu)
			->value('delivery_flg_fri', $delivery_flg_fri)
			->value('delivery_flg_sat', $delivery_flg_sat)
			->value('delivery_flg_sun', $delivery_flg_sun)
			->value('search_field', Common_Util::mb_converts($data, array('delivery_week_code')))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $delivery['id']);

		return $query->execute() !== false;
	}

	/**
	 * 配達曜日を削除する
	 *
	 * @param array $member 元データ
	 */
	private function delete_delivery_week($delivery_week) {
		$query = DB::update('delivery_weeks')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('code', '=', $delivery_week['code'])
			->where('del_flg', '=', UNDELETED);

		return $query->execute() !== false;
	}
}
