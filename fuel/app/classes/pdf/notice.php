<?php
use Fuel\Core\File;
use Fuel\Core\Config;
use Fuel\Core\Asset;
/**
 * お知らせPDF管理クラス
 */
class Pdf_Notice {

	/**
	 * お知らせPDFを表示する
	 *
	 * @param int $notice_id お知らせID
	 * @param array $attr 属性
	 * @param string $group グループ
	 */
	public static function pdf($notice_id, $attr = array(), $group = null) {
		$name = $notice_id . '.' . Config::get('upload.setting.notice_pdf.extension');
		$file = NOTICE_PDF_PATH . $name;
		if (!File::exists($file)) {
			return '';
		}
		return $file;
	}

	/**
	 * お知らせPDFファイルURLを取得する
	 *
	 * @param int $notice_id お知らせID
	 */
	public static function url($notice_id) {
		$name = $notice_id . '.' . Config::get('upload.setting.notice_pdf.extension');
		$url = Asset::get_file('notice_pdf/' . $name, 'img' );
		$file = NOTICE_PDF_PATH . $name;
		if (!empty($url)) {
			return $url."?".filemtime($file);
		}

		return $url;
	}


	/**
	 * お知らせPDFパスを取得する
	 *
	 * @param int $notice_id お知らせID
	 */
	public static function path($notice_id) {
		$name = $notice_id . '.' . Config::get('upload.setting.notice_pdf.extension');
		return NOTICE_PDF_PATH . $name;
	}

	/**
	 * お知らせPDF存在チェック
	 *
	 * @param int $notice_id お知らせID
	 */
	public static function exist($notice_id) {
		return File::exists(self::path($notice_id));
	}

	/**
	 * お知らせPDFファイルを生成する
	 *
	 * @param int $notice_id お知らせID
	 * @param array $file ファイル情報
	 */
	public static function create($notice_id, $file) {
		return File::rename($file['tmp_name'], NOTICE_PDF_PATH . $notice_id . '.' . Config::get('upload.setting.notice_pdf.extension'));
	}

	/**
	 * お知らせPDFを削除する
	 *
	 * @param int $notice_id お知らせID
	 */
	public static function remove($notice_id) {
		if (!self::exist($notice_id)) {
			return true;
		}
		return File::delete(self::path($notice_id));
	}
}