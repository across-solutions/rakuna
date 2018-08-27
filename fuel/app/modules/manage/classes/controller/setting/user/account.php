<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Auth\Auth;
/**
 * ユーザ設定-管理者アカウント設定コントローラクラス
 */
class Controller_Setting_User_Account extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = 'ユーザ設定-管理者アカウント設定';
	
	/**
	 * 管理者アカウント設定画面-初期表示
	 */
	public function action_index() {
		$data = \Model_User::find('first');
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}
		
		$this->render($data);
	}
	
	/**
	 * 管理者アカウント設定画面-保存処理
	 */
	public function action_save() {
		$data = Input::post();

		$user = \Model_User::find('first');
		if (empty($user)) {
			throw new \HttpServerErrorException();
		}
		
		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/user/account/index');
			return;
		}
		
		if (!$this->update_user($user, $data)) {
			throw new \HttpServerErrorException();
		}
		
		Auth::force_login($user->id);
		
		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/user/account');
	}
	
	/**
	 * 更新バリデート
	 * 
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();
		
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 20);
		$validation->add('password', 'パスワード')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);
		$validation->add('password_confirm', 'パスワード(再)')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15)
			->add_rule('match_field', 'password');
		
		return $this->validate($validation, $data);
	}
	
	/**
	 * 管理者アカウントを更新する
	 * 
	 * @param Model_User $user 元データ
	 * @param array $data フォームデータ
	 */
	private function update_user($user, $data) {
		$user->username = $data['username'];
		if (!is_null($data['password']) && $data['password'] != '') {
			$user->password = $data['password'];
		}
		
		return $user->save() !== false;
	}
}