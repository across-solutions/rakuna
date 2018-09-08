<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\HttpServerErrorException;
use Fuel\Core\Response;
/**
 * 営業担当アカウントコントローラクラス
 */
class Controller_Sales_Representative extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save',
								'upload_csv', 'upload_csv_save', 'download_csv', 'download_csv_save');

	/**
	 * ページタイトル
	 */
	protected $title = '営業担当管理';

	/**
	 * 営業担当一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 営業担当追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 営業担当追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'sales/representative/add');
			return;
		}

		$data['username'] = $this->create_username($data);
		$data['password'] = $this->create_password($data);

		if (!$this->insert_sales_representative($data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 営業担当編集画面-初期表示
	 *
	 * @param int $id 営業担当アカウントID
	 */
	public function action_edit($id) {
		$data = \Model_Sales_Representative::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 営業担当編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$sales_representative = \Model_Sales_Representative::find($data['id']);
		if (empty($sales_representative)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'sales/representative/edit');
			return;
		}

		if (!$this->update_sales_representative($sales_representative, $data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 営業担当編集画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$sales_representative = \Model_Sales_Representative::find($data['id']);
		if (empty($sales_representative)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->delete_sales_representative($sales_representative)) {
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
		$this->process_upload('sales_representative_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'sales/representative/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Sales_Representative($this->get_upload_file('sales_representative_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'sales/representative/upload_csv');
			return;
		}

		if (!$csv->save()) {
			if ($csv->has_error()) {
				$this->render(null, 'sales/representative/upload_csv');
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
		$csv = new \Download_Csv_Sales_Representative();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_SALES_REPRESENTATIVE, $data);
	}

	/**
	 * ログインIDを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_username($data) {
		$username = $data['username'];
		if (!is_null($username) && $username != '') {
			return $username;
		}

		while (true) {
			$username = \Common_Util::random_string(RANDOM_USERNAME_NUM);

			if (!\Model_Sales_Representative::exists($username, 'username')) {
				return $username;
			}
		}
	}

	/**
	 * パスワードを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_password($data) {
		$password = $data['password'];
		if (!is_null($password) && $password != '') {
			return $password;
		}

		return \Common_Util::random_string(RANDOM_PASSWORD_NUM);
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('sales_person_code', '営業担当者コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'sales_representatives', 'sales_person_code');
		$validation->add('sales_person_name', '営業担当者名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('username', 'ログインID')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'sales_representatives', 'username');
		$validation->add('password', 'パスワード')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('sales_person_code', '営業担当者コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'sales_representatives', 'sales_person_code', $data['id']);
		$validation->add('sales_person_name', '営業担当者名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'sales_representatives', 'username', $data['id']);
		$validation->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('sales_representative_csv', true);
	}

	/**
	 * 営業担当アカウントを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_sales_representative($data) {
		$fields = array('sales_person_code', 'sales_person_name', 'username', 'password');
		$values = \Common_Util::filter($data, $fields);
		$values['status'] = Config::get('define.sales_status.enable');

		$model = \Model_Sales_Representative::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 営業担当アカウントを更新する
	 *
	 * @param Model_Sales_Representative $sales_representative 元データ
	 * @param array $data フォームデータ
	 */
	private function update_sales_representative($sales_representative, $data) {
		$fields = array('sales_person_code', 'sales_person_name', 'username', 'password');
		\Common_Util::copy($sales_representative, $data, $fields);

		return $sales_representative->save() !== false;
	}

	/**
	 * 営業担当アカウントを削除する
	 *
	 * @param Model_Sales_Representative $sales_representative 元データ
	 */
	private function delete_sales_representative($sales_representative) {
		$sales_representative->del_flg = DELETED;

		return $sales_representative->save() !== false;
	}
}