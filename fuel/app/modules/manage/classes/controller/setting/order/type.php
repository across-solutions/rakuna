<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Upload;
use Fuel\Core\Config;
use Fuel\Core\File;
use Fuel\Core\DB;
use Fuel\Core\Arr;
use Fuel\Core\Format;
use Fuel\Core\Unzip;
/**
 * ユーザ設定-発注タイプ設定コントローラクラス
 */
class Controller_Setting_Order_Type extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save',  'delete_save');

	/**
	 * ページタイトル
	 */
	protected $title = '発注タイプ設定';

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
			$this->render($data, 'setting/order/type/add');
			return;
		}

		if (!$this->insert_order_type($data)) {
			throw new HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 発注タイプID
	 */
	public function action_edit($id) {
		$data = \Model_Order_Type::find($id);
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

		$order_type = \Model_Order_Type::find($data['id']);
		if (empty($order_type)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/order/type/edit');
			return;
		}

		if (!$this->update_order_type($order_type, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'setting/order/type/edit');
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

		$order_type = \Model_Order_Type::find($data['id']);
		if (empty($order_type)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$order_type->soft_delete()) {
			throw new HttpServerErrorException();
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

		$validation->add('name', '発注タイプ名')
			->add_rule('required')
			->add_rule('max_length', 50);
		$validation->add('code', '出荷区分コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10);
		$validation->add('warehouse_code', '倉庫コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10);

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('name', '発注タイプ名')
			->add_rule('required')
			->add_rule('max_length', 50);
		$validation->add('code', '出荷区分コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10);
		$validation->add('warehouse_code', '倉庫コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 10);

		return $this->validate($validation, $data);
	}

	/**
	 * 発注タイプを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_order_type($data) {
		$fields = array('name', 'code', 'warehouse_code');
		$values = \Common_Util::filter($data, $fields);

		$model = \Model_Order_Type::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 発注タイプを更新する
	 *
	 * @param Model_Order_Type $order_type 元データ
	 * @param array $data フォームデータ
	 */
	private function update_order_type($order_type, $data) {
		$fields = array('name', 'code', 'warehouse_code');
		\Common_Util::copy($order_type, $data, $fields);

		return $order_type->save() !== false;
	}
}