<?php
use Fuel\Core\Session;
use Fuel\Core\DB;
/**
 * CSVアップロード基底クラス
 */
abstract  class Upload_Csv_Base {

	/**
	 * CSVフォーマット区分
	 */
	abstract protected function get_csv_format_div();

	/**
	 * バリデート
	 *
	 * @param array $data 行データ
	 * @param int $num 行番号
	 */
	abstract protected function validate(&$data, $num);

	/**
	 * 保存処理
	 *
	 * @param array $data 行データ
	 */
	abstract protected function save_line($data);

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = false;

	/**
	 * CSVファイルパス
	 */
	private $file = null;

	/**
	 * データ
	 */
	private $data = array();

	/**
	 * エラーメッセージ
	 */
	private $errors = array();

	/**
	 * 必須ではないCSV項目の初期値
	 * 各要素はarray(CSVのカラム名,DBのカラム名,初期値)で定義
	 */
	protected $norequire_columns_default = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイルパス
	 */
	public function __construct($file) {
		$this->file = $file;
	}

	/**
	 * CSVファイルを読み込む
	 */
	public function parse() {
		$csv_format = $this->get_csv_format();
		if (empty($csv_format)) {
			throw new \HttpServerErrorException();
		}

		$str = file_get_contents($this->file);
		mb_convert_variables('UTF-8', 'UTF-16', $str);
		file_put_contents($this->file, $str);

		$file = new SplFileObject($this->file);
		$file->setCsvControl(',', '"', '^');
		$file->setFlags(SplFileObject::READ_CSV);
		foreach($file as $num => $values) {
			if ($this->has_header && $num == 0) {
				continue;
			}

			if (count($values) == 1 && $values[0] == null) {
				continue;
			}

			if (count($csv_format) != count($values)) {
				$this->set_error($num, '列数が不正です[' . count($values) . ']');
				continue;
			}

			$data = array();
			$index = 0;
			foreach ($csv_format as $format) {
				$data[$format->key] = $values[$index];
				$index++;
			}

			if($this->validate($data, $num)) {
				$key = $this->get_unique_key($data);
				if (is_null($key)) {
					$this->data[] = $data;
				} else {
					$this->data[$key] = $data;
				}
			}
		}

		if (!empty($this->errors)) {
			$this->add_error('上記のエラー以外を取り込みました');
			Session::set_flash('validate_upload_errors', $this->errors);
		}
	}

	/**
	 * 保存処理
	 */
	public function save() {
		DB::start_transaction();
		foreach ($this->data as $data) {
			if (!$this->save_line($data)) {
				DB::rollback_transaction();
				return false;
			}
		}

		if (!$this->save_after()) {
			DB::rollback_transaction();
			return false;
		}

		DB::commit_transaction();
		return true;
	}

	/**
	 * 一意キーを返す
	 * この値が一致する行が存在する場合は後勝ち
	 *
	 * @param array $data 行データ
	 */
	protected function get_unique_key($data) {
		return null;
	}

	/**
	 * データを取得する
	 */
	protected function get_data() {
		return $this->data;
	}

	/**
	 * エラーメッセージを取得する
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * エラーの有無を返す
	 */
	public function has_error() {
		return count($this->errors) > 0;
	}

	/**
	 * 保存後処理
	 */
	protected function save_after() {
		return true;
	}

	/**
	 * エラーメッセージを設定する
	 *
	 * @param int $num 行番号
	 * @param string $message エラーメッセージ
	 */
	protected function set_error($num, $message) {
		$this->errors[$num + 1][] = $message;
	}

	/**
	 * エラーメッセージを末尾に追加する
	 * @param string $message エラーメッセージ
	 */
	protected function add_error($message) {
		$this->errors[] = $message;
	}

	/**
	 * CSVフォーマットを取得する
	 */
	private function get_csv_format() {
		return Model_Csv_Format::query()
			->where('div', $this->get_csv_format_div())
			->order_by('sort', 'asc')->order_by('id', 'asc')
			->get();
	}

	/**
	 * 非必須のCSV未設定項目の設定
	 * @param array &$data CSVレコードデータ
	 * @param array $dest 更新レコード 新規登録時は不要
	 */
	protected function set_norequire_columns(&$data, $dest = array()) {
		foreach ($this->norequire_columns_default as $set) {
			list($data_col, $dest_col, $default) = $set;
			if (!isset($data[$data_col])) {
				$data[$data_col] = isset($dest[$dest_col]) ? $dest[$dest_col] : $default;
			}
		}
	}
}