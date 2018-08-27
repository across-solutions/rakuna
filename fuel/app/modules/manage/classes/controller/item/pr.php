<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Config;
use Fuel\Core\Format;
use Fuel\Core\Upload;
use Fuel\Core\DB;
/**
 * PR商品管理コントローラクラス
 */
class Controller_Item_Pr extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('edit', 'edit_save', 'upload_csv', 'upload_csv_save', 'add', 'add_save');

	/**
	 * ページタイトル
	 */
	protected $title = 'PR商品管理';

	/**
	 * 初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 商品コードを入力して追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 商品コードを入力して追加画面-追加処理
	 */
	public function action_add_save() {
		$data = Input::post();

		if (!$this->validate_add($data)) {
			$this->render($data, 'item/pr/add');
			return;
		}

		$item = $this->get_item($data['code']);
		if (!$this->update_pr_flg($item)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集
	 * @param $id 商品ID
	 */
	public function action_edit($id) {
		$data = \Model_Item::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 編集画面-解除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item = \Model_Item::find($data['id']);
		if (empty($item)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->remove_pr_flg($item)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('解除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVアップロード
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * CSVアップロード保存
	 */
	public function action_upload_csv_save() {
		if (!$this->validate_csv_upload()) {
			$this->render(null, 'item/pr/upload_csv');
			return;
		}

		$file = \Common_Upload::instance()->get_file('pr_item_csv');
		$csv = new \Upload_Csv_Pr($file['tmp_name']);
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'item/pr/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 追加バリデート
	 * @param $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('code', '商品コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('exist', 'items', 'code')
			->add_rule('unique', 'items', 'code', null, array(array('pr_flg', '=', true)));

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_upload('pr_item_csv', true);
	}

	/**
	 * 商品を取得する
	 *
	 * @param string $code 商品コード
	 */
	private function get_item($code) {
		return \Model_Item::query()
			->where('code', '=', $code)
			->get_one();
	}

	/**
	 * PRフラグを設定する
	 *
	 * @param Model_Item $item 商品
	 */
	private function update_pr_flg($item) {
		$item->pr_flg = true;

		return $item->save() !== false;
	}

	/**
	 * PRフラグを解除する
	 *
	 * @param Model_Item $item 商品
	 */
	private function remove_pr_flg($item) {
		$item->pr_flg = false;

		return $item->save() !== false;
	}
}