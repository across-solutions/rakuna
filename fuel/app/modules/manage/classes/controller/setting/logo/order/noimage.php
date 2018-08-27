<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\DB;
use Fuel\Core\Upload;
use Fuel\Core\Config;
/**
 * ロゴ設定-発注画面NoImage画像設定コントローラクラス
 */
class Controller_Setting_Logo_Order_Noimage extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'ロゴ設定-発注画面NoImage画像設定';

	/**
	 * 発注画面設定画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 発注画面設定画面-保存処理
	 */
	public function action_save() {
		$this->process_upload('no_image');

		if (!$this->validate_edit()) {
			$this->render(array(), 'setting/logo/order/noimage/index');
			return;
		}

		if (!$this->edit_setting()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/logo/order/noimage');
	}

	/**
	 * 画像削除処理
	 */
	public function action_default() {
		if (!$this->delete()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('デフォルト画像に更新しました');
		Response::redirect('/manage/setting/logo/order/noimage');
	}

	/**
	 * 更新処理
	 *
	 * @param Model_Setting $setting システム設定
	 * @param array $data フォームデータ
	 */
	private function edit_setting() {
		try {
			$this->save_upload();
		} catch (\Exception $e) {
			throw $e;
		}
		return true;
	}

	/**
	 * 更新バリデート処理
	 *
	 * @param $data フォームデータ
	 */
	private function validate_edit() {
		return $this->validate_file_upload('no_image');
	}

	/**
	 * 削除処理
	 */
	private function delete() {
		return \Image_Logo::remove(NO_IMAGE_PATH . 'noimage.jpg');
	}
}