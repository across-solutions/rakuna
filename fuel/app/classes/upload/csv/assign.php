<?php
use Fuel\Core\DB;
/**
 * 割当商品CSVアップロードクラス
 */
class Upload_Csv_Assign extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * 発注者コードリスト
	 */
	private $members = array();

	/**
	 * 商品コードリスト
	 */
	private $items = array();

	/**
	 * 割当リスト
	 */
	private $assigns = array();

	/**
	 * 商品単位リスト
	 */
	private $item_units = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->members = $this->list_member_code();
		$this->items = $this->list_item_code();
		$this->assigns = $this->list_assign();
		$this->item_units = $this->list_item_unit();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.assign');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		foreach ($data as $key => $value) {
			switch ($key) {
				case 'member_code':
					$this->validate_member_code($value, $num);
					break;
				case 'item_code':
					$this->validate_item_code($value, $num);
					break;
				case 'item_price':
					$this->validate_price($value, $num, $data['item_unit_code']);
					break;
				case 'item_unit_code':
					$this->validate_unit_code($value, $num, $data['item_price']);
					break;
				case 'item_price_case':
					$this->validate_price_case($value, $num, $data['item_unit_code_case']);
					break;
				case 'item_unit_code_case':
					$this->validate_unit_code_case($value, $num, $data['item_price_case']);
					break;
				case 'item_price_carton':
					$this->validate_price_carton($value, $num, $data['item_unit_code_carton']);
					break;
				case 'item_unit_code_carton':
					$this->validate_unit_code_carton($value, $num, $data['item_price_carton']);
					break;
				case 'control_code':
					$this->validate_control_code($value, $num);
					break;
			}
		}

		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_line()
	 */
	protected function save_line($data) {
		$control_code = $data['control_code'];
		$member_id = $this->members[$data['member_code']];
		$item_code = $data['item_code'];

		if ($control_code == '0') {
			if (isset($this->assigns[$item_code][$member_id])) {
				if (!$this->update_item_assign($data, $this->assigns[$item_code][$member_id])) {
					return false;
				}
			} else {
				if (!$this->insert_item_assign($data, $member_id)) {
					return false;
				}
			}
		} else {
			if (isset($this->assigns[$item_code][$member_id])) {
				if (!$this->delete_item_assign($this->assigns[$item_code][$member_id])) {
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
		return $data['item_code'] . '_' . $data['member_code'];
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
	 * 商品コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '商品コードを入力してください');
			return false;
		}

		if (!array_key_exists($value, $this->items)) {
			parent::set_error($num, '商品コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単価(バラ)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $unit_code 単位コード(バラ)
	 */
	private function validate_price($value, $num, $unit_code) {
		if ($value == '') {
			if ($unit_code == '') {
				return true;
			}
			parent::set_error($num, '単位コード(バラ)が入力されている場合、単価(バラ)の入力は必須です');
			return false;
		}
		if (!is_numeric($value)) {
			parent::set_error($num, '単価(バラ)は数値で入力してください[' . $value . ']');
			return false;
		}
		if ($value < 0 || $value > 9999999) {
			parent::set_error($num, '単価(バラ)は0以上、9999999以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単位コード(バラ)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $price 単価(バラ)
	 */
	private function validate_unit_code($value, $num, $price) {
		if ($value == '') {
			if ($price == '') {
				return true;
			}
			parent::set_error($num, '単価(バラ)が入力されている場合、単位コード(バラ)の入力は必須です');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '単位コード(バラ)は半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 10) {
			parent::set_error($num, '単位コード(バラ)は10文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!array_key_exists($value, $this->item_units)) {
			parent::set_error($num, '単位コード(バラ)は登録されていません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単価(ケース)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $unit_code_case 単位コード(ケース)
	 */
	private function validate_price_case($value, $num, $unit_code_case) {
		if ($value == '') {
			if ($unit_code_case== '') {
				return true;
			}
			parent::set_error($num, '単位コード(ケース)が入力されている場合、単価(ケース)の入力は必須です');
			return false;
		}
		if (!is_numeric($value)) {
			parent::set_error($num, '単価(ケース)は数値で入力してください[' . $value . ']');
			return false;
		}
		if ($value < 0 || $value > 9999999) {
			parent::set_error($num, '単価(ケース)は0以上、9999999以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単位コード(ケース)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $price_case 単価(ケース)
	 */
	private function validate_unit_code_case($value, $num, $price_case) {
		if ($value == '') {
			if ($price_case== '') {
				return true;
			}
			parent::set_error($num, '単価(ケース)が入力されている場合、単位コード(ケース)の入力は必須です');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '単位コード(ケース)は半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 10) {
			parent::set_error($num, '単位コード(ケース)は10文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!array_key_exists($value, $this->item_units)) {
			parent::set_error($num, '単位コード(ケース)は登録されていません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単価(カートン)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $unit_code_carton 単位コード(カートン)
	 */
	private function validate_price_carton($value, $num, $unit_code_carton) {
		if ($value == '') {
			if ($unit_code_carton== '') {
				return true;
			}
			parent::set_error($num, '単位コード(カートン)が入力されている場合、単価(カートン)の入力は必須です');
			return false;
		}
		if (!is_numeric($value)) {
			parent::set_error($num, '単価(カートン)は数値で入力してください[' . $value . ']');
			return false;
		}
		if ($value < 0 || $value > 9999999) {
			parent::set_error($num, '単価(カートン)は0以上、9999999以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 単位コード(カートン)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param string $price_carton 単価(カートン)
	 */
	private function validate_unit_code_carton($value, $num, $price_carton) {
		if ($value == '') {
			if ($price_carton== '') {
				return true;
			}
			parent::set_error($num, '単価(カートン)が入力されている場合、単位コード(カートン)の入力は必須です');
			return false;
		}
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '単位コード(カートン)は半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 10) {
			parent::set_error($num, '単位コード(カートン)は10文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!array_key_exists($value, $this->item_units)) {
			parent::set_error($num, '単位コード(カートン)は登録されていません[' . $value . ']');
			return false;
		}
		return true;
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
	 * 発注者コードリストを取得する
	 */
	private function list_member_code() {
		return DB::select('code', 'id')
			->from('members')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 商品コードリストを取得する
	 */
	private function list_item_code() {
		return DB::select('code')
			->from('items')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code');
	}

	/**
	 * 割当リストを取得する
	 */
	private function list_assign() {
		$assigns = DB::select('id', 'item_code', 'member_id', 'price', 'unit_code',
							'price_case', 'unit_code_case', 'price_carton', 'unit_code_carton')
			->from('item_assigns')
			->where('del_flg', UNDELETED)
			->order_by('item_code', 'ASC')
			->execute()
			->as_array();

		$results = array();
		foreach ($assigns as $assign) {
			if (!isset($results[$assign['item_code']])) {
				$results[$assign['item_code']] = array();
			}
			$results[$assign['item_code']][$assign['member_id']] = $assign;
		}
		return $results;
	}

	/**
	 * 商品単位リストを取得する
	 */
	private function list_item_unit() {
		$results = DB::select('code', 'name')
		->from('item_units')
		->where('del_flg', '=', UNDELETED)
		->order_by('code', 'ASC')
		->execute();

		$list = array();
		foreach ($results as $result) {
			$list[$result['code']] = $result;
		}

		return $list;
	}

	/**
	 * 割当商品を登録する
	 *
	 * @param array $data 行データ
	 * @param int $member_id 発注者アカウントID
	 */
	private function insert_item_assign($data, $member_id) {
		$values = array();
		$values['item_code'] = $data['item_code'];
		$values['member_id'] = $member_id;
		$values['price'] = $data['item_price'] === '' ? null : $data['item_price'];
		$values['unit_code'] = $data['item_unit_code'] === '' ? null : $data['item_unit_code'];
		$values['price_case'] = $data['item_price_case'] === '' ? null : $data['item_price_case'];
		$values['unit_code_case'] = $data['item_unit_code_case'] === '' ? null : $data['item_unit_code_case'];
		$values['price_carton'] = $data['item_price_carton'] === '' ? null : $data['item_price_carton'];
		$values['unit_code_carton'] = $data['item_unit_code_carton'] === '' ? null : $data['item_unit_code_carton'];
		$values['renewal_datetime'] = date('Y-m-d H:i:s');
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('item_assigns')->set($values)->execute() !== false;
	}

	/**
	 * 割当商品を更新する
	 *
	 * @param array $data 行データ
	 * @param array $assign 元データ
	 */
	private function update_item_assign($data, $assign) {
		$price = $data['item_price'] === '' ? null : $data['item_price'];
		$unit_code = $data['item_unit_code'] === '' ? null : $data['item_unit_code'];
		$price_case = $data['item_price_case'] === '' ? null : $data['item_price_case'];
		$unit_code_case = $data['item_unit_code_case'] === '' ? null : $data['item_unit_code_case'];
		$price_carton = $data['item_price_carton'] === '' ? null : $data['item_price_carton'];
		$unit_code_carton = $data['item_unit_code_carton'] === '' ? null : $data['item_unit_code_carton'];

		if ($price === $assign['price']
				&& $unit_code === $assign['unit_code']
				&& $price_case === $assign['price_case']
				&& $unit_code_case === $assign['unit_code_case']
				&& $price_carton === $assign['price_carton']
				&& $unit_code_carton === $assign['unit_code_carton']) {
			return true;
		}

		return DB::update('item_assigns')
			->value('price', $price)
			->value('unit_code', $unit_code)
			->value('price_case', $price_case)
			->value('unit_code_case', $unit_code_case)
			->value('price_carton', $price_carton)
			->value('unit_code_carton', $unit_code_carton)
			->value('renewal_datetime', date('Y-m-d H:i:s'))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $assign['id'])
			->execute() !== false;
	}

	/**
	 * 割当商品を削除する
	 *
	 * @param array $assign 元データ
	 */
	private function delete_item_assign($assign) {
		return DB::update('item_assigns')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $assign['id'])
			->execute();
	}
}