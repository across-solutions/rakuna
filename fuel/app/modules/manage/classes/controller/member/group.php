<?php
namespace Manage;

use Fuel\Core\Validation;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Session;
/**
 * 発注者グループ管理コントローラクラス
 */
class Controller_Member_Group extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save');

	/**
	 * ページタイトル
	 */
	protected $title = 'グループ管理';

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
			$this->render(null, 'member/group/add');
			return;
		}

		if (!$this->insert_member_group($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render(null, 'member/group/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 発注者グループID
	 */
	public function action_edit($id) {
		$data = \Model_Member_Group::find($id);
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

		$member_group = \Model_Member_Group::find($data['id']);
		if (empty($member_group)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'member/group/edit');
			return;
		}

		if (!$this->update_member_group($member_group, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'member/group/edit');
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

		$member_group = \Model_Member_Group::find($data['id']);
		if (empty($member_group)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if ($this->count_member($data['id']) > 0) {
			$this->set_error_message('発注者アカウントが登録されているため削除できません');
			$this->render($member_group, 'member/group/edit');
			return;
		}

		if (!$member_group->soft_delete()) {
			$this->set_error_message('削除に失敗しました');
			$this->render($member_group, 'member/group/edit');
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

		$validation->add('code', 'グループコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'member_groups', 'code');
		$validation->add('name', 'グループ名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('code', 'グループコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'member_groups', 'code', $data['id']);
		$validation->add('name', 'グループ名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * 登録されている発注者アカウント数を取得する
	 *
	 * @param int $member_group_id 発注者グループID
	 */
	private function count_member($member_group_id) {
		return \Model_Member::query()
			->where('member_group_id', '=', $member_group_id)
			->count();
	}

	/**
	 * 発注者グループを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_member_group($data) {
		$fields = array('code', 'name');
		$values = \Common_Util::filter($data, $fields);

		$member_group = \Model_Member_Group::forge($values);

		return $member_group->save() !== false;
	}

	/**
	 * 発注者グループを更新する
	 *
	 * @param Model_Member_Group $member_group 元データ
	 * @param array $data フォームデータ
	 */
	private function update_member_group($member_group, $data) {
		$member_group['code'] = $data['code'];
		$member_group['name'] = $data['name'];

		return $member_group->save() !== false;
	}
}