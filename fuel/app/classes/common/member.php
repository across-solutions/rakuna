<?php
use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\Session;
/**
 * [代理発注]ログインユーザ情報取得クラス
 */
class Common_Member {

	/**
	 * 代理発注セッション用キーリスト
	 */
	private static $SESSION_SALES_REPRESENTATIVE_FIELDS = array('id', 'sales_person_code', 'sales_person_name');

	/**
	 * ログインユーザIDを取得する
	*/
	public static function get_member_id() {
		return Auth::instance()->get_user_id()[1];
	}

	/**
	 * ログインユーザコードを取得する
	 */
	public static function get_member_code() {
		return self::get_member('code');
	}

	/**
	 * ログインユーザ情報を取得する
	 *
	 * @param string $field フィールド名
	 */
	public static function get_member($field) {
		$member = Session::get('login_info');
		if (empty($member)) {
			return null;
		}
		return Arr::get($member, $field);
	}

	/**
	 * 代理発注可否を返す
	 */
	public static function is_agency() {
		$sales_representative = Session::get(SESSION_KEY_SALES);

		return !empty($sales_representative);
	}

	/**
	 * 代理発注営業担当者アカウントIDを取得する
	 */
	public static function get_agency_id() {
		$sales_representative_id = self::get_agency('id');
		if (empty($sales_representative_id)) {
			return 0;
		}

		return $sales_representative_id;
	}

	/**
	 * 代理発注営業担当者アカウントコードを取得する
	 */
	public static function get_agency_code() {
		$sales_representative_code = self::get_agency('sales_person_code');
		if (empty($sales_representative_code)) {
			return '';
		}

		return $sales_representative_code;
	}

	/**
	 * 代理発注営業担当者アカウント名を取得する
	 */
	public static function get_agency_name() {
		$sales_representative_name = self::get_agency('sales_person_name');
		if (empty($sales_representative_name)) {
			return '';
		}

		return $sales_representative_name;
	}

	/**
	 * 代理発注中の営業担当者情報を取得する
	 *
	 * @param string $field フィールド名
	 */
	public static function get_agency($field) {
		$sales_representative= Session::get(SESSION_KEY_SALES);
		if (empty($sales_representative)) {
			return null;
		}
		return Arr::get($sales_representative, $field);
	}

	/**
	 * 営業担当アカウントからセッション用配列を取得する
	 *
	 * @param Model_Sales_Representative $sales_representative 営業担当アカウント情報
	 */
	public static function filter_sales_representative($sales_representative) {
		return Common_Util::filter($sales_representative, self::$SESSION_SALES_REPRESENTATIVE_FIELDS);
	}
}