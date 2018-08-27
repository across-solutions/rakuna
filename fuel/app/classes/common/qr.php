<?php
/**
 * QRコード管理クラス
 */
class Common_Qr {
	
	private $qr;
	
	/**
	 * コンストラクタ
	 */
	public function __construct() {
		import('Image_QRCode-0.1.3/Image/QRCode', 'vendor');
		$this->qr = new \Image_QRCode();
	}
	
	/**
	 * インスタンス生成
	 */
	public static function forge() {
		return new static();
	}

	public function output($output, $file_name, $message) {
		$file = $this->qr->makeCode($message, array('output_type' => 'return', 'module_size' => 6));
		imagepng($file, $output . $file_name . '.png');
	}
}