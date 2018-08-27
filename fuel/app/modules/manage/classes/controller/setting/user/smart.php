<?php
namespace Manage;
use Fuel\Core\Response;
use Fuel\Core\File;
/**
 * ユーザ設定-端末設定コントローラクラス
 */
class Controller_Setting_User_Smart extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'ユーザ設定-端末設定';

	/**
	 * 端末設定画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 端末設定画面-保存処理
	 */
	public function action_save() {
		$this->process_upload('webclip_image');

		if (!$this->validate_edit()) {
			$this->render(null, 'setting/user/smart/index');
			return;
		}

		$this->save_upload();

		$apple_touch_icon = WEBCLIP_IMAGE_PATH . 'apple-touch-icon.png';
		$apple_touch_icon_precomposed = WEBCLIP_IMAGE_PATH . 'apple-touch-icon-precomposed.png';

		if (File::exists($apple_touch_icon_precomposed)) {
			File::delete($apple_touch_icon_precomposed);
		}

		File::copy($apple_touch_icon, $apple_touch_icon_precomposed);

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/user/smart');
	}

	/**
	 * 更新バリデート
	 */
	private function validate_edit() {
		return $this->validate_file_upload('webclip_image', true);
	}
}