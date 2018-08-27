<?php
namespace Manage;

use Fuel\Core\Validation;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\DB;
/**
 * いつものグループ管理コントローラクラス
 */
class Controller_Recommended_Group extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save');

	/**
	 * ページタイトル
	 */
	protected $title = 'いつものグループ管理';

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
			$this->render($data, 'recommended/group/add');
			return;
		}

		if (!$this->insert_recommended_group($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'recommended/group/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id いつものグループID
	 */
	public function action_edit($id) {
		$data = \Model_Recommended_Group::find($id);
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

		$recommended_group = \Model_Recommended_Group::find($data['id']);
		if (empty($recommended_group)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'recommended/group/edit');
			return;
		}

		if (!$this->update_recommended_group($recommended_group, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'recommended/group/edit');
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

		$recommended_group = \Model_Recommended_Group::find($data['id']);
		if (empty($recommended_group)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if($this->count_recommended_item($recommended_group->id) > 0){
			$this->set_error_message('いつもの商品が含まれているため削除できません。');
			$this->render($recommended_group, 'recommended/group/edit');
			return;
		}

		if (!$this->delete_recommended_group($recommended_group)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($recommended_group, 'recommended/group/edit');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}


	/**
	 * 削除処理
	 *
	 * @param Model_Recommended_Group $recommended_group 削除データ
	 */
	private function delete_recommended_group($recommended_group) {

		try {
			DB::start_transaction();

			$this->delete_recommended_group_assigns($recommended_group->id);
			$recommended_group->soft_delete();

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * 論理削除処理
	 *
	 * @param int $recommended_group_id いつものグループID
	 */
	private function delete_recommended_group_assigns($recommended_group_id){
		return DB::update('recommended_group_assigns')
			->value('del_flg', DELETED)
			->value('update_user_id', $this->get_user_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('recommended_group_id', $recommended_group_id)
			->execute();
	}


	/**
	 * 登録されているいつもの商品数を取得する
	 *
	 * @param int $recommended_group_id いつものグループID
	 */

	private function count_recommended_item($recommended_group_id){

		$count = \Model_Recommended_Item::query()
			   ->related('item', array('join_type' => 'inner'))
			   ->where('recommended_group_id', '=', $recommended_group_id)
			   ->count();

		return $count;
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('code', 'いつものグループコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'recommended_groups', 'code');
		$validation->add('name', 'いつものグループ名')
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

		$validation->add('code', 'いつものグループコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'recommended_groups', 'code', $data['id']);
		$validation->add('name', 'いつものグループ名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * いつものグループを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_recommended_group($data) {
		$fields = array('code', 'name');
		$values = \Common_Util::filter($data, $fields);

		$recommended_group = \Model_Recommended_Group::forge($values);

		return $recommended_group->save() !== false;
	}

	/**
	 * いつものグループを更新する
	 *
	 * @param Model_Recommended_Group $recommended_group 元データ
	 * @param array $data フォームデータ
	 */
	private function update_recommended_group($recommended_group, $data) {
		$recommended_group['code'] = $data['code'];
		$recommended_group['name'] = $data['name'];

		return $recommended_group->save() !== false;
	}
}