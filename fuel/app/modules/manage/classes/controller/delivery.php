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
 * 納品先コントローラクラス
 */
class Controller_Delivery extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save',
								'upload_csv', 'upload_csv_save', 'download_csv', 'download_csv_save');

	/**
	 * ページタイトル
	 */
	protected $title = '発注者管理';

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
			$this->render($data, 'delivery/add');
			return;
		}

		if (!$this->insert_delivery($data)) {
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
		$data = \Model_Delivery::find($id);
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

		$delivery = \Model_Delivery::find($data['id']);
		if (empty($delivery)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'delivery/edit');
			return;
		}

		if (!$this->update_delivery($delivery, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'delivery/edit');
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

		$delivery = \Model_Delivery::find($data['id']);
		if (empty($delivery)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$delivery->soft_delete()) {
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
		$this->process_upload('delivery_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'delivery/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Delivery($this->get_upload_file('delivery_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'delivery/upload_csv');
			return;
		}

		if (!$csv->save()) {
			if ($csv->has_error()) {
				$this->render(null, 'delivery/upload_csv');
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
		$csv = new \Download_Csv_Delivery();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_DELIVERY, $data);
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$unique_where = array();
		$unique_where['unique_where'][] = 'member_code';
		$unique_where['unique_where'][] = '=';
		$unique_where['unique_where'][] = $data['member_code'];

		$validation->add('member_code', '発注者コード')
			->add_rule('required')
			->add_rule('exist', 'members', 'code');
		$validation->add('code', '納品先コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'deliveries', 'code', null, $unique_where);
		$validation->add('name', '納品先名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('name_kana', '納入先カナ名')
			->add_rule('max_length', 50);
		$validation->add('receiver_name1', '荷受け人名1')
			->add_rule('max_length', 40);
		$validation->add('receiver_name2', '荷受け人名2')
			->add_rule('max_length', 40);
		$validation->add('zip', '郵便番号')
			->add_rule('max_length', 8);
		$validation->add('address1', '住所1')
			->add_rule('max_length', 50);
		$validation->add('address2', '住所2')
			->add_rule('max_length', 50);
		$validation->add('address3', '住所3')
			->add_rule('max_length', 50);
		$validation->add('tel', '電話番号')
			->add_rule('max_length', 14);
		$validation->add('fax', 'FAX')
			->add_rule('max_length', 14);
		$validation->add('delivery_week_code', '配達曜日コード')
			->add_rule('exist', 'delivery_weeks', 'code');

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$unique_where = array();
		$unique_where['unique_where'][] = 'member_code';
		$unique_where['unique_where'][] = '=';
		$unique_where['unique_where'][] = $data['member_code'];

		$validation->add('member_code', '発注者コード')
			->add_rule('required')
			->add_rule('exist', 'members', 'code');
		$validation->add('code', '納品先コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'deliveries', 'code', $data['id'], $unique_where);
		$validation->add('name', '納品先名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('name_kana', '納入先カナ名')
			->add_rule('max_length', 50);
		$validation->add('receiver_name1', '荷受け人名1')
			->add_rule('max_length', 40);
		$validation->add('receiver_name2', '荷受け人名2')
			->add_rule('max_length', 40);
		$validation->add('zip', '郵便番号')
			->add_rule('max_length', 8);
		$validation->add('address1', '住所1')
			->add_rule('max_length', 50);
		$validation->add('address2', '住所2')
			->add_rule('max_length', 50);
		$validation->add('address3', '住所3')
			->add_rule('max_length', 50);
		$validation->add('tel', '電話番号')
			->add_rule('max_length', 14);
		$validation->add('fax', 'FAX')
			->add_rule('max_length', 14);
		$validation->add('delivery_week_code', '配達曜日コード')
			->add_rule('exist', 'delivery_weeks', 'code');

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('delivery_csv', true);
	}

	/**
	 * 納品先を登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_delivery($data) {
		$fields = array('member_code', 'code', 'name', 'name_kana', 'receiver_name1', 'receiver_name2',
						'zip', 'address1', 'address2', 'address3', 'tel', 'fax', 'delivery_week_code');
		$values = \Common_Util::filter($data, $fields);

		$model = \Model_Delivery::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 納品先を更新する
	 *
	 * @param Model_Delivery $delivery 元データ
	 * @param array $data フォームデータ
	 */
	private function update_delivery($delivery, $data) {
		$fields = array('member_code', 'code', 'name', 'name_kana', 'receiver_name1', 'receiver_name2',
						'zip', 'address1', 'address2', 'address3', 'tel', 'fax', 'delivery_week_code');
		\Common_Util::copy($delivery, $data, $fields);

		return $delivery->save() !== false;
	}

}