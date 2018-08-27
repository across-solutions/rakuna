<?php
use Fuel\Core\Config;
use Fuel\Core\Lang;
use Fuel\Core\File;
use Fuel\Core\Upload;
use Fuel\Core\Format;
use Auth\Auth;

/**
 * PR商品CSVアップロードクラス
 */
class Upload_Csv_Pr extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;
	
	private $item_codes = array();

	private $pr_items = array();
	
	/**
	 * コンストラクタ
	 * 
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);
		
		$this->item_codes = $this->list_item_code();
		$this->pr_items = $this->list_pr_item();
	}
	
	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.pr');
	}
	
	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		foreach ($data as $key => $value) {
			switch ($key) {
				case 'item_code':
					$this->validate_item_code($value, $num);
					break;
			}
		}
		
		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_line()
	 */
	protected function save_line($data) {
		$item_code = Arr::get($data, 'item_code');
		if (array_key_exists($item_code, $this->pr_items)) {
			unset($this->pr_items[$item_code]);
			return true;
		}
		
		$item = $this->get_item($item_code);
		if (!$this->update_pr($item)) {
			return false;
		}
		
		return true;
	}

	/**
	 * @see Upload_Csv_Base::save_after()
	 */
	protected function save_after() {
		foreach ($this->pr_items as $item) {
			if (!$this->update_pr($item, false)) {
				return false;
			}
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
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '商品コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 20) {
			parent::set_error($num, '商品コードは20文字以下で入力してください[' . $value . ']');
			return false;
		}
		if (!array_key_exists($value, $this->item_codes)) {
			parent::set_error($num, '商品が存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 商品を取得する
	 * 
	 * @param string $item_code 商品コード
	 */
	private function get_item($item_code) {
		return Model_Item::query()
			->where('code', '=', $item_code)
			->get_one();
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
	 * PR商品リストを取得する
	 */
	private function list_pr_item() {
		$items = Model_Item::query()
			->where('pr_flg', '=', true)
			->order_by('code', 'asc')
			->get();
		
		$list = array();
		foreach ($items as $item) {
			$list[$item['code']] = $item;
		}
		return $list;
	}
	
	/**
	 * PR商品に更新する
	 * 
	 * @param Model_Item $item 商品
	 * @param boolean $pr_flg PRフラグ
	 */
	private function update_pr($item, $pr_flg = true) {
		$item->pr_flg = $pr_flg;
		
		return $item->save() !== false;
	}
}