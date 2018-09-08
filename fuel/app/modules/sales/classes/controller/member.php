<?php
namespace Sales;
use Fuel\Core\Response;
use Fuel\Core\HttpServerErrorException;
use Auth\Auth;
use Fuel\Core\Session;
use Fuel\Core\Config;
/**
 * [代理発注]発注者選択コントローラクラス
 */
class Controller_Member extends Controller_Base {

	/**
	 * テンプレート
	 */
	public $template = 'layout/none';

	/**
	 * ページタイトル
	 */
	protected $title = '発注者選択';

	/**
	 * 発注者選択画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 発注者選択画面-発注者選択
	 *
	 * @param string $code 取引先コード
	 */
	public function action_select($code) {
		$member = $this->get_member_data($code);
		if (empty($member)) {
			Response::redirect('/sales/login/logout');
		}

		$auth_key = \Common_Util::random_string(50);
		if (!$this->insert_auth_agency($this->get_member_id(), $code, $auth_key)) {
			throw new HttpServerErrorException();
		}

		Response::redirect('/order/agency/auth/' . $auth_key);
	}

	/**
	 * 発注者選択画面-認証処理
	 *
	 * @param string $auth_key 認証キー
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

		if (!$this->delete_auth_agency($auth_agency)) {
			throw new HttpServerErrorException();
		}

		Auth::force_login($sales_representative->id);
		Session::delete(SESSION_KEY_SALES);

		Response::redirect('/sales/section');
	}

	/**
	 * 発注者を取得する
	 *
	 * @param string $member_code 発注者コード
	 */
	private function get_member_data($member_code) {
		return \Model_Member::query()
			->where('code', '=', $member_code)
			->get_one();
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
	 * 代理発注ログインキーを更新する
	 *
	 * @param int $sales_representative_id 営業担当アカウントID
	 * @param string $member_code 発注者コード
	 * @param string $auth_key 認証キー
	 */
	private function insert_auth_agency($sales_representative_id, $member_code, $auth_key) {
		$values = array();
		$values['sales_representative_id'] = $sales_representative_id;
		$values['member_code'] = $member_code;
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