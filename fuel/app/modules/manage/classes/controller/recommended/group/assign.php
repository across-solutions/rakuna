<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
/**
 * いつものグループ割当管理コントローラクラス
 */
class Controller_Recommended_Group_Assign extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'delete', 'delete_save', 'upload_csv', 'upload_csv_save', 'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = 'いつものグループ割当管理';

	/**
	 * いつものグループ割当一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * いつものグループ割当追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * いつものグループ割当追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'recommended/group/assign/add');
			return;
		}

		$recommended_group = \Model_Recommended_Group::query()->where('code', $data['recommended_group_code'])->get_one();
		$member = \Model_Member::query()->where('code', $data['member_code'])->get_one();

		if ($this->exist_recommended_group_assign($member->id, $recommended_group->id)) {
			$this->set_error_message('登録済みです');
			$this->render($data, 'recommended/group/assign/add');
			return;
		}

		if (!$this->insert_recommended_group_assign($member->id, $recommended_group->id)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'recommended/group/assign/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * いつものグループ割当削除画面-初期表示
	 *
	 * @param int $id いつものグループ割当商品ID
	 */
	public function action_delete($id) {
		$data = \Model_Recommended_Group_Assign::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * いつものグループ割当削除画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$recommended_group_assign = \Model_Recommended_Group_Assign::find($data['id']);
		if (empty($recommended_group_assign)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$recommended_group_assign->soft_delete()) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'recommended/group/assign/delete');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * いつものグループ割当CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * いつものグループ割当CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('recommended_group_assign_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'recommended/group/assign/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Recommended_Group_Assign($this->get_upload_file('recommended_group_assign_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'recommended/group/assign/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * いつものグループ割当CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * いつものグループ割当CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Recommended_Group_Assign();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_RECOMMENDED_GROUP_ASSIGN, $data);
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
		$validation->add('recommended_group_code', 'いつものグループコード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('exist', 'recommended_groups', 'code');

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('recommended_group_assign_csv', true);
	}

	/**
	 * 登録済みチェック
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $recommended_group_id いつものグループ割当コード
	 */
	private function exist_recommended_group_assign($member_id, $recommended_group_id) {
		return \Model_Recommended_Group_Assign::query()
			->where('member_id', $member_id)
			->where('recommended_group_id', $recommended_group_id)
			->count() > 0;
	}

	/**
	 * いつものグループ割当を登録する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $recommended_group_id いつものグループ割当コード
	 */
	private function insert_recommended_group_assign($member_id, $recommended_group_id) {
		$values = array();
		$values['recommended_group_id'] = $recommended_group_id;
		$values['member_id'] = $member_id;

		$model = \Model_Recommended_Group_Assign::forge($values);

		return $model->save() !== false;
	}
}