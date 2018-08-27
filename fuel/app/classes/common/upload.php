<?php

use Fuel\Upload\Upload;
use Fuel\Core\Config;
use Fuel\Core\Lang;
use Fuel\Core\File;

/**
 * ファイルアップロードクラス
 */
class Common_Upload {
	
	private static $instance = null;
	
	/**
	 * アップロードファイルリスト
	 */
	private $files = array();
	
	/**
	 * コンストラクタ
	 */
	private function __construct() {
		Lang::load('upload', true);
		$config = Config::load('upload', true);
		$config['langCallback'] = '\\Common_Upload::lang_callback';

		$upload = new Upload($config);
		$upload->processFiles();

		foreach ($upload->getAllFiles() as $file) {
			$this->files[$file->element] = $file;
			
			$file->setConfig(Config::get('upload.setting.' . $file->element));
			$file->validate();
		}
	}
	
	/**
	 * インスタンスを取得する
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * コールバック処理
	 * 
	 * @param string $error エラーコード
	 */
	public static function lang_callback($error) {
		return Lang::get('upload.error_'.$error, array(), '');
	}
	
	/**
	 * 妥当か否かを返す
	 * 
	 * @param string $field フィールド名
	 */
	public function is_valid($field) {
		if (!isset($this->files[$field])) {
			return false;
		}
		return $this->files[$field]->isValid();
	}
	
	/**
	 * ファイルを取得する
	 * 
	 * @param string $field フィールド名
	 */
	public function get_file($field) {
		if (!isset($this->files[$field])) {
			return null;
		}
		return $this->files[$field];
	}
	
	/**
	 * エラーメッセージを取得する
	 * 
	 * @param string $field フィールド名
	 */
	public function get_errors($field) {
		$file = $this->get_file($field);
		if (is_null($file)) {
			return array();
		}

		$errors = array();
		foreach ($file->getErrors() as $error) {
			$errors[$error->getError()] = $this->replace_message($error->getMessage(), $field);
		}
		return $errors;
	}
	
	/**
	 * ファイルを出力する
	 * 
	 * @param string $field フィールド名
	 * @param string $sub_dir サブディレクトリ名
	 * @param string $file_name ファイル名
	 */
	public function output($field, $sub_dir, $file_name) {
		$file = $this->get_file($field);
		if (empty($file['tmp_name'])) {
			return false;
		}
		
		$base_path = Config::get('upload.setting.' . $field . '.path');
		if (empty($base_path)) {
			return false;
		}
		
		if (!file_exists($base_path . $sub_dir)) {
			if (!File::create_dir($base_path, $sub_dir)) {
				return false;
			}
		}
		
		$extension = Config::get('upload.setting.' . $field . '.extension');
		if (!is_null($extension) && strpos($file_name, '.') === false) {
			$file_name .= '.' . $extension;
		}
		
		return File::rename($file['tmp_name'], $base_path . $sub_dir . DS . $file_name);
	}
	
	/**
	 * メッセージ中の置換文字列を置換する
	 * 
	 * @param string $message メッセージ
	 * @param string $field フィールド名
	 */
	private function replace_message($message, $field) {
		$replaces = Config::get('upload.message_replaces.' . $field);
		foreach ($replaces as $key => $value) {
			$message = str_replace(':' . $key, $value, $message);
		}
		return $message;
	}
}