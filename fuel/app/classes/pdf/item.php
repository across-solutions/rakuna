<?php
use Fuel\Core\Asset;
use Fuel\Core\File;
use Fuel\Core\Config;
/**
 * PDF管理クラス
 */
class PDF_Item {

	/**
	 * PDFのURLを取得する
	 *
	 * @param string $item_code 商品コード
	 */
	public static function url($item_code) {
		$name = $item_code . '.' . Config::get('upload.setting.item_pdf.extension');
		$url = Asset::get_file('item_pdf/' . $name, 'img');
		$file = self::path($item_code);
		
		if (!empty($url)) {
			return $url."?".filemtime($file);
		}

	}

	/**
	 * PDFパスを取得する
	 *
	 * @param string $item_code 商品コード
	 */
	public static function path($item_code) {
		$name = $item_code . '.' . Config::get('upload.setting.item_pdf.extension');
		return ITEM_PDF_PATH . $name;
	}

	/**
	 * PDF存在チェック
	 *
	 * @param string $item_code 商品コード
	 */
	public static function exist($item_code) {
		return File::exists(self::path($item_code));
	}

	/**
	 * PDFファイルを生成する
	 *
	 * @param string $item_code 商品コード
	 * @param array $file ファイル
	 */
	public static function create($item_code, $file) {
		return File::rename($file, ITEM_PDF_PATH . $item_code . '.' . Config::get('upload.setting.item_pdf.extension'));
	}



	/**
	 * PDFを削除する
	 *
	 * @param string $item_code 商品コード
	 */
	public static function remove($item_code) {
		if (!self::exist($item_code)) {
			return true;
		}
		return File::delete(self::path($item_code));
	}
}