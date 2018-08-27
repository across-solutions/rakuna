<?php
use Fuel\Core\File;
use Fuel\Core\Config;
use Fuel\Core\Asset;
/**
 * お知らせ画像管理クラス
 */
class Image_Notice {
	
	/**
	 * お知らせ画像を表示する
	 * 
	 * @param int $notice_id お知らせID
	 * @param array $attr 属性
	 * @param string $group グループ
	 */
	public static function img($notice_id, $attr = array(), $group = null) {
		$name = $notice_id . '.' . Config::get('upload.setting.notice_image.extension');
		$file = NOTICE_IMAGE_PATH . $name;
		if (!File::exists($file)) {
			return '';
		}
		return Asset::img('notice/' . $name, $attr, $group);
	}
	
	/**
	 * お知らせ画像パスを取得する
	 *
	 * @param int $notice_id お知らせID
	 */
	public static function path($notice_id) {
		$name = $notice_id . '.' . Config::get('upload.setting.notice_image.extension');
		return NOTICE_IMAGE_PATH . $name;
	}
	
	/**
	 * お知らせ画像存在チェック
	 * 
	 * @param int $notice_id お知らせID
	 */
	public static function exist($notice_id) {
		return File::exists(self::path($notice_id));
	}
	
	/**
	 * お知らせ画像ファイルを生成する
	 * 
	 * @param int $notice_id お知らせID
	 * @param array $file ファイル情報
	 */
	public static function create($notice_id, $file) {
		return File::rename($file['tmp_name'], NOTICE_IMAGE_PATH . $notice_id . '.' . Config::get('upload.setting.notice_image.extension'));
	}
	
	/**
	 * お知らせ画像を削除する
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