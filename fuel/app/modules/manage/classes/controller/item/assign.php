<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
/**
 * 割当管理コントローラクラス
 */
class Controller_Item_Assign extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'delete', 'delete_save', 'upload_csv', 'upload_csv_save', 'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = '割当管理';

	/**
	 * 割当一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 割当追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 割当追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'item/assign/add');
			return;
		}

		$member = \Model_Member::query()->where('code', $data['member_code'])->get_one();
		if ($this->exist_item_assign($member->id, $data['item_code'])) {
			$this->set_error_message('登録済みです');
			$this->render($data, 'item/assign/add');
			return;
		}

		if (!$this->insert_item_assign($member->id, $data['item_code'])) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'item/assign/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 割当削除画面-初期表示
	 *
	 * @param int $id 割当商品ID
	 */
	public function action_delete($id) {
		$data = \Model_Item_Assign::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 割当削除画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item_assign = \Model_Item_Assign::find($data['id']);
		if (empty($item_assign)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$item_assign->soft_delete()) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'item/assign/delete');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 割当CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * 割当CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('assign_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'item/assign/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Assign($this->get_upload_file('assign_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'item/assign/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 割当CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * 割当CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Assign();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_ASSIGN, $data);
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('member_code', '発注者コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('exist', 'members', 'code');
		$validation->add('item_code', '商品コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('exist', 'items', 'code');

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('assign_csv', true);
	}

	/**
	 * 登録済みチェック
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function exist_item_assign($member_id, $item_code) {
		return \Model_Item_Assign::query()
			->where('member_id', $member_id)
			->where('item_code', $item_code)
			->count() > 0;
	}

	/**
	 * 割当商品を登録する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function insert_item_assign($member_id, $item_code) {
		$values = array();
		$values['item_code'] = $item_code;
		$values['member_id'] = $member_id;
		$values['renewal_datetime'] = date('Y-m-d H:i:s');

		$model = \Model_Item_Assign::forge($values);

		return $model->save() !== false;
	}
}