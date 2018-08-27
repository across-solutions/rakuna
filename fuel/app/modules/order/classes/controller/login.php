<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\Validation;
use Auth\Auth;
use Fuel\Core\Response;
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\Arr;
use Fuel\Core\Cookie;
use Fuel\Core\DB;

/**
 * [発注]ログインコントローラクラス
 */
class Controller_Login extends Controller_Base {

	/**
	 * テンプレート
	 */
	public $template = 'layout/none';
	
	/**
	 * ページタイトル
	 */
	protected $title = 'ログイン';
	
	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = false;
	
	/**
	 * 初期表示
	 */
	public function action_index() {
		$auto_login_key = Cookie::get(COOKIE_KEY_ORDER_AUTO_LOGIN);
		if (!empty($auto_login_key)) {
			$member = $this->get_member($auto_login_key);
			if (empty($member)) {
				Cookie::delete(COOKIE_KEY_ORDER_AUTO_LOGIN);
			} else {
				$auto_login_key = $this->update_auto_login($member);
				if (!empty($auto_login_key)) {;
					Cookie::set(COOKIE_KEY_ORDER_AUTO_LOGIN, $auto_login_key, COOKIE_EXPIRATION_AUTO_LOGIN);
				}
				Auth::force_login($member->id);
				Response::redirect('/order/home');
			}
		}
		
		$this->render();
	}

	/**
	 * ログイン処理
	 */
	public function action_login() {
		$data = Input::post();
		if (!$this->validate_login($data)) {
			$this->render($data, 'login/index');
			return;
		}
		
		$auth = Auth::instance();
		$username = Arr::get($data, 'username');
		$password = Arr::get($data, 'password');
		if (!$auth->login($username, $password)) {
			$this->set_error_message('ログインID、または、パスワードが違います');
			$this->render($data, 'login/index');
			return;
		}

		$auto_login = Arr::get($data, 'auto_login');
		$member = \Model_Member::find($this->get_member_id());
		if ($auto_login) {
			$auto_login_key = $this->update_auto_login($member);
			if (!empty($auto_login_key)) {;
				Cookie::set(COOKIE_KEY_ORDER_AUTO_LOGIN, $auto_login_key, COOKIE_EXPIRATION_AUTO_LOGIN);
			}
		} else {
			$this->remove_auto_login($member);
			Cookie::delete(COOKIE_KEY_ORDER_AUTO_LOGIN);
		}
		
		Response::redirect('/order/home');
	}
	
	/**
	 * ログアウト
	 */
	public function action_logout() {
		$member = \Model_Member::find($this->get_member_id());
		if (!empty($member)) {
			$this->remove_auto_login($member);
		}
		Cookie::delete(COOKIE_KEY_ORDER_AUTO_LOGIN);
		
		Auth::logout();
		Response::redirect('/order/login');
	}

	/**
	 * ログインバリデート
	 * 
	 * @param array $data フォームデータ
	 */
	private function validate_login($data) {
		$validation = Validation::forge();
	
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10);
		$validation->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);
	
		return $this->validate($validation);
	}
	
	/**
	 * 発注者アカウントを取得する
	 * 
	 * @param string $auto_login_key 自動ログインキー
	 */
	private function get_member($auto_login_key) {
		return \Model_Member::query()
			->where('auto_login_key', $auto_login_key)
			->where('auto_login_updatetime', '>', date('Y-m-d H:i:s', strtotime('-' . COOKIE_EXPIRATION_AUTO_LOGIN . ' second')))
			->where('status', Config::get('define.member_status.enable'))
			->get_one();
	}
	
	/**
	 * 自動ログインキーを更新する
	 * 
	 * @param Model_Member $member 発注者アカウント
	 */
	private function update_auto_login($member) {
		$auto_login_key = \Common_Util::random_string(50);

		$member->auto_login_key = $auto_login_key;
		$member->auto_login_updatetime = date('Y-m-d H:i:s');
		
		if ($member->save() === false) {
			return false;
		}
		return $auto_login_key;
	}
	
	/**
	 * 自動ログインキーを削除する
	 * 
	 * @param Model_Member $member 発注者アカウント
	 */
	private function remove_auto_login($member) {
		$member->auto_login_key = null;
		$member->auto_login_updatetime = null;
		
		return $member->save() !== false;
	}
}