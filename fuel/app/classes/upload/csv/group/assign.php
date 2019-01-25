<?php
use Fuel\Core\DB;
/**
 * グループ割当商品CSVアップロードクラス
 */
class Upload_Csv_Group_Assign extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * エラー以外の取込有無
	 */
	protected $other_than_error = true;

	/**
	 * 発注者グループコードリスト
	 */
	private $member_groups = array();

	/**
	 * 商品コードリスト
	 */
	private $items = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->member_groups = $this->list_member_group_code();
		$this->items = $this->list_item_code();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.group_assign');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		$result = true;

		foreach ($data as $key => $value) {
			switch ($key) {
				case 'member_group_code':
					if (!$this->validate_member_group_code($value, $num)) {
						$result = false;
					}
					break;
				case 'item_code':
					if (!$this->validate_item_code($value, $num)) {
						$result = false;
					}
					break;
				case 'item_price':
					if (!$this->validate_price($value, $num)) {
						$result = false;
					}
					break;
			}
		}

		return $result;
	}

	/**
	 * @see Upload_Csv_Base::save_before()
	 */
	protected function save_before() {
		if (!$this->delete_group_assign()) {
			return false;
		}

		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_line()
	 */
	protected function save_line($data) {
		if (!$this->insert_group_assign($data)) {
			return false;
		}

		return true;
	}

	/**
	 * @see Upload_Csv_Base::get_unique_key()
	 */
	protected function get_unique_key($data) {
		return $data['item_code'] . $data['member_group_code'];
	}

	/**
	 * 発注者グループコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_member_group_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, 'グループコードを入力してください');
			return false;
		}
		if (!array_key_exists($value, $this->member_groups)) {
			parent::set_error($num, 'グループコードが存在しません[' . $value . ']');
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
			parent::set_error($num, '単価は数値で入力してください[' . $value . ']');
			return false;
		}
		if ($value < 0 || $value > 9999999) {
			parent::set_error($num, '単価は0以上、9999999以下で入力してください[' . $value . ']');
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
	 * 発注者グループコードリストを取得する
	 */
	private function list_member_group_code() {
		return DB::select('code', 'id')
			->from('member_groups')
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
	 * グループ割当リストを取得する
	 */
	private function list_group_assign() {
		$assigns = DB::select('id', 'item_code', 'member_group_code', 'price', 'price_case')
			->from('group_assigns')
			->where('del_flg', UNDELETED)
			->order_by('item_code', 'ASC')
			->execute()
			->as_array();

		$results = array();
		foreach ($assigns as $assign) {
			if (!isset($results[$assign['item_code']])) {
				$results[$assign['item_code']] = array();
			}
			$results[$assign['item_code']][$assign['member_group_code']] = $assign;
		}
		return $results;
	}

	/**
	 * グループ割当商品を登録する
	 *
	 * @param array $data 行データ
	 */
	private function insert_group_assign($data) {
		$values = array();
		$values['item_code'] = $data['item_code'];
		$values['member_group_code'] = $data['member_group_code'];
		$values['price'] = $data['group_price'] === '' ? null : $data['group_price'];
		$values['price_case'] = $data['group_price'] === '' ? null : $data['group_price'];
		$values['renewal_datetime'] = date('Y-m-d H:i:s');
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('group_assigns')->set($values)->execute() !== false;
	}

	/**
	 * グループ割当商品を更新する
	 *
	 * @param array $data 行データ
	 * @param array $assign 元データ
	 */
	private function update_group_assign($data, $assign) {
		$price = $data['group_price'] === '' ? null : $data['group_price'];
		$price_case = $data['group_price_case'] === '' ? null : $data['group_price_case'];

		if ($price === $assign['price']
				&& $price_case === $assign['price_case']) {
			return true;
		}

		return DB::update('group_assigns')
			->value('price', $price)
			->value('price_case', $price)
			->value('renewal_datetime', date('Y-m-d H:i:s'))
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $assign['id'])
			->execute() !== false;
	}

	/**
	 * グループ割当商品を削除する
	 */
	private function delete_group_assign() {
		return DB::delete('group_assigns')
			->where('del_flg', '=', UNDELETED)
			->execute();
	}
}