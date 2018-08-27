<?php
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\Date;
use Fuel\Core\Session;
use Fuel\Core\Input;
use Auth\Auth;
use Auth\Auth_Login_Driver;

/**
 * ログイン認証
 */
class Auth_Login_Mosauth extends Auth_Login_Driver {

	/**
	 * 初期化処理
	 */
	public static function _init() {
		Config::load('mosauth', true);
		if (self::get_config_value('remember_me.enabled', false)) {
			static::$remember_me = Session::forge(array(
				'driver' => 'cookie',
				'cookie' => array(
					'cookie_name' => self::get_config_value('remember_me.cookie_name', 'rmcookie'),
				),
				'encrypt_cookie' => true,
				'expire_on_close' => false,
				'expiration_time' => self::get_config_value('remember_me.expiration', 86400 * 31),
			));
		}
	}

	/**
	 * ログインユーザ情報
	 */
	protected $user = null;

	/**
	 * @see \Auth\Auth_Login_Driver::hash_password()
	 */
	public function hash_password($password) {
		return $password;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::perform_check()
	 */
	protected function perform_check() {
		$username    = Session::get('username');
		$login_hash  = Session::get('login_hash');

		if (!empty($username) and ! empty($login_hash)) {
			if (is_null($this->user) or ($this->user['username'] != $username)) {
				$this->user = $this->get_user_from_username($username);
			}

			if ($this->user and (self::get_config_value('multiple_logins', false) or $this->user['login_hash'] === $login_hash)) {
				return true;
			}
		} elseif (static::$remember_me and $user_id = static::$remember_me->get('user_id', null)) {
			return $this->force_login($user_id);
		}

		$this->clear_session();

		return false;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::validate_user()
	 */
	public function validate_user($username = '', $password = '') {
		$username = trim($username) ?: trim(Input::post(self::get_config_value('username_post_key', 'username')));
		$password = trim($password) ?: trim(Input::post(self::get_config_value('password_post_key', 'password')));
		if (empty($username) or empty($password)) {
			return false;
		}

		$password = $this->hash_password($password);

		$user = $this->get_user_from_username_password($username, $password);
		if (empty($user)) {
			return false;
		}
		return $user;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::login()
	 */
	public function login($username = '', $password = '') {
		if (!($this->user = $this->validate_user($username, $password))) {
			$this->clear_session();
			return false;
		}

		Auth::_register_verified($this);

		Session::set('username', $this->user['username']);
		Session::set('login_hash', $this->create_login_hash());
		Session::set('login_info', $this->user);
		Session::instance()->rotate();
		return true;
	}
	/**
	 * @see \Auth\Auth_Login_Driver::force_login()
	 */
	public function force_login($user_id = '') {
		if (empty($user_id)) {
			return false;
		}

		$this->user = $this->get_user($user_id);
		if ($this->user == false) {
			$this->clear_session();
			return false;
		}

		Session::set('username', $this->user['username']);
		Session::set('login_hash', $this->create_login_hash());
		Session::set('login_info', $this->user);
		return true;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::logout()
	 */
	public function logout() {
		$this->clear_session();
		return true;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::get_user_id()
	 */
	public function get_user_id() {
		if (empty($this->user)) {
			return false;
		}

		return array($this->id, (int) $this->user['id']);
	}

	/**
	 * @see \Auth\Auth_Login_Driver::get_groups()
	 */
	public function get_groups() {

		if (empty($this->user)) {
			return false;
		}

		return array(array($this->user['mosgroup']));
	}

	/**
	 * Verify Acl access
	 *
	 * @see \Auth\Auth_Login_Driver::has_access($condition, $driver = null, $entity = null)
	 * @param string $condition そのページが求めている権限
	 * @param string $user_mosgroup ログイン中の管理側ユーザの所属グループ
	 */
	public function has_access($condition, $user_mosgroup = null, $driver = null )
	{
		//$groupsには、今MOSの中で定義されているグループ→権限(role)の配列が入る。
		$groups = self::get_config_value('groups', array('*'));

		if ( empty($condition) || empty($groups) ) {
			return false;
		}

		//$current_rolesに、指定の$mosgroupグループの持つ権限(role。userとかadminとか)を割り出す
		$current_roles  = $this->get_roles($groups, $user_mosgroup);
		if ( empty($current_roles) ){
			return false;
		}

		//指定の$mosgroupグループで許可されたラベルを格納する配列
		$current_allowed_labels = array();

		//$rolesは、MOSの各権限とラベルの割当の配列
		$roles = \Config::get('mosauth.roles', array());

		foreach ($current_roles as $r_role) {
			//$r_roleには、今のmosgroupの持っている権限、ラベルが入る。
			$current_allowed_labels[] = $roles[$r_role];
		}

		//$conditionはページが求める権限。$conditionが$current_allowed_labelsにあるかどうか検索する。
		if ( ! in_array($condition, $current_allowed_labels )) {
			return false;
		}

		// 最終的に権限があった
		return true;
	}

	/**
	 * @see \Auth\Auth_Login_Driver::get_email()
	 */
	public function get_email() {
		if (empty($this->user)) {
			return false;
		}

		return $this->user['email'];
	}

	/**
	 * @see \Auth\Auth_Login_Driver::get_screen_name()
	 */
	public function get_screen_name() {
		if (empty($this->user)) {
			return false;
		}

		return $this->user['name'];
	}

	/**
	 * 管理側ユーザの所属するグループを返す
	 */
	public function get_user_mosgroup(){
		return \Model_User::find(Auth::instance()->get_user_id()[1])->mosgroup;
	}

	/**
	 * ログインチェック用ハッシュ値を生成する
	 */
	private function create_login_hash() {
		if (empty($this->user)) {
			throw new \SimpleUserUpdateException('User not logged in, can\'t create login hash.', 10);
		}

		$last_login = date('Y-m-d H:i:s');
		$login_hash = sha1(self::get_config_value('login_hash_salt').$this->user['username'].strtotime($last_login));

		$this->update_login_hash($login_hash, $last_login);
		$this->user['login_hash'] = $login_hash;

		return $login_hash;
	}

	/**
	 * 設定値を取得する
	 *
	 * @param string $key キー
	 * @param string $default デフォルト値
	 */
	private static function get_config_value($key, $default = null) {
		return Config::get('mosauth.' . $key, $default);
	}

	/**
	 * ログインユーザ情報を取得する
	 *
	 * @param int $id ID
	 */
	private function get_user($id) {
		$user = DB::select_array(self::get_config_value('table_columns', array('*')))
			->where('id', '=', $id)
			->where('status', '=', Config::get('define.member_status.enable'))
			->where('del_flg', '=', UNDELETED)
			->from(self::get_config_value('table_name'))
			->execute(self::get_config_value('db_connection'))->current();

		return $user;
	}

	/**
	 * ログインユーザ情報を取得する
	 *
	 * @param string $username ログインユーザ名
	 */
	private function get_user_from_username($username) {
		$user = DB::select_array(self::get_config_value('table_columns', array('*')))
			->where('username', '=', $username)
			->where('status', '=', Config::get('define.member_status.enable'))
			->where('del_flg', '=', UNDELETED)
			->from(self::get_config_value('table_name'))
			->execute(self::get_config_value('db_connection'))->current();

		return $user;
	}

	/**
	 * ユーザ情報を取得する
	 *
	 * @param string $username ログインユーザ名
	 * @param string $password ログインパスワード
	 */
	private function get_user_from_username_password($username, $password) {
		$user = DB::select_array(self::get_config_value('table_columns', array('*')))
			->where('username', '=', $username)
			->where('password', '=', $password)
			->where('status', '=', Config::get('define.member_status.enable'))
			->where('del_flg', '=', UNDELETED)
			->from(self::get_config_value('table_name'))
			->execute(self::get_config_value('db_connection'))->current();

		return $user;
	}

	/**
	 * ログインハッシュを更新する
	 *
	 * @param string $login_hash ログインハッシュ
	 * @param Date $last_login 最終ログイン日時
	 */
	private function update_login_hash($login_hash, $last_login) {
		return DB::update(self::get_config_value('table_name'))
			->set(array('last_login' => $last_login, 'login_hash' => $login_hash))
			->where('username', '=', $this->user['username'])
			->execute(self::get_config_value('db_connection'));
	}

	/**
	 * セッション情報をクリアする
	 */
	private function clear_session() {
		$this->user = false;
		Session::delete('username');
		Session::delete('login_hash');
		Session::delete('login_info');
	}

	/**
	 * グループの持つ権限を返す
	 *
	 * @param array $groups 定義されているグループと権限の割当
	 * @param string $user_mosgroup 管理側ユーザの所属グループ
	 */
	private function get_roles( $groups, $user_mosgroup )
	{
		foreach ( $groups as $value ) {
			if ( $value['name'] === $user_mosgroup ) {
				return $value['roles'];
			}
		}

		return false;
	}
}