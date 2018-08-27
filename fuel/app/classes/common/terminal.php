<?php
/**
 * 端末判定クラス
 */
class Common_Terminal {
	
	private static $instance = null;
	
	private $detect = null;
	
	/**
	 * コンストラクタ
	 */
	private function __construct() {
		if (is_null(self::$instance)) {
			import('Mobile-Detect-2.8.11/Mobile_Detect', 'vendor');
			$this->detect = new \Mobile_Detect();
		}
	}
	
	/**
	 * PCか否かを返す
	 */
	public static function is_pc() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		
		return !self::$instance->detect->isMobile() && !self::$instance->detect->isTablet();
	}
}