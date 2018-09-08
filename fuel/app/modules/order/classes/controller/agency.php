<?php
namespace Order;
use Fuel\Core\Response;
use Fuel\Core\Config;
use Auth\Auth;
use Fuel\Core\Session;
use Fuel\Core\HttpServerErrorException;

/**
 * [発注]代理発注コントローラクラス
 */
class Controller_Agency extends Controller_Base {

	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = false;

	/**
	 * テンプレート
	 */
	public $template = 'layout/none';

	/**
	 * 代理発注認証処理
	 *
	 * @param string $auth_key 代理発注認証キー
	 */
	public function action_auth($auth_key = null) {
		$auth_agency = $this->get_auth_agency($auth_key);
		if (empty($auth_agency)) {
			Response::redirect('/sales/login/logout');
		}

		$sales_representative = $this->get_sales_representative($auth_agency->sales_representative_id);
		if (empty($sales_representative)) {
			Response::redirect('/sales/login/logout');
		}

		$member = $this->get_db_member($auth_agency->member_code);
		if (empty($member)) {
			Response::redirect('/sales/login/logout');
		}

		if (!$this->delete_auth_agency($auth_agency)) {
			throw new HttpServerErrorException();
		}

		Auth::force_login($member->id);
		Session::set(SESSION_KEY_SALES, \Common_Member::filter_sales_representative($sales_representative));

		Response::redirect('/order/home');
	}

	/**
	 * 取引先選択に戻る処理
	 */
	public function action_section() {
		$sales_representative_id = \Common_Member::get_agency('id');
		if (empty($sales_representative_id)) {
			Response::redirect('/sales/login/logout');
		}

		$auth_key = \Common_Util::random_string(50);

		if (!$this->insert_auth_agency($sales_representative_id, $auth_key)) {
			throw new HttpServerErrorException();
		}

		Response::redirect('/sales/section/auth/' . $auth_key);
	}

	/**
	 * 代理発注認証を取得する
	 *
	 * @param string $auth_key 代理発注認証キー
	 */
	private function get_auth_agency($auth_key) {
		return \Model_Auth_Agency::query()
			->where('auth_key', '=', $auth_key)
			->get_one();
	}

	/**
	 * 営業担当アカウントを取得する
	 *
	 * @param string $sales_representative_id 営業担当アカウントID
	 */
	private function get_sales_representative($sales_representative_id) {
		return \Model_Sales_Representative::query()
			->where('id', '=', $sales_representative_id)
			->where('status', '=', Config::get('define.member_status.enable'))
			->get_one();
	}

	/**
	 * 発注者を取得する
	 *
	 * @param string $member_code 発注者コード
	 */
	private function get_db_member($member_code) {
		return \Model_Member::query()
			->where('code', '=', $member_code)
			->where('status', '=', Config::get('define.member_status.enable'))
			->get_one();
	}

	/**
	 * 代理発注認証を登録する
	 *
	 * @param int $sales_representative_id 営業担当アカウントID
	 * @param string $auth_key 代理発注ログインキー
	 */
	private function insert_auth_agency($sales_representative_id, $auth_key) {
		$values = array();
		$values['sales_representative_id'] = $sales_representative_id;
		$values['auth_key'] = $auth_key;

		$model = \Model_Auth_Agency::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 代理発注認証を削除する
	 */
	private function delete_auth_agency($auth_agency) {
		$auth_agency->del_flg = DELETED;

		return $auth_agency->save() !== false;
	}
}