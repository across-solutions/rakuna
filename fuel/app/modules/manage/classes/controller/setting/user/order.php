<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\DB;
use Fuel\Core\Upload;
/**
 * ユーザ設定-発注管理設定コントローラクラス
 */
class Controller_Setting_User_Order extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'ユーザ設定-発注管理設定';

	/**
	 * 発注画面設定画面-初期表示
	 */
	public function action_index() {
		$data = \Model_Setting::find('first');
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}

		$this->render($data);
	}

	/**
	 * 発注画面設定画面-保存処理
	 */
	public function action_save() {
		$data = Input::post();

		$setting = \Model_Setting::find('first');
		if (empty($setting)) {
			throw new \HttpServerErrorException();
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/user/order/index');
			return;
		}

		if (!$this->edit_setting($setting, $data)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/user/order');
	}

	/**
	 * 更新処理
	 *
	 * @param Model_Setting $setting システム設定
	 * @param array $data フォームデータ
	 */
	private function edit_setting($setting, $data) {
		try {
			DB::start_transaction();

			if (!$this->update_setting($setting, $data)) {
				DB::rollback_transaction();
				return false;
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * 更新バリデート処理
	 *
	 * @param $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('item_num', '商品表示件数')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 3, 18);
		$validation->add('tax_rate', '消費税率')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 100);

		return $this->validate($validation, $data);

	}

	/**
	 * システム設定を更新する
	 *
	 * @param Model_Setting $setting 元データ
	 * @param array $data フォームデータ
	 */
	private function update_setting($setting, $data) {
		$setting->item_num = $data['item_num'];
		$setting->tax_rate = $data['tax_rate'];

		return $setting->save() !== false;
	}
}