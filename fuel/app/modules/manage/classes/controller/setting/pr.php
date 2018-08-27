<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Arr;
use Fuel\Core\Response;
use Fuel\Core\File;
/**
 * PR商品設定コントローラクラス
 */
class Controller_Setting_Pr extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'PR商品設定';

	/**
	 * PR商品設定画面-初期表示
	 */
	public function action_index() {
		$data = \Model_Setting::find('first');
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}

		$this->render($data);
	}
	
	/**
	 * PR商品設定画面-更新処理
	 */
	public function action_save() {
		$data = Input::post();
		$setting = \Model_Setting::find('first');
		if (empty($setting)) {
			throw new \HttpServerErrorException();
		}
		
		if (!$this->validate_edit($data)) {
			$data = array_merge($setting->to_array(), $data);
			$this->render($data, 'setting/pr/index');
			return;
		}
		
		if (!$this->edit_setting($setting, $data)) {
			$data = array_merge($setting->to_array(), $data);
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'setting/pr/index');
			return;
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/pr');
	}
	
	/**
	 * 更新バリデート
	 * 
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('pr_title', 'PR商品ページタイトル')
			->add_rule('required')
			->add_rule('max_length', 50);
		
		$field_error = $this->validate($validation, $data);
		$upload_error = $this->validate_upload('pr_image');
		
		return $field_error && $upload_error;
	}

	/**
	 * 更新処理
	 * 
	 * @param Model_Setting $setting 元データ
	 * @param array $data フォームデータ
	 */
	private function edit_setting($setting, $data) {
		try {
			DB::start_transaction();
			
			$file = \Common_Upload::instance()->get_file('pr_image');
			
			if (!$this->update_setting($setting, Arr::get($data, 'pr_title'), Arr::get($file, 'name'))) {
					DB::rollback_transaction();
					return false;
			}
			
			if (!isset($file['tmp_name']) || !empty($file['tmp_name'])) {
				if (!\Image_Pr::create($file)) {
					DB::rollback_transaction();
					return false;
				}
				if (!\Image_Pr::remove($file['name'])) {
					DB::rollback_transaction();
					return false;
				}
			}
			
			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		
		return true;
	}
	
	/**
	 * システム設定を更新する
	 * 
	 * @param Model_Setting $setting 元データ
	 * @param string $pr_title PR商品タイトル
	 * @param string $pr_image_name PR商品ファイル名
	 */
	private function update_setting($setting, $pr_title, $pr_image_name) {
		$setting->pr_title = $pr_title;
		if (!empty($pr_image_name)) {
			$setting->pr_image_name = $pr_image_name;
		}
		
		return $setting->save() !== false;
	}
}