<?php
use Fuel\Core\Asset;
use Fuel\Core\File;
use Fuel\Core\Config;
/**
 * 商品画像管理クラス
 */
class Image_Item {

	/**
	 * 商品画像を表示する
	 *
	 * @param string $item_code 商品コード
	 * @param array $attr 属性
	 * @param string $group グループ
	 */
	public static function img($item_code, $attr = array(), $group = null) {
		$name = $item_code . '.' . Config::get('upload.setting.item_image.extension');
		$file = ITEM_IMAGE_PATH . $name;
		if (!File::exists($file)) {
			if (!File::exists(NO_IMAGE_PATH . 'noimage.jpg')) {
				return Asset::img(DEFAULT_NO_IMAGE_URL, $attr, $group);
			}
			return Asset::img(NO_IMAGE_URL, $attr, $group);
		}
		return Asset::img('item/' . $name, $attr, $group);
	}

	/**
	 * 画像URLを取得する
	 *
	 * @param string $item_code 商品コード
	 */
	public static function url($item_code) {
		$name = $item_code . '.' . Config::get('upload.setting.item_image.extension');
		$url = Asset::get_file('item/' . $name, 'img');
		if (!empty($url)) {
			return $url;
		}

		return  Asset::get_file(NO_IMAGE_URL, 'img');
	}

	/**
	 * 商品画像パスを取得する
	 *
	 * @param string $item_code 商品コード
	 */
	public static function path($item_code) {
		$name = $item_code . '.' . Config::get('upload.setting.item_image.extension');
		return ITEM_IMAGE_PATH . $name;
	}

	/**
	 * 商品画像存在チェック
	 *
	 * @param string $item_code 商品コード
	 */
	public static function exist($item_code) {
		return File::exists(self::path($item_code));
	}

	/**
	 * 商品画像ファイルを生成する
	 *
	 * @param string $item_code 商品コード
	 * @param array $file ファイル
	 */
	public static function create($item_code, $file) {
		return File::rename($file, ITEM_IMAGE_PATH . $item_code . '.' . Config::get('upload.setting.item_image.extension'));
	}

	/**
	 * 商品画像を削除する
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