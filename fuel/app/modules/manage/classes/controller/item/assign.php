<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Arr;
use Fuel\Core\HttpServerErrorException;
/**
 * 割当管理コントローラクラス
 */
class Controller_Item_Assign extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save', 'upload_csv', 'upload_csv_save', 'download_csv');

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

		if (!$this->insert_item_assign($member, $data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'item/assign/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 割当編集画面-初期表示
	 *
	 * @param int $id 割当商品ID
	 */
	public function action_edit($id) {
		$data = \Model_Item_Assign::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 割当編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item_assign = \Model_Item_Assign::find($data['id']);
		if (empty($item_assign)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$item_assign->price_case = Arr::get($data, 'price_case');
			$item_assign->price = Arr::get($data, 'price');
			$this->render($item_assign, 'item/assign/edit');
			return;
		}

		if (!$this->update_item_assign($item_assign, $data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
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
			throw new HttpServerErrorException();
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
		//if ($csv->has_error()) {
		//	$this->render(null, 'item/assign/upload_csv');
		//	return;
		//}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		if ($csv->has_error()) {
			$this->render(null, 'item/assign/upload_csv');
			return;
		} else {
			$this->set_info_message('登録しました');
			Response::redirect('/manage/dialog/complete');
		}
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
		$validation->add('price_case', 'ケース単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('price', 'バラ単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('hidden_flg_single', 'バラ表示')
			->add_rule('required');
		$validation->add('hidden_flg_case', 'ケース表示')
			->add_rule('required');

		return $this->validate($validation, $data);
	}

	/**
	 * 編集バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('price_case', 'ケース単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('price', 'バラ単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('hidden_flg_single', 'バラ表示')
			->add_rule('required');
		$validation->add('hidden_flg_case', 'ケース表示')
			->add_rule('required');

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
	 * @param Model_Member $member_id 発注者アカウント
	 * @param array $data フォームデータ
	 */
	private function insert_item_assign($member, $data) {
		$values = array();
		$values['item_code'] = $data['item_code'];
		$values['member_id'] = $member['id'];
		$values['price'] = is_null($data['price']) || trim($data['price']) === '' ? null : $data['price'];
		$values['price_case'] = is_null($data['price_case']) || trim($data['price_case']) === '' ? null : $data['price_case'];
		$values['hidden_flg_single'] = $data['hidden_flg_single'];
		$values['hidden_flg_case'] = $data['hidden_flg_case'];
		$values['renewal_datetime'] = date('Y-m-d H:i:s');

		$model = \Model_Item_Assign::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 割当商品を更新する
	 *
	 * @param Model_Item_Assign $item_assign 割当商品
	 * @param array $data フォームデータ
	 */
	private function update_item_assign($item_assign, $data) {
		$price_case = is_null($data['price_case']) || trim($data['price_case']) === '' ? null : $data['price_case'];
		$price = is_null($data['price']) || trim($data['price']) === '' ? null : $data['price'];

		if ($item_assign->price_case === $price_case && $item_assign->price === $price
				&& $item_assign->hidden_flg_single === $data['hidden_flg_single']
				&& $item_assign->hidden_flg_case === $data['hidden_flg_case']) {
			return true;
		}

		$item_assign->price_case = $price_case;
		$item_assign->price = $price;
		$item_assign->hidden_flg_single = $data['hidden_flg_single'];
		$item_assign->hidden_flg_case = $data['hidden_flg_case'];
		$item_assign->renewal_datetime = date('Y-m-d H:i:s');

		return $item_assign->save() !== false;
	}
}