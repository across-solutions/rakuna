<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Session;
use Fuel\Core\File;
use Fuel\Core\Config;
use Fuel\Core\DB;
use Email\Email;
/**
 * 配達曜日コントローラクラス
 */
class Controller_Setting_Shipping_Week extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save',
								'upload_csv', 'upload_csv_save', 'download_csv', 'download_csv_save');

	/**
	 * ページタイトル
	 */
	protected $title = '配達曜日設定';

	/**
	 * 一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();

		if (!$this->validate_add($data)) {
			$this->render($data, 'setting/shipping/week/add');
			return;
		}

		if (!$this->insert_delivery_week($data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 発注者アカウントID
	 */
	public function action_edit($id) {
		$data = \Model_Delivery_Week::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$delivery_week = \Model_Delivery_Week::find($data['id']);
		if (empty($delivery_week)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/shipping/week/edit');
			return;
		}

		if (!$this->update_delivery_week($delivery_week, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'setting/shipping/week/edit');
			return;
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$delivery_week = \Model_Delivery_Week::find($data['id']);
		if (empty($delivery_week)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$delivery_week->soft_delete()) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('delivery_week_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'setting/shipping/week/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Delivery_Week($this->get_upload_file('delivery_week_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'setting/shipping/week/upload_csv');
			return;
		}

		if (!$csv->save()) {
			if ($csv->has_error()) {
				$this->render(null, 'setting/shipping/week/upload_csv');
				return;
			}else{
				throw new \HttpServerErrorException();
			}
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Delivery_Week();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_DELIVERY_WEEK, $data);
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('code', '配達曜日コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10)
			->add_rule('unique', 'delivery_weeks', 'code');

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('code', '配達曜日コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10)
			->add_rule('unique', 'delivery_weeks', 'code', $data['id']);

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('delivery_week_csv', true);
	}

	/**
	 * 配達曜日を登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_delivery_week($data) {
		$fields = array('code');
		$values = \Common_Util::filter($data, $fields);
		$values['delivery_flg_mon'] = isset($data['delivery_flg_mon']) && $data['delivery_flg_mon'] == '1';
		$values['delivery_flg_tue'] = isset($data['delivery_flg_tue']) && $data['delivery_flg_tue'] == '1';
		$values['delivery_flg_wed'] = isset($data['delivery_flg_wed']) && $data['delivery_flg_wed'] == '1';
		$values['delivery_flg_thu'] = isset($data['delivery_flg_thu']) && $data['delivery_flg_thu'] == '1';
		$values['delivery_flg_fri'] = isset($data['delivery_flg_fri']) && $data['delivery_flg_fri'] == '1';
		$values['delivery_flg_sat'] = isset($data['delivery_flg_sat']) && $data['delivery_flg_sat'] == '1';
		$values['delivery_flg_sun'] = isset($data['delivery_flg_sun']) && $data['delivery_flg_sun'] == '1';

		$model = \Model_Delivery_Week::forge($values);
		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * 配達曜日を更新する
	 *
	 * @param Model_Delivery_Week $delivery_week 元データ
	 * @param array $data フォームデータ
	 */
	private function update_delivery_week($delivery_week, $data) {
		$fields = array('code');
		\Common_Util::copy($delivery_week, $data, $fields);
		$delivery_week['delivery_flg_mon'] = isset($data['delivery_flg_mon']) && $data['delivery_flg_mon'] == '1';
		$delivery_week['delivery_flg_tue'] = isset($data['delivery_flg_tue']) && $data['delivery_flg_tue'] == '1';
		$delivery_week['delivery_flg_wed'] = isset($data['delivery_flg_wed']) && $data['delivery_flg_wed'] == '1';
		$delivery_week['delivery_flg_thu'] = isset($data['delivery_flg_thu']) && $data['delivery_flg_thu'] == '1';
		$delivery_week['delivery_flg_fri'] = isset($data['delivery_flg_fri']) && $data['delivery_flg_fri'] == '1';
		$delivery_week['delivery_flg_sat'] = isset($data['delivery_flg_sat']) && $data['delivery_flg_sat'] == '1';
		$delivery_week['delivery_flg_sun'] = isset($data['delivery_flg_sun']) && $data['delivery_flg_sun'] == '1';

		return $delivery_week->save() !== false;
	}

}