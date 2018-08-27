<?php
use Fuel\Core\Unzip;
use Fuel\Core\Config;
use Fuel\Core\File;
use Fuel\Core\DB;
/**
 * 商品画像ZIPファイルアップロードクラス
 */
class Upload_Image_Item {

	/**
	 * 商品コードリスト
	 */
	private $items = array();
	
	/**
	 * 商品画像ZIPファイルパス
	 */
	private $file = null;
	
	/**
	 * 商品画像リスト
	 */
	private $images = array();

	/**
	 * エラーメッセージ
	 */
	private $errors = array();
	
	/**
	 * コンストラクタ
	 * 
	 * @param array $file 商品画像ZIPファイルパス
	 */
	public function __construct($file) {
		$this->file = $file;
		
		$this->items = $this->list_item_code();
	}
	
	/**
	 * ZIPファイルを読み込む
	 */
	public function parse() {
		$unzip = new Unzip();
		$images = $unzip->extract($this->file);
		if (empty($images)) {
			$this->set_error('画像ファイルが見つかりません');
			return;
		}
		
		foreach ($images as $image) {
			if ($this->validate_image($image)) {
				$this->images[] = $image;
			}
		}
		
		if (!empty($this->errors)) {
			Session::set_flash('validate_upload_errors', $this->errors);
		}
	}
	
	/**
	 * 保存処理
	 */
	public function save() {
		foreach ($this->images as $image) {
		$file_info = File::file_info($image);
			if (!Image_Item::create($file_info['filename'], $image)) {
				return false;
			}
		}
		return true;
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
	 * エラーメッセージを設定する
	 * 
	 * @param string $message エラーメッセージ
	 */
	protected function set_error($message) {
		$this->errors[] = $message;
	}
	
	private function validate_image($file) {
		$file_info = File::file_info($file);
		$setting = Config::get('upload.setting.item_image');
		
		preg_match('|^(.*)/(.*)|', $file_info['mimetype'], $mimeinfo);
		if ( ! empty($setting['ext_whitelist']) && ! in_array(strtolower($file_info['extension']), $setting['ext_whitelist'])) {
			$this->set_error('jpgファイルのみ有効です[' . $file_info['basename'] . ']');
			return false;
		}

		if ( ! empty($setting['type_whitelist']) && ! in_array($mimeinfo[1], $setting['type_whitelist'])) {
			$this->set_error('jpgファイルのみ有効です[' . $file_info['basename'] . ']');
			return false;
		}

		if (!array_key_exists($file_info['filename'], $this->items)) {
			$this->set_error('商品がみつかりません[' . $file_info['basename'] . ']');
			return false;
		}
		
		return true;
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
}