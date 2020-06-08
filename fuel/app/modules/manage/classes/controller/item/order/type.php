<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Arr;
use Fuel\Core\HttpServerErrorException;
/**
 * 商品発注タイプ管理コントローラクラス
 */
class Controller_Item_Order_Type extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save', 'upload_csv', 'upload_csv_save', 'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = '商品発注タイプ管理';

	/**
	 * 商品発注タイプ一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 商品発注タイプ追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 商品発注タイプ追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'item/order/type/add');
			return;
		}

		$member = \Model_Member::query()->where('code', $data['member_code'])->get_one();
		if ($this->exist_item_order_type($member->id, $data['item_code'])) {
			$this->set_error_message('登録済みです');
			$this->render($data, 'item/order/type/add');
			return;
		}

		if (!$this->insert_item_order_type($member, $data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'item/order/type/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品発注タイプ編集画面-初期表示
	 *
	 * @param int $id 商品発注タイプID
	 */
	public function action_edit($id) {
		$data = \Model_Item_Order_Type::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 商品発注タイプ編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item_order_type = \Model_Item_Order_Type::find($data['id']);
		if (empty($item_order_type)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($item_order_type, 'item/order/type/edit');
			return;
		}

		if (!$this->update_item_order_type($item_order_type, $data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品発注タイプ削除画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item_order_type = \Model_Item_Order_Type::find($data['id']);
		if (empty($item_order_type)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$item_order_type->soft_delete()) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品発注タイプCSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * 商品発注タイプCSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('item_order_type_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'item/order/type/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Item_Order_Type($this->get_upload_file('item_order_type_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'item/order/type/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品発注タイプCSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * 商品発注タイプCSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Item_Order_Type();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_ITEM_ORDER_TYPE, $data);
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
		$validation->add('order_type', '発注タイプ')
			->add_rule('exist', 'order_types', 'id');

		return $this->validate($validation, $data);
	}

	/**
	 * 編集バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('order_type', '発注タイプ')
			->add_rule('exist', 'order_types', 'id');

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('order_type_csv', true);
	}

	/**
	 * 登録済みチェック
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function exist_item_order_type($member_id, $item_code) {
		return \Model_Item_Order_Type::query()
			->where('member_id', $member_id)
			->where('item_code', $item_code)
			->count() > 0;
	}

	/**
	 * 商品発注タイプ商品を登録する
	 *
	 * @param Model_Member $member_id 発注者アカウント
	 * @param array $data フォームデータ
	 */
	private function insert_item_order_type($member, $data) {
		$values = array();
		$values['item_code'] = $data['item_code'];
		$values['member_id'] = $member['id'];
		$values['order_type'] = $data['order_type'];

		$model = \Model_Item_Order_Type::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 商品発注タイプ商品を更新する
	 *
	 * @param Model_Item_Order_Type $item_order_type 商品発注タイプ商品
	 * @param array $data フォームデータ
	 */
	private function update_item_order_type($item_order_type, $data) {
		$item_order_type->order_type = $data['order_type'];

		return $item_order_type->save() !== false;
	}
}