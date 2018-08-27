<?php
namespace Manage;

use Fuel\Core\Validation;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * 受注担当者コントローラクラス
 */
class Controller_Setting_Orderuser extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save');

	/**
	 * ページタイトル
	 */
	protected $title = '受注担当者管理';

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
	 *  一覧画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'setting/orderuser/add');
			return;
		}

		$data['corporation_name'] = \Model_Setting::find('first')->get('corporation_name');
		$data['mosgroup'] = Config::get('define.manage_group.2');

		$id = $this->insert_orderuser($data);
		if (!$id) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'orderuser/add');
			return;
		}

		$this->set_info_message('追加しました');
		Response::redirect('/manage/dialog/complete');
	}


	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 受注担当者アカウントID
	 */
	public function action_edit($id) {
		$data = \Model_User::find($id);
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

		$user = \Model_User::find($data['id']);
		if (empty($user)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/orderuser/edit');
			return;
		}

		if (!$this->update_orderuser($user, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'orderuser/edit');
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

		$user = \Model_User::find($data['id']);
		if (empty($user)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->remove_orderuser($user)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'orderuser/edit');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('name', '受注担当者名')
			->add_rule('required')
			->add_rule('max_length', 40)
			->add_rule('unique', 'users', 'name');
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'users', 'username');
		$validation->add('password', 'パスワード')
			->add_rule('required')
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

		$validation->add('name', '受注担当者名')
			->add_rule('required')
			->add_rule('max_length', 40)
			->add_rule('unique', 'users', 'name', $data['id']);
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'users', 'username', $data['id']);
		$validation->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);

		return $this->validate($validation, $data);
	}

	/**
	 * 受注担当者アカウントを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_orderuser($data) {
		$fields = array('corporation_name', 'name', 'username', 'password', 'mosgroup');
		$values = \Common_Util::filter($data, $fields);
		$values['corporation_name'] = $data['corporation_name'];
		$values['name'] = $data['name'];
		$values['username'] = $data['username'];
		$values['password'] = $data['password'];
		$values['mosgroup'] = $data['mosgroup'];
		$values['status'] = Config::get('define.user_status.enable');

		$model = \Model_User::forge($values);
		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * 受注担当者アカウントを更新する
	 *
	 * @param Model_User $user 元データ
	 * @param array $data フォームデータ
	 */
	private function update_orderuser($user, $data) {
		$fields = array('name', 'username', 'password');
		\Common_Util::copy($user, $data, $fields);

		return $user->save() !== false;
	}

	/**
	 * 削除処理
	 *
	 * @param Model_User $user 元データ
	 */
	private function remove_orderuser($user) {
		try {
			DB::start_transaction();

			if (!$user->soft_delete()) {
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


}