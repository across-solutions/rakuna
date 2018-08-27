<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\DB;
/**
 * カテゴリ管理コントローラクラス
 */
class Controller_Item_Category extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save', 'bulk_delete', 'bulk_delete_save');

	/**
	 * ページタイトル
	 */
	protected $title = 'カテゴリ管理';

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
			$this->render($data, 'item/category/add');
			return;
		}

		if (!$this->insert_item_category($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'item/category/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id カテゴリID
	 */
	public function action_edit($id) {
		$data = \Model_Item_Category::find($id);
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

		$item_category = \Model_Item_Category::find($data['id']);
		if (empty($item_category)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'item/category/edit');
			return;
		}

		if (!$this->update_item_category($item_category, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'item/category/edit');
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

		$item_category = \Model_Item_Category::find($data['id']);
		if (empty($item_category)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->remove_item_category($item_category)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'item/category/edit');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 一括削除画面-初期表示
	 */
	public function action_bulk_delete() {
		$this->render();
	}

	/**
	 * 一括削除画面-一括削除処理
	 */
	public function action_bulk_delete_save() {
		$data = Input::post();
		if (!isset($data['delete_id']) || empty($data['delete_id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->remove_item_categories($data['delete_id'])) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'item/category/bulk_delete');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 追加バリデート
	 * @param $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('code', 'カテゴリコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'item_categories', 'code');
		$validation->add('name', 'カテゴリ名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 * @param $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('code', 'カテゴリコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'item_categories', 'code', $data['id']);
		$validation->add('name', 'カテゴリ名')
			->add_rule('required')
			->add_rule('max_length', 20);

		return $this->validate($validation, $data);
	}

	/**
	 * 削除処理
	 *
	 * @param Model_Item_Category $item_category 元データ
	 */
	private function remove_item_category($item_category) {
		try {
			DB::start_transaction();

			if (!$item_category->soft_delete()) {
				DB::rollback_transaction();
				return false;
			}

			$this->update_item($item_category->id);

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return true;
	}

	/**
	 * 削除処理
	 *
	 * @param array $id_list カテゴリIDリスト
	 */
	private function remove_item_categories($id_list) {
		try {
			DB::start_transaction();

			if (!$this->delete_item_categories($id_list)) {
				DB::rollback_transaction();
				return false;
			}

			$this->update_items($id_list);

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * カテゴリを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_item_category($data) {
		$fields = array('code', 'name');
		$values = \Common_Util::filter($data, $fields);

		$model = \Model_Item_Category::forge($values);

		return $model->save() !== false;
	}

	/**
	 * カテゴリを更新する
	 *
	 * @param Model_Item_Category $item_category 元データ
	 * @param array $data フォームデータ
	 */
	private function update_item_category($item_category, $data) {
		$item_category['code'] = $data['code'];
		$item_category['name'] = $data['name'];

		return $item_category->save() !== false;
	}

	/**
	 * 商品のカテゴリを解除する
	 *
	 * @param int $item_category_id カテゴリID
	 */
	private function update_item($item_category_id) {
		return $this->update_items(array($item_category_id));
	}

	/**
	 * 商品のカテゴリを解除する
	 *
	 * @param int $id_list カテゴリIDリスト
	 */
	private function update_items($id_list) {
		return DB::update('items')
			->value('item_category_id', null)
			->value('update_user_id', $this->get_user_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('item_category_id', 'in', $id_list)
			->execute();
	}

	/**
	 * 論理削除処理
	 *
	 * @param array $id_list カテゴリIDリスト
	 */
	private function delete_item_categories($id_list) {
		return DB::update('item_categories')
			->value('del_flg', DELETED)
			->value('update_user_id', $this->get_user_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', 'in', $id_list)
			->execute();
	}
}