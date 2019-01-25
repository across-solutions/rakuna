<?php
use Fuel\Core\Validation;
use Fuel\Core\Str;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Upload\Upload;
use Fuel\Core\Validation_Error;

/**
 * 共通バリデートクラス
 */
class Common_Validation {

	/**
	 * 半角英数チェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_alphanum($value) {
		if (is_null($value) || $value == '') {
			return true;
		}

		if (preg_match('/^[a-zA-Z0-9]+$/', $value)) {
			return true;
		}

		return false;
	}

	/**
	 * 数字ハイフンチェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_numhyphen($value) {
		if (is_null($value) || $value == '') {
			return true;
		}

		if (preg_match('/^[0-9-_]+$/', $value)) {
			return true;
		}

		return false;
	}

	/**
	 * 数値チェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_numeric($value) {
		if (is_null($value) || $value == '') {
			return true;
		}

		return is_numeric($value);
	}

	/**
	 * 全角カタカナチェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_zenkaku_katakana($value) {
		if (is_null($value) || $value == '') {
			return true;
		}

		return preg_match('/^[ァ-ヶー]+$/u', $value ) > 0;
	}


	/**
	 * メールアドレスチェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_simple_email($value) {
		if (is_null($value) || $value == '') {
			return true;
		}

		return preg_match('/^([._a-z0-9-])+@[a-z0-9-]+([\.][a-z0-9-]+)+$/i', $value) > 0;
	}

	/**
	 * 存在チェック
	 *
	 * @param $value チェック対象文字列
	 * @param $table テーブル名
	 * @param $field フィールド名
	 */
	public static function _validation_exist($value, $table, $field) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$result = DB::select($field)
			->where($field, '=', trim($value))
			->where('del_flg', UNDELETED)
			->from($table)
			->execute();

		return $result->count() > 0;
	}

	/**
	 * コード存在チェック
	 *
	 * @param $value チェック対象文字列
	 * @param $key コードキー
	 */
	public static function _validation_exist_code($value, $key) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$config = Config::get($key);

		return array_search($value, $config) !== false;
	}

	/**
	 * コードキー存在チェック
	 *
	 * @param $value チェック対象文字列
	 * @param $key コードキー
	 */
	public static function _validation_exist_code_key($value, $key) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$config = Config::get($key);

		return array_key_exists($value, $config) !== false;
	}

	/**
	 * ユニークチェック
	 *
	 * @param string $value チェック対象文字列
	 * @param string $table テーブル名
	 * @param string $field フィールド名
	 * @param int $id ID
	 * $param array $wheres 検索条件
	 */
	public static function _validation_unique($value, $table, $field, $id = null, $wheres = array()) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$query = DB::select($field)
			->where($field, '=', trim($value))
			->where('id', '!=', $id)
			->where('del_flg', UNDELETED)
			->from($table);
		foreach ($wheres as $where) {
			$query->where($where[0], $where[1], $where[2]);
		}

		$result = $query->execute();

		return $result->count() == 0;
	}

	/**
	 * ユニークチェック
	 *
	 * @param $value チェック対象文字列
	 * @param $table テーブル名
	 * @param $field フィールド名
	 * @param $id ID
	 */
	public static function _validation_unique_ignore_del_flg($value, $table, $field, $id = null) {
		if (is_null($value) || $value == '') {
			return true;
		}

		$result = DB::select($field)
			->where($field, '=', trim($value))
			->where('id', '!=', $id)
			->from($table)
			->execute();

		return $result->count() == 0;
	}

	/**
	 * 検索開始日付終了日付逆転チェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_date_reversal($value) {
		$validate = Validation::instance();

		$start_date_valid_result = $validate->_validation_valid_date( $value[0],  'Y-m-d' );
		$end_date_valid_result = $validate->_validation_valid_date( $value[1],  'Y-m-d' );

		if ( $start_date_valid_result == false || $end_date_valid_result == false ){
			return true;
		}

		$start_datetime = new DateTime($value[0]);
		$end_datetime   = new DateTime($value[1]);

		if ( $start_datetime <= $end_datetime ) {
			return true;
		}

		return false;
	}

	/**
	 * 検索開始日付終了日付期間チェック
	 *
	 * @param $value チェック対象文字列
	 */
	public static function _validation_date_interval($value, $days) {
		$validate = Validation::instance();

		$start_date_valid_result = $validate->_validation_valid_date( $value[0],  'Y-m-d' );
		$end_date_valid_result = $validate->_validation_valid_date( $value[1],  'Y-m-d' );

		if ( $start_date_valid_result == false || $end_date_valid_result == false ){
			return true;
		}

		$start_datetime = new DateTime($value[0]);
		$end_datetime   = new DateTime($value[1]);

		$interval_days = $start_datetime->diff($end_datetime)->format('%R%a');

		if ( $interval_days <= $days ) {
			return true;
		}

		return false;
	}

	/**
	 * 存在チェック - 配列
	 *
	 * @param $value チェック対象文字列
	 * @param $array
	 */
	public static function _validation_exist_in_array($value, $array) {
		foreach ($array as $comparison) {
			if ($comparison === $value) {
				return true;
			}
		}

		return false;
	}
}