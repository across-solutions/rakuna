<?php
namespace Manage;

use Fuel\Core\Response;
use Fuel\Core\Presenter;
use Fuel\Core\Input;
use Fuel\Core\Validation;
use Auth\Auth;
use Fuel\Core\Cookie;
use Fuel\Core\Arr;
use Fuel\Core\Config;
/**
 * ログインコントローラクラス
 */
class Controller_Login extends Controller_Base {

	/**
	 * テンプレート
	 */
	public $template = 'layout/login';

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
		$auto_login_key = Cookie::get(COOKIE_KEY_MANAGE_AUTO_LOGIN);
		if (!empty($auto_login_key)) {
			$user = $this->get_user($auto_login_key);
			if (empty($user)) {
				Cookie::delete(COOKIE_KEY_MANAGE_AUTO_LOGIN);
			} else {
				$auto_login_key = $this->update_auto_login($user);
				if (!empty($auto_login_key)) {
					Cookie::set(COOKIE_KEY_MANAGE_AUTO_LOGIN, $auto_login_key, COOKIE_EXPIRATION_AUTO_LOGIN);
				}
				Auth::force_login($user->id);
				Response::redirect('/manage/order');
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
		$user = \Model_User::find($this->get_user_id());
		if ($auto_login) {
			$auto_login_key = $this->update_auto_login($user);
			if (!empty($auto_login_key)) {;
				Cookie::set(COOKIE_KEY_MANAGE_AUTO_LOGIN, $auto_login_key, COOKIE_EXPIRATION_AUTO_LOGIN);
			}
		} else {
			$this->remove_auto_login($user);
			Cookie::delete(COOKIE_KEY_MANAGE_AUTO_LOGIN);
		}
		
		Response::redirect('/manage/order');
	}

	/**
	 * ログアウト
	 */
	public function action_logout() {
		$user = \Model_User::find($this->get_user_id());
		if (!empty($user)) {
			$this->remove_auto_login($user);
		}
		Cookie::delete(COOKIE_KEY_MANAGE_AUTO_LOGIN);
		
		Auth::logout();
		Response::redirect('/manage/login');
	}

	/**
	 * ログインバリデート
	 * @param $data フォームデータ
	 */
	private function validate_login($data) {
		$validation = Validation::forge();

		$validation->add('username', 'ログインID')
			->add_rule('required');
		$validation->add('password', 'パスワード')
			->add_rule('required');
		
		return $this->validate($validation, $data);
	}
	
	private function get_user($auto_login_key) {
		return \Model_User::query()
			->where('auto_login_key', $auto_login_key)
			->where('auto_login_updatetime', '>', date('Y-m-d H:i:s', strtotime('-' . COOKIE_EXPIRATION_AUTO_LOGIN . ' second')))
			->where('status', Config::get('define.user_status.enable'))
			->get_one();
	}
	
	private function update_auto_login($user) {
		$auto_login_key = \Common_Util::random_string(50);
		
		$user->auto_login_key = $auto_login_key;
		$user->auto_login_updatetime = date('Y-m-d H:i:s');
		
		if ($user->save() === false) {
			return false;
		}
		return $auto_login_key;
	}
	
	private function remove_auto_login($user) {
		$user->auto_login_key = null;
		$user->auto_login_updatetime = null;
		
		return $user->save() !== false;
	}
}
