<?php
use Fuel\Core\DB;
/**
 * いつものグループ割当CSVアップロードクラス
 */
class Upload_Csv_Recommended_Group_Assign extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * 発注者コードリスト
	 */
	private $members = array();

	/**
	 * いつものグループリスト
	 */
	private $recommended_groups = array();

	/**
	 * いつものグループ割当リスト
	 */
	private $recommended_group_assigns = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->members = $this->list_member_code();
		$this->recommended_groups = $this->list_recommended_group_code();
		$this->recommended_group_assigns = $this->list_recommended_group_assign();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.recommended_group_assign');
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
				case 'recommended_group_code':
					$this->validate_recommended_group_code($value, $num);
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
		$recommended_group_id = $this->recommended_groups[$data['recommended_group_code']];

		if ($control_code == '0') {
			if (!isset($this->recommended_group_assigns[$recommended_group_id][$member_id])) {
				if (!$this->insert_recommended_group_assign($recommended_group_id, $member_id)) {
					return false;
				}
			}
		} else {
			if (isset($this->recommended_group_assigns[$recommended_group_id][$member_id])) {
				if (!$this->delete_recommended_group_assign($this->recommended_group_assigns[$recommended_group_id][$member_id])) {
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
		return $data['member_code'] . '_' . $data['recommended_group_code'];
	}

	/**
	 * いつものグループコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_recommended_group_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, 'いつものグループコードを入力してください');
			return false;
		}
		if (!array_key_exists($value, $this->recommended_groups)) {
			parent::set_error($num, 'いつものグループコードが存在しません[' . $value . ']');
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
	 * いつものグループIDリストを取得する
	 */
	private function list_recommended_group_code() {
		return DB::select('code','id')
			->from('recommended_groups')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * いつものグループ割当リストを取得する
	 */
	private function list_recommended_group_assign() {
		$recommended_group_assigns = DB::select('id', 'recommended_group_id', 'member_id')
			->from('recommended_group_assigns')
			->where('del_flg', UNDELETED)
			->order_by('recommended_group_id', 'ASC')
			->execute()
			->as_array();

		$results = array();
		foreach ($recommended_group_assigns as $recommended_group_assign) {
			if (!isset($results[$recommended_group_assign['recommended_group_id']])) {
				$results[$recommended_group_assign['recommended_group_id']] = array();
			}
			$results[$recommended_group_assign['recommended_group_id']][$recommended_group_assign['member_id']] = $recommended_group_assign['id'];
		}
		return $results;
	}

	/**
	 * いつものグループ割当を登録する
	 *
	 * @param string $recommended_group_id いつものグループID
	 * @param int $member_id 発注者アカウントID
	 */
	private function insert_recommended_group_assign($recommended_group_id, $member_id) {
		$values = array();
		$values['recommended_group_id'] = $recommended_group_id;
		$values['member_id'] = $member_id;

		$model = Model_Recommended_Group_Assign::forge($values);

		return $model->save() !== false;
	}

	/**
	 * いつものグループ割当を削除する
	 *
	 * @param int $id 割当ID
	 */
	private function delete_recommended_group_assign($id) {
		return DB::update('recommended_group_assigns')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $id)
			->execute();
	}
}