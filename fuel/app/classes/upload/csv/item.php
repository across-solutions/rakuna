<?php
use Fuel\Core\Config;
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\Arr;
use Auth\Auth;
/**
 * 商品CSVアップロードクラス
 */
class Upload_Csv_Item extends Upload_Csv_Base {

	/**
	 * カテゴリコードリスト
	 */
	private $categories = array();

	/**
	 * 商品コードリスト
	 */
	private $items = array();

	/**
	 * JANコードリスト
	 */
	private $jan_codes = array();

	/**
	 * JANコードチェック
	 */
	private $jan_codes_check = array();

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * 必須ではないCSV項目の初期値
	 */
	protected $norequire_columns_default = array(
			array('item_yomigana', 'yomigana', ''),
			array('item_size', 'size', ''),
			array('item_comment', 'comment', ''),
			array('item_price', 'price', 0),
			array('item_price_case', 'price_case', 0),
			array('jan_code', 'jan_code', ''),
	);

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->categories = $this->list_item_category_code();
		$this->items = $this->list_item_code();
		$this->jan_codes = $this->list_jan_code();
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.item');
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
					case 'category_code':
						$this->validate_category_code($value, $num);
						break;
					case 'item_code':
						$this->validate_item_code($value, $num);
						break;
					case 'item_name':
						$this->validate_item_name($value, $num);
						break;
					case 'item_yomigana':
						$this->validate_item_yomigana($value, $num);
						break;
					case 'item_size':
						$this->validate_item_size($value, $num);
						break;
					case 'item_comment':
						$this->validate_item_comment($value, $num);
						break;
					case 'item_price':
						$this->validate_price($value, $num);
						break;
					case 'item_price_case':
						$this->validate_price_case($value, $num);
						break;
					case 'jan_code':
						$this->validate_jan_code($value, $num, $data['item_code']);
						break;
				}
			}
		} elseif ($control_code == '1') {
			// 削除
			if (!array_key_exists($data['item_code'], $this->items)) {
				// 商品コードが存在しない場合は無視する
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
			if (array_key_exists($data['item_code'], $this->items)) {
				if (!$this->update_item($data)) {
					return false;
				}
			} else {
				if (!$this->insert_item($data)) {
					return false;
				}
			}
		} elseif ($control_code == '1') {
			if (!$this->remove_item($data['item_code'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @see Upload_Csv_Base::get_unique_key()
	 */
	protected function get_unique_key($data) {
		return $data['item_code'];
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
	 * カテゴリコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_category_code($value, $num) {
		if ($value != '' && !array_key_exists($value, $this->categories)) {
			parent::set_error($num, 'カテゴリが存在しません[' . $value . ']');
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
		if (!Common_Validation::_validation_alphanum($value)) {
			parent::set_error($num, '商品コードは半角英数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 20) {
			parent::set_error($num, '商品コードは20文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 商品名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_name($value, $num) {
		if ($value == '') {
			parent::set_error($num, '商品名を入力してください');
			return false;
		}
		if (Str::length($value) > 50) {
			parent::set_error($num, '商品名は50文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 商品カナ名バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_yomigana($value, $num) {
		if (Str::length($value) > 50) {
			parent::set_error($num, '商品カナ名は50文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 入数バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_size($value, $num) {
		if (Str::length($value) > 10) {
			parent::set_error($num, '入数は10文字以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 商品説明文バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_comment($value, $num) {
		if (Str::length($value) > 500) {
			parent::set_error($num, '商品説明文は500文字以下で入力してください[' . $value . ']');
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
	 * JANコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 * @param $item_code 商品コード
	 */
	private function validate_jan_code($value, $num, $item_code) {
		if ($value == '') {
			return true;
		}
		if (!is_numeric($value)) {
			parent::set_error($num, 'JANコードは数字で入力してください[' . $value . ']');
			return false;
		}
		if (Str::length($value) > 13) {
			parent::set_error($num, 'JANコードは13桁以下で入力してください[' . $value . ']');
			return false;
		}

		if (array_key_exists($value, $this->jan_codes_check)) {
			parent::set_error($num, 'JANコードが重複しています[' . $value . ']');
			return false;
		}
		$this->jan_codes_check[$value] = $value;

		if (isset($this->jan_codes[$value])) {
			$code = $this->jan_codes[$value];
			if ($code != $item_code) {
				parent::set_error($num, 'JANコードはすでに登録されています[' . $value . ']');
				return false;
			}
		}
		return true;
	}

	/**
	 * カテゴリコードリストを取得する
	 */
	private function list_item_category_code() {
		return DB::select('code', 'id')
			->from('item_categories')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 商品コードリストを取得する
	 */
	private function list_item_code() {
		$items = DB::select('id', 'item_category_id', 'code', 'name', 'yomigana', 'size', 'comment', 'price', 'price_case', 'jan_code')
				->from('items')
				->where('del_flg', UNDELETED)
				->order_by('code', 'asc')
				->execute();

		$list = array();
		foreach ($items as $item) {
			$list[$item['code']] = $item;
		}
		return $list;
	}

	/**
	 * JANコードリストを取得する
	 */
	private function list_jan_code() {
		return DB::select('jan_code', 'code')
				->from('items')
				->where('jan_code', '!=', null)
				->where('jan_code', '!=', '')
				->where('del_flg', UNDELETED)
				->order_by('jan_code', 'asc')
				->execute()
				->as_array('jan_code', 'code');
	}

	/**
	 * 商品を登録する
	 *
	 * @param array $data データ
	 */
	private function insert_item($data) {
		parent::set_norequire_columns($data);
		if (isset($data['category_code'])) {
		$category_id = $data['category_code'] == '' ? null : $this->categories[$data['category_code']];
		} else {
			$category_id = null;
		}

		$values = array();
		$values['item_category_id'] = $category_id;
		$values['code'] = $data['item_code'];
		$values['name'] = $data['item_name'];
		$values['yomigana'] = $data['item_yomigana'];
		$values['size'] = $data['item_size'];
		$values['comment'] = $data['item_comment'];
		$values['price'] = empty($data['item_price']) ? 0 : $data['item_price'];
		$values['price_case'] = empty($data['item_price_case']) ? 0 : $data['item_price_case'];
		$values['jan_code'] = $data['jan_code'];
		$values['pr_flg'] = false;
		$values['renewal_datetime'] = date('Y-m-d H:i:s');
		$values['search_field'] =  $data['item_code'] . ' ' . $data['item_name'] . ' ' . $data['item_yomigana'] . ' ' . $data['item_comment'];
		$values['del_flg'] = UNDELETED;
		$values['update_user_id'] = Auth::get_user_id()[1];
		$values['created'] = date('Y-m-d H:i:s');
		$values['updated'] = date('Y-m-d H:i:s');

		return DB::insert('items')->set($values)->execute();
	}

	/**
	 * 商品を更新する
	 *
	 * @param array $data データ
	 */
	private function update_item($data) {
		$item = $this->items[$data['item_code']];
		parent::set_norequire_columns($data, $item);
		if (isset($data['category_code'])) {
		$category_id = $data['category_code'] == '' ? null : $this->categories[$data['category_code']];
		} else {
			$category_id = $item['item_category_id'];
		}

		if ($item['item_category_id'] == $category_id
				&& $item['code'] == $data['item_code']
				&& $item['name'] == $data['item_name']
				&& $item['yomigana'] == $data['item_yomigana']
				&& $item['size'] == $data['item_size']
				&& $item['comment'] == $data['item_comment']
				&& $item['price'] == $data['item_price']
				&& $item['price_case'] == $data['item_price_case']
				&& $item['jan_code'] == $data['jan_code']) {
			return true;
		}

		$query = DB::update('items')
			->value('item_category_id', $category_id)
			->value('name', $data['item_name'])
			->value('yomigana', $data['item_yomigana'])
			->value('size', $data['item_size'])
			->value('comment', $data['item_comment'])
			->value('price', empty($data['item_price']) ? 0 : $data['item_price'])
			->value('price_case', empty($data['item_price_case']) ? 0 : $data['item_price_case'])
			->value('jan_code', $data['jan_code'])
			->value('search_field', $data['item_code'] . ' ' . $data['item_name'] . ' ' . $data['item_yomigana'] . ' ' . $data['item_comment'] )
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', '=', $item['id']);

		if ($item['price'] != $data['item_price'] || $item['price_case'] != $data['item_price_case']) {
			$query->value('renewal_datetime', date('Y-m-d H:i:s'));
		}

		return $query->execute() !== false;
	}

	/**
	 * 商品を削除する
	 *
	 * @param string $code 商品コード
	 */
	private function remove_item($code) {
		return DB::update('items')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('code', $code)
			->where('del_flg', UNDELETED)
			->execute();
	}
}