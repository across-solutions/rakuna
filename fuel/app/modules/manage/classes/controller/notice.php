<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Response;
use Fuel\Core\Arr;
use Fuel\Core\Upload;
use Fuel\Core\Config;
use Fuel\Core\File;
/**
 * お知らせコントローラクラス
 */
class Controller_Notice extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save', 'mail', 'mail_save');

	/**
	 * ページタイトル
	 */
	protected $title = 'お知らせ';

	/**
	 * お知らせ一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * お知らせ追加画面-初期表示
	 */
	public function action_add() {
		$this->render();
	}

	/**
	 * お知らせ追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		$this->process_upload('notice_image');
		$this->process_upload('notice_pdf');

		if (!$this->validate_add($data)) {
			$this->render($data, 'notice/add');
			return;
		}

		if (!$this->create_notice($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'notice/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * お知らせ編集画面-初期表示
	 *
	 * @param int $id お知らせID
	 */
	public function action_edit($id) {
		$data = \Model_Notice::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * お知らせ編集画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$notice = \Model_Notice::find($data['id']);
		if (empty($notice)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'notice/edit');
			return;
		}

		if (!$this->edit_notice($notice, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'notice/edit');
			return;
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * お知らせ編集画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$notice = \Model_Notice::find($data['id']);
		if (empty($notice)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->delete_notice($notice)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'item/edit');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * お知らせメール送信画面-初期表示
	 *
	 * @param int $id お知らせID
	 */
	public function action_mail($id) {
		$data = \Model_Notice::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * お知らせメール送信画面-送信処理
	 */
	public function action_mail_send() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$notice = \Model_Notice::find($data['id']);
		if (empty($notice)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$members = $this->get_target_member($notice);
		if (count($members) > 0) {
			$mail = new \Sendmail_Notice($notice->id);
			foreach ($members as $member) {
				if (!$mail->send($member['id'])) {
					// TODO
				}
			}
		}

		$this->set_info_message('送信しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * お知らせPDFダウンロード
	 */
	public function action_download_pdf($id) {

		$notice = \Model_Notice::find($id);
		if (empty($notice)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$file = NOTICE_PDF_PATH . $id . '.pdf';
		if (!File::exists($file)) {
			$this->set_error_message('ファイルが存在しません');
			$this->render(null, 'notoce/edit');
			return;
		}

		File::download($file, 'notice.pdf');

	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('title', 'タイトル')
			->add_rule('required')
			->add_rule('max_length', 35);
		$validation->add('message', '内容')
			->add_rule('max_length', 5000);
		$validation->add('item_code', '掲載商品コード')
			->add_rule('max_length', 20)
			->add_rule('exist', 'items', 'code');
		$validation->add('member_group_id', '配信先グループ')
			->add_rule('exist', 'member_groups', 'id');

		$validate_error = $this->validate($validation, $data);
		$upload_error = $this->validate_image();

		return $validate_error && $upload_error;
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('title', 'タイトル')
			->add_rule('required')
			->add_rule('max_length', 35);
		$validation->add('message', '内容')
			->add_rule('max_length', 5000);
		$validation->add('item_code', '掲載商品コード')
			->add_rule('max_length', 20)
			->add_rule('exist', 'items', 'code');
		$validation->add('member_group_id', '配信先グループ')
			->add_rule('exist', 'member_groups', 'id');

		$validate_error = $this->validate($validation, $data);
		$upload_error = $this->validate_image();

		return $validate_error && $upload_error;
	}

	/**
	 * 画像のバリデート
	 */
	private function validate_image() {
		$image_validate = true;
		$this->process_upload('notice_image');
		if (!$this->validate_file_upload('notice_image')) {
			$image_validate = false;
		}
		$this->process_upload('notice_pdf');
		if (!$this->validate_file_upload('notice_pdf')) {
			$image_validate = false;
		}

		return $image_validate;
	}

	/**
	 * 追加処理
	 *
	 * @param array $data フォームデータ
	 */
	private function create_notice($data) {
		try {
			DB::start_transaction();

			$notice_id = $this->insert_notice($data);
			if (!$notice_id) {
				DB::rollback_transaction();
				return false;
			}

			Upload::register('before', function(&$file) {
				$upload_setting = Config::get('upload.setting');
				$config = $upload_setting[$file['element']];
				$file['path'] = $config['path'];
			});

			$this->process_upload('notice_image');
			$this->save_upload(function(&$file) use($notice_id) {
				$file['filename'] = $notice_id . '.' . Config::get('upload.setting.notice_image.extension');
			});

			$this->process_upload('notice_pdf');
			$this->save_upload(function(&$file) use($notice_id) {
				$file['filename'] = $notice_id . '.' . Config::get('upload.setting.notice_pdf.extension');
			});

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
	 * @param Model_Notice $notice 元データ
	 * @param array $data フォームデータ
	 */
	private function edit_notice($notice, $data) {
		try {
			DB::start_transaction();

			if (!$this->update_notice($notice, $data)) {
				DB::rollback_transaction();
				return false;
			}

			Upload::register('before', function(&$file) {
				$upload_setting = Config::get('upload.setting');
				$config = $upload_setting[$file['element']];
				$file['path'] = $config['path'];
			});

			$this->process_upload('notice_image');
			$this->save_upload(function(&$file) use($data) {
				$file['filename'] = $data['id'] . '.' . Config::get('upload.setting.notice_image.extension');
			});

			$this->process_upload('notice_pdf');
			$this->save_upload(function(&$file) use($data) {
				$file['filename'] = $data['id'] . '.' . Config::get('upload.setting.notice_pdf.extension');
			});

			$image_del = Arr::get($data, 'image_del');
			if (!is_null($image_del)) {
				if (!\Image_Notice::remove($data['id'])) {
					DB::rollback_transaction();
					return false;
				}
			}

			$pdf_del = Arr::get($data, 'pdf_del');
			if (!is_null($pdf_del)) {
				if (!\Pdf_Notice::remove($data['id'])) {
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
	 * @param Model_Notice $notice 削除データ
	 */
	private function delete_notice($notice) {
		try {
			DB::start_transaction();

			if (!$notice->soft_delete()) {
				DB::rollback_transaction();
				return false;
			}

			if (!\Image_Notice::remove($notice->id)) {
				DB::rollback_transaction();
				return false;
			}

			if (!\Pdf_Notice::remove($notice->id)) {
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
	 * お知らせメール送信対象を取得する
	 *
	 * @param Model_Notice $notice お知らせ
	 */
	private function get_target_member($notice) {
		$query = DB::select('id')->from('members');


		if($notice->member_group_id != ALL_MEMBER_GROUP){
			$query->where('members.member_group_id', '=', $notice->member_group_id );
		}

		$query->where('status', '=', Config::get('define.member_status.enable'));
		$query->where('email', '<>', '');
		$query->where('del_flg', '=', UNDELETED);

		$item_cd = $notice['item_code'];
		if (!is_null($item_cd) && trim($item_cd) != '') {
			$sub_query1 = 'NOT EXISTS (SELECT 1 FROM item_assigns WHERE members.id = item_assigns.member_id AND item_assigns.del_flg = '
				. UNDELETED . ')';
			$sub_query2 = 'EXISTS (SELECT 1 FROM item_assigns WHERE members.id = item_assigns.member_id AND item_assigns.item_code = \''
				. $notice->item_code . '\' AND item_assigns.del_flg = ' . UNDELETED . ')';
			$query->where_open();
			$query->where(DB::expr($sub_query1));
			$query->or_where(DB::expr($sub_query2));
			$query->where_close();
		}

		return $query->execute();
	}

	/**
	 * お知らせを登録する
	 *
	 * @param array $data フォームデータ
	 */
	private function insert_notice($data) {
		$fields = array('member_group_id', 'title', 'message', 'item_code');

		if ($data['member_group_id'] == '') {
			$data['member_group_id'] = ALL_MEMBER_GROUP;
		}

		$values = \Common_Util::filter($data, $fields);
		$values['entry_datetime'] = date('Y-m-d H:i:s');

		$model = \Model_Notice::forge($values);
		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * お知らせを更新する
	 *
	 * @param Model_Notice $notice 元データ
	 * @param array $data フォームデータ
	 */
	private function update_notice($notice, $data) {
		$fields = array('member_group_id', 'title', 'message', 'item_code');

		if ($data['member_group_id'] == '') {
			$data['member_group_id'] = ALL_MEMBER_GROUP;
		}

		\Common_Util::copy($notice, $data, $fields);

		return $notice->save() !== false;
	}
}