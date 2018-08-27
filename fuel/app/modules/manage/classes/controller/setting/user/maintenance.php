<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Cache;
/**
 * ユーザ設定-メンテナンス設定コントローラクラス
 */
class Controller_Setting_User_Maintenance extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'ユーザ設定-メンテナンス設定';

	/**
	 * システム設定画面-初期表示
	 */
	public function action_index() {
		$data = \Model_Setting::find('first');
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}

		$this->render($data);
	}

	/**
	 * システム設定画面-保存処理
	 */
	public function action_save() {
		$data = Input::post();

		$setting = \Model_Setting::find('first');
		if (empty($setting)) {
			throw new \HttpServerErrorException();
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/user/maintenance/index');
			return;
		}

		if (!$this->update_setting($setting, $data)) {
			throw new \HttpServerErrorException();
		}

		Cache::delete(CACHE_KEY_MAINTENANCE_FLG);

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/user/maintenance');
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('maintenance_flg', 'メンテナンス表示')
			->add_rule('required')
			->add_rule('exist_in_array', array(DISPLAY, NON_DISPLAY));

		return $this->validate($validation, $data);
	}

	/**
	 * システム設定を更新する
	 *
	 * @param Model_Setting $setting 元データ
	 * @param array $data フォームデータ
	 */
	private function update_setting($setting, $data) {
		$setting->maintenance_flg = $data['maintenance_flg'];

		return $setting->save() !== false;
	}
}