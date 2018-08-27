<?php
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Fuel\Core\Request;
/**
 * ログクラス
 */
class Common_Logger {
	
	/**
	 * インスタンス
	 */
	private static $instance = null;
	
	/**
	 * ロガー
	 */
	private $logger = null;
	
	/**
	 * コンストラクタ
	 */
	private function __construct() {
		$key = '';
		$request = Request::active();
		if (!empty($request)) {
			$key = Request::active()->controller . '.' . Request::active()->action;
		}
		$this->logger = new Logger($key);
		$this->logger->pushHandler(new RotatingFileHandler(APPPATH . 'logs/application.log', 180, Logger::INFO));
	}
	
	/**
	 * INFOログを出力する
	 *
	 * @param string $message メッセージ
	 */
	public static function info($message) {
		self::log(Logger::INFO, $message);
	}
	
	/**
	 * WARNINGログを出力する
	 *
	 * @param string $message メッセージ
	 */
	public static function warning($message) {
		self::log(Logger::WARNING, $message);
	}
	
	/**
	 * ERRORログを出力する
	 *
	 * @param string $message メッセージ
	 */
	public static function error($message) {
		self::log(Logger::ERROR, $message);
	}
	
	/**
	 * ログを出力する
	 *
	 * @param int $level ログレベル
	 * @param string $message メッセージ
	 */
	private static function log($level, $message) {
		if (is_null(self::$instance)) {
			self::$instance = new Common_Logger();
		}
		
		if (is_array($message)) {
			$message = print_r($message, true);
		}
		self::$instance->logger->log($level, $message);
	}
}