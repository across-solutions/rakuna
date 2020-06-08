<?php
use Fuel\Core\DB;
/**
 * 商品発注タイプCSVアップロードクラス
 */
class Upload_Csv_Item_Order_Type extends Upload_Csv_Base {

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

	/** 発注タイプリスト */
	private $order_types = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->members = $this->list_member_code();
		$this->items = $this->list_item_code();
		$this->order_types = $this->list_order_type();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.item_order_type');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		$result = true;

		foreach ($data as $key => $value) {
			switch ($key) {
				case 'member_code':
					if (!$this->validate_member_code($value, $num)) {
						$result = false;
					}
					break;
				case 'item_code':
					if (!$this->validate_item_code($value, $num)) {
						$result = false;
					}
					break;
				case 'order_type':
					if (!$this->validate_order_type($value, $num)) {
						$result = false;
					}
					break;
			}
		}

		return $result;
	}

	/**
	 * @see Upload_Csv_Base::save_line()
	 */
	protected function save_line($data) {
		$control_code = $data['control_code'];
		$member_id = $this->members[$data['member_code']];
		$item_code = $data['item_code'];
		$this->delete_item_order_type($member_id, $item_code);
		if ($control_code == '0') {
			$order_type = $data['order_type'];
			return $this->insert_item_order_type($member_id, $item_code, $order_type);
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
	private function validate_order_type($value, $num) {
		if (!array_key_exists($value, $this->order_types)) {
			parent::set_error($num, '発注タイプが存在しません[' . $value . ']');
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
	 * 発注タイプリストを取得する
	 */
	private function list_order_type() {
		return DB::select('id')
			->from('order_types')
			->where('del_flg', UNDELETED)
			->order_by('id')
			->execute()
			->as_array('id');
	}

	/**
	 * 商品発注タイプを登録する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 * @param int $order_type 発注タイプ
	 */
	private function insert_item_order_type($member_id, $item_code, $order_type) {
		$values = array();
		$values['item_code'] = $item_code;
		$values['member_id'] = $member_id;
		$values['order_type'] = $order_type;
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('item_order_types')->set($values)->execute() !== false;
	}

	/**
	 * 商品発注タイプを削除する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function delete_item_order_type($member_id, $item_code) {
		return DB::delete('item_order_types')
			->where('member_id', $member_id)
			->where('item_code', $item_code)
			->execute();
	}
}