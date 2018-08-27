<?php
use Fuel\Core\File;
use Fuel\Core\Asset;
/**
 * PR商品誘導バナー画像管理クラス
 */
class Image_Pr {

	/**
	 * PR商品誘導バナー画像を表示する
	 *
	 * @param string $name ファイル名
	 * @param array $attr 属性
	 * @param string $group グループ
	 */
	public static function img($name, $attr = array(), $group = null) {
		$file = PR_IMAGE_PATH . $name;
		if (!File::exists($file)) {
			return '';
		}
		return Asset::img('pr/' . $name, $attr, $group);
	}
	
	/**
	 * PR商品誘導バナー画像パスを取得する
	 * 
	 * @param string $name ファイル名
	 */
	public static function path($name) {
		return PR_IMAGE_PATH . $name;
	}
	
	/**
	 * PR商品誘導バナー画像を表示する
	 * 
	 * @param array $file ファイル情報
	 */
	public static function create($file) {
		return File::rename($file['tmp_name'], PR_IMAGE_PATH . $file['name']);
	}
	
	/**
	 * PR商品誘導バナー画像を削除する
	 * 
	 * @param string $ignore_name 除外ファイル名
	 */
	public static function remove($ignore_name) {
		$names = File::read_dir(PR_IMAGE_PATH);
		foreach ($names as $name) {
			if ($name != $ignore_name) {
				if (!File::delete(self::path($name))) {
					return false;
				}
			}
		}
		return true;
	}
}