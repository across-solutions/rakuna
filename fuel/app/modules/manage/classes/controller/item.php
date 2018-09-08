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
 * 商品管理コントローラクラス
 */
class Controller_Item extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save',  'delete_save',
							   'upload_csv', 'upload_csv_save',
							   'upload_image', 'upload_image_save',
							   'upload_pdf', 'upload_pdf_save',
							   'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = '商品管理';

	/**
	 * 商品一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 商品追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * 商品追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();

		if (!$this->validate_add($data)) {
			$this->render($data, 'item/add');
			return;
		}

		if (!$this->create_item($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'item/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品編集画面-初期表示
	 *
	 * @param int $id 商品ID
	 */
	public function action_edit($id) {
		$data = \Model_Item::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 商品編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$item = \Model_Item::find($data['id']);
		if (empty($item)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'item/edit');
			return;
		}

		if (!$this->edit_item($item, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'item/edit');
			return;
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 商品編集画面-削除処理
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

		if (!$this->delete_item($item)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'item/edit');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('item_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'item/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Item($this->get_upload_file('item_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'item/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 画像アップロード画面-初期表示
	 */
	public function action_upload_image() {
		$this->render();
	}

	/**
	 * 画像アップロード画面-アップロード処理
	 */
	public function action_upload_image_save() {
		$this->process_upload('item_image_zip');

		if (!$this->validate_image_upload()) {
			$this->render(null, 'item/upload_image');
			return;
		}

		$zip = new \Upload_Image_Item($this->get_upload_file('item_image_zip'));
		$zip->parse();
		if ($zip->has_error()) {
			$this->render(null, 'item/upload_image');
			return;
		}

		if (!$zip->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 画像アップロード画面-初期表示
	 */
	public function action_upload_pdf() {
		$this->render();
	}

	/**
	 * PDFアップロード画面-アップロード処理
	 */
	public function action_upload_pdf_save() {
		$this->process_upload('item_pdf_zip');

		if (!$this->validate_pdf_upload()) {
			$this->render(null, 'item/upload_pdf');
			return;
		}

		$zip = new \Upload_Pdf_Item($this->get_upload_file('item_pdf_zip'));
		$zip->parse();
		if ($zip->has_error()) {
			$this->render(null, 'item/upload_pdf');
			return;
		}

		if (!$zip->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Item();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_ITEM, $data);
	}

	/**
	 * 追加処理
	 *
	 * @param array $data フォームデータ
	 */
	private function create_item($data) {
		try {
			DB::start_transaction();

			if (!$this->insert_item($data)) {
				DB::rollback_transaction();
				return false;
			}

			$this->setUploadRegister($data);

			$this->process_upload('item_image');
			$this->save_upload();

			$this->process_upload('item_pdf');
			$this->save_upload();


			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * 更新処理
	 *
	 * @param Model_Item $item 商品
	 * @param array $data フォームデータ
	 */
	private function edit_item($item, $data) {
		DB::start_transaction();
		try {

			if (!$this->update_item($item, $data)) {
				DB::rollback_transaction();
				return false;
			}

			$this->setUploadRegister($data);

			$this->process_upload('item_image');
			$this->save_upload();

			$this->process_upload('item_pdf');
			$this->save_upload();

			$image_del = Arr::get($data, 'image_del');
			if (!is_null($image_del)) {
				if (!\Image_Item::remove($item->code)) {
					DB::rollback_transaction();
					return false;
				}
			}

			$pdf_del = Arr::get($data, 'pdf_del');
			if (!is_null($pdf_del)) {
				if (!\Pdf_Item::remove($item->code)) {
					DB::rollback_transaction();
					return false;
				}
			}

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
	 * @param Model_Item $item 削除データ
	 */
	private function delete_item($item) {
		try {
			DB::start_transaction();

			if (!$item->soft_delete()) {
				DB::rollback_transaction();
				return false;
			}

			if (!\Image_Item::remove($item->code)) {
				DB::rollback_transaction();
				return false;
			}

			if (!\Pdf_Item::remove($item->code)) {
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
			->add_rule('unique', 'items', 'code');
		$validation->add('name', '商品名')
			->add_rule('required')
			->add_rule('max_length', 50);
		$validation->add('yomigana', '商品カナ名')
			->add_rule('max_length', 50);
		$validation->add('item_category_id', 'カテゴリ')
			->add_rule('exist', 'item_categories', 'id');
		$validation->add('unit_name_case', 'ケース単位')
			->add_rule('required')
			->add_rule('max_length', 10);
		$validation->add('unit_name', 'バラ単位')
			->add_rule('required')
			->add_rule('max_length', 10);
		$validation->add('size_case', 'ケース入数')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999);
		$validation->add('size', 'バラ入数')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999);
		$validation->add('comment', '商品説明文')
			->add_rule('max_length', 500);
		$validation->add('price', \Common_Setting::is_case() ? 'バラ単価' : '単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('price_case', 'ケース単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('jan_code', 'JANコード')
			->add_rule('numeric')
			->add_rule('max_length', 13)
			->add_rule('unique', 'items', 'jan_code');
// 		$validation->add('pr_flg', 'PR商品')
// 			->add_rule('required');

		$field_error = $this->validate($validation, $data);
		$upload_error = $this->validate_file();

		return $field_error && $upload_error;
	}

	/**
	 * 更新バリデート
	 *
	 * @param $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('code', '商品コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'items', 'code', $data['id']);
		$validation->add('name', '商品名')
			->add_rule('required')
			->add_rule('max_length', 50);
		$validation->add('yomigana', '商品カナ名')
			->add_rule('max_length', 50);
		$validation->add('item_category_id', 'カテゴリ')
			->add_rule('exist', 'item_categories', 'id');
		$validation->add('unit_name_case', 'ケース単位')
			->add_rule('required')
			->add_rule('max_length', 10);
		$validation->add('unit_name', 'バラ単位')
			->add_rule('required')
			->add_rule('max_length', 10);
		$validation->add('size_case', 'ケース入数')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999);
		$validation->add('size', 'バラ入数')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999);
		$validation->add('comment')
			->add_rule('max_length', 500);
		$validation->add('price', \Common_Setting::is_case() ? 'バラ単価' : '単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('price_case', 'ケース単価')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);
		$validation->add('jan_code', 'JANコード')
			->add_rule('numeric')
			->add_rule('max_length', 13)
			->add_rule('unique', 'items', 'jan_code', $data['id']);
// 		$validation->add('pr_flg', 'PR商品')
// 			->add_rule('required');

		$field_error = $this->validate($validation, $data);
		$upload_error = $this->validate_file();

		return $field_error && $upload_error;
	}

	/**
	 * アップロードファイルバリデート
	 *
	 * @return boolean $flg
	 */
	private function validate_file() {
		$flg = true;

		$this->process_upload('item_image');
		if (!$this->validate_file_upload('item_image')) {
			$flg = false;
		}

		$this->process_upload('item_pdf');
		if (!$this->validate_file_upload('item_pdf')) {
			$flg = false;
		}

		return $flg;
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('item_csv', true);
	}

	/**
	 * 画像アップロードバリデート
	 */
	private function validate_image_upload() {
		return $this->validate_file_upload('item_image_zip', true);
	}

	/**
	 * PDFアップロードバリデート
	 */
	private function validate_pdf_upload() {
		return $this->validate_file_upload('item_pdf_zip', true);
	}

	/**
	 * 商品を登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_item($data) {
		$fields = array('item_category_id', 'code', 'name', 'yomigana', 'unit_name_case', 'unit_name',
						'size_case', 'size', 'type', 'comment', 'price', 'price_case', 'jan_code', 'pr_flg');
		$values = \Common_Util::filter($data, $fields);
		$values['renewal_datetime'] = date('Y-m-d H:i:s');

		if (is_null($values['price'])) {
			$values['price'] = null;
		}
		if (is_null($values['price_case'])) {
			$values['price_case'] = null;
		}

		$model = \Model_Item::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 商品を更新する
	 *
	 * @param Model_Item $item 元データ
	 * @param array $data フォームデータ
	 */
	private function update_item($item, $data) {
		$update_fields = array('item_category_id', 'code', 'name', 'yomigana', 'unit_name_case', 'unit_name',
								'size_case', 'size', 'type', 'comment', 'jan_code', 'pr_flg');
		$renewal_fields = array('code');

		if (isset($data['price'])) {
			$update_fields[] = 'price';
			$renewal_fields[] = 'price';
			if ($data['price'] == '') {
				$data['price'] = null;
			}
		}
		if (isset($data['price_case'])) {
			$update_fields[] = 'price_case';
			$renewal_fields[] = 'price_case';
			if ($data['price_case'] == '') {
				$data['price_case'] = null;
			}
		}

		if (\Common_Util::diff($item, $data, $renewal_fields)) {
			$item->renewal_datetime = date('Y-m-d H:i:s');
		}

		\Common_Util::copy($item, $data, $update_fields);

		return $item->save() !== false;
	}

	/**
	 * アップロードイベントにコールバックを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function setUploadRegister($data) {

		Upload::register('before', function(&$file) use($data) {
			$upload_setting = Config::get('upload.setting');
			$config = $upload_setting[$file['element']];
			$file['path'] = $config['path'];

			$extension = $upload_setting[$file['element']]['extension'];
			$file['filename'] = $data['code'] . '.' . $extension;
		});
	}
}