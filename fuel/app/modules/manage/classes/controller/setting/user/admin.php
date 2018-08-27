<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
/**
 * ユーザ設定-管理者設定コントローラクラス
 */
class Controller_Setting_User_Admin extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'ユーザ設定-管理者設定';

	/**
	 * 管理者設定画面-初期表示
	 */
	public function action_index() {
		$data = \Model_Setting::find('first');
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}

		$this->render($data);
	}

	/**
	 * 管理者設定画面-保存処理
	 */
	public function action_save() {
		$data = Input::post();

		$setting = \Model_Setting::find('first');
		$user = \Model_User::find($this->get_user_id());
		if (empty($setting)) {
			throw new \HttpServerErrorException();
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/user/admin/index');
			return;
		}

		if (!$this->update_setting($setting, $data)) {
			throw new \HttpServerErrorException();
		}

		if (!$this->update_user($user, $data)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/user/admin');
	}

	/**
	 * 更新バリデート処理
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('administrator_name', '管理者名')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('corporation_name', '管理企業名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * システム設定を更新する
	 *
	 * @param Model_Setting $setting 元データ
	 * @param array $data フォームデータ
	 */
	private function update_setting($setting, $data) {
		$setting->administrator_name = $data['administrator_name'];
		$setting->corporation_name = $data['corporation_name'];

		return $setting->save() !== false;
	}

	/**
	 * 管理者アカウントの名前を変更する
	 *
	 * @param Model_User $user 元データ
	 * @param array $data フォームデータ
	 */
	private function update_user($user, $data) {

		if ($user->name == $data['administrator_name']) {
			return true;
		}

		$user->name = $data['administrator_name'];

		return $user->save() !== false;
	}
}