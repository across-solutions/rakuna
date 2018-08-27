<?php
use Fuel\Core\Asset;
use Fuel\Core\File;
use Fuel\Core\Config;
/**
 * 画像管理クラス
 */
class Image_Logo {

	/**
	 * 発注側ロゴ画像を表示する
	 *
	 */
	public static function img_order_base_logo($images = array(), $attr = array(), $group = NULL) {
		$file = LOGO_IMAGE_PATH . 'order_logo.png';
		if (!File::exists($file)) {
			return Asset::img(DEFAULT_ORDER_LOGO_IMAGE_URL, $attr, $group);
		}
		return Asset::img(ORDER_LOGO_IMAGE_URL, $attr, $group);
	}

	/**
	 * 発注側ログインロゴ画像を表示する
	 *
	 */
	public static function img_order_login_logo($images = array(), $attr = array(), $group = NULL) {
		$file = ORDER_LOGIN_LOGO_IMAGE_PATH . 'order_login_logo.png';
		if (!File::exists($file)) {
			return Asset::img(DEFAULT_ORDER_LOGIN_LOGO_IMAGE_URL, $attr, $group);
		}
		return Asset::img(ORDER_LOGIN_LOGO_IMAGE_URL, $attr, $group);
	}

	/**
	 * NoImage画像を表示する
	 *
	 */
	public static function item_noimage_logo($images = array(), $attr = array(), $group = NULL) {
		$file = NO_IMAGE_PATH . 'noimage.jpg';
		if (!File::exists($file)) {
			return Asset::img(DEFAULT_NO_IMAGE_URL, $attr, $group);
		}
		return Asset::img(NO_IMAGE_URL, $attr, $group);
	}

	/**
	 * 画像を削除する
	 *
	 */
	public static function remove($file) {
		if (!File::exists($file)) {
			return true;
		}
		return File::delete($file);
	}
}