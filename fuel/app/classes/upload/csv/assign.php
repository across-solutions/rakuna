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
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->members = $this->list_member_code();
		$this->items = $this->list_item_code();
		$this->assigns = $this->list_assign();
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
					$this->validate_price($value, $num);
					break;
				case 'item_price_case':
					$this->validate_price_case($value, $num);
					break;
				case 'hidden_flg_single':
					$this->validate_hidden_flg_single($value, $num);
					break;
				case 'hidden_flg_case':
					$this->validate_hidden_flg_case($value, $num);
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
		return $data['item_code'] . $data['member_code'];
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
	 */
	private function validate_price($value, $num) {
		if ($value == '') {
			return true;
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
	 * 単価(ケース)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_price_case($value, $num) {
		if ($value == '') {
			return true;
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
	 * 非表示フラグ(バラ)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_hidden_flg_single($value, $num) {
		if ($value == '') {
			parent::set_error($num, '非表示フラグ(バラ)を入力してください');
			return false;
		}
		if ($value != '0' && $value != '1') {
			parent::set_error($num, '非表示フラグ(バラ)は0、または、1で入力してください[' .$value .']');
			return false;
		}
		return true;
	}

	/**
	 * 非表示フラグ(ケース)バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_hidden_flg_case($value, $num) {
		if ($value == '') {
			parent::set_error($num, '非表示フラグ(ケース)を入力してください');
			return false;
		}
		if ($value != '0' && $value != '1') {
			parent::set_error($num, '非表示フラグ(ケース)は0、または、1で入力してください[' .$value .']');
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
		$assigns = DB::select('id', 'item_code', 'member_id', 'price', 'price_case',
								'hidden_flg_single', 'hidden_flg_case')
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
		$values['price_case'] = $data['item_price_case'] === '' ? null : $data['item_price_case'];
		$values['hidden_flg_single'] = $data['hidden_flg_single'];
		$values['hidden_flg_case'] = $data['hidden_flg_case'];
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
		$price_case = $data['item_price_case'] === '' ? null : $data['item_price_case'];

		if ($price === $assign['price']
				&& $price_case === $assign['price_case']
				&& $assign['hidden_flg_single'] == $data['hidden_flg_single']
				&& $assign['hidden_flg_case'] == $data['hidden_flg_case']) {
			return true;
		}

		return DB::update('item_assigns')
			->value('price', $price)
			->value('price_case', $price_case)
			->value('hidden_flg_single', $data['hidden_flg_single'])
			->value('hidden_flg_case', $data['hidden_flg_case'])
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