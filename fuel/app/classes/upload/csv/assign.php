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
			if (!isset($this->assigns[$item_code][$member_id])) {
				if (!$this->insert_item_assign($item_code, $member_id)) {
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
		$assigns = DB::select('id', 'item_code', 'member_id')
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
			$results[$assign['item_code']][$assign['member_id']] = $assign['id'];
		}
		return $results;
	}

	/**
	 * 割当商品を登録する
	 *
	 * @param string $item_code 商品コード
	 * @param int $member_id 発注者アカウントID
	 */
	private function insert_item_assign($item_code, $member_id) {
		$values = array();
		$values['item_code'] = $item_code;
		$values['member_id'] = $member_id;
		$values['renewal_datetime'] = date('Y-m-d H:i:s');

		$model = Model_Item_Assign::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 割当商品を削除する
	 *
	 * @param int $id 割当ID
	 */
	private function delete_item_assign($id) {
		return DB::update('item_assigns')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $id)
			->execute();
	}
}