<?php
use Fuel\Core\DB;
/**
 * システム設定クラス
 */
class Common_Setting {

	private static $instance = null;
	
	private $setting = null;
	
	/**
	 * コンストラクタ
	 */
	private function __construct() {
		$this->setting = Model_Setting::find('first');
	}
	
	/**
	 * 設定値を取得する
	 * 
	 * @param string $key 項目名
	 */
	public static function get($key) {
		if (is_null(self::$instance)) {
			self::$instance = new Common_Setting();
		}
		
		return self::$instance->setting->{$key};
	}
	
	/**
	 * 金額表示の有無を返す
	 */
	public static function is_price() {
		return self::get('price_flg');
	}
	
	/**
	 * ケース表示の有無を返す
	 */
	public static function is_case() {
		return self::get('case_flg');
	}
}