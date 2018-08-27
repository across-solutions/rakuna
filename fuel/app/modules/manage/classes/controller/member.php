<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Session;
use Fuel\Core\File;
use Fuel\Core\Config;
use Fuel\Core\DB;
use Email\Email;
/**
 * 発注者アカウントコントローラクラス
 */
class Controller_Member extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete_save',  'upload_csv', 'upload_csv_save', 'download_csv', 'download_csv_save', 'complete_mail', 'complete_qr', 'id_mail', 'bulk_id_mail', 'id_mail_send');

	/**
	 * ページタイトル
	 */
	protected $title = '発注者管理';

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
			$this->render($data, 'member/add');
			return;
		}

		$qr_key = \Common_Util::random_string(RANDOM_QR_KEY_NUM);
		$username = $this->create_username($data);
		$password = $this->create_password($data);

		$id = $this->insert_member($data, $qr_key, $username, $password);
		if (!$id) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'member/add');
			return;
		}

		$qr = \Common_Qr::forge();
		$qr->output(QR_IMAGE_PATH, $qr_key, $this->create_mail_auth_message($qr_key));

		if (empty($data['email'])) {
			Response::redirect('/manage/member/complete_qr/' . $id);
		}

		//登録完了メールの自動送信はしないのでコメントアウト
// 		$sendmail = new \Sendmail_Account();
// 		if (!$sendmail->send($id)) {
// 			// TODO
// 		}
//		Response::redirect('/manage/member/complete_mail/' . $id);

		$this->set_info_message('追加しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * 追加画面-登録完了(メール)
	 *
	 * @param int $id 発注者アカウントID
	 */
	public function action_complete_mail($id) {
		$data = \Model_Member::find($id);
		if (empty($data) || empty($data['email'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 追加画面-登録完了(QRコード)
	 *
	 * @param int $id 発注者アカウントID
	 */
	public function action_complete_qr($id) {
		$data = \Model_Member::find($id);
		if (empty($data) || empty($data['qr_key'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * QRコードダウンロード
	 *
	 * @param $id 発注者アカウントID
	 */
	public function action_download_qr($id) {
		$data = \Model_Member::find($id);
		if (empty($data) || empty($data['qr_key'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		File::download(QR_IMAGE_PATH . $data['qr_key'] . '.png', $data['code'] . '.png');
	}

	/**
	 * 編集画面-初期表示
	 *
	 * @param int $id 発注者アカウントID
	 */
	public function action_edit($id) {
		$data = \Model_Member::find($id);
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

		$member = \Model_Member::find($data['id']);
		if (empty($member)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->validate_edit($data)) {
			$this->render($data, 'member/edit');
			return;
		}

		if (!$this->update_member($member, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'member/edit');
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

		$member = \Model_Member::find($data['id']);
		if (empty($member)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$this->remove_member($member)) {
			$this->set_error_message('削除に失敗しました');
			$this->render($data, 'member/edit');
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
		$this->process_upload('member_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'member/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Member($this->get_upload_file('member_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'member/upload_csv');
			return;
		}

		if (!$csv->save()) {
			if ($csv->has_error()) {
				$this->render(null, 'member/upload_csv');
				return;
			}else{
				throw new \HttpServerErrorException();
			}
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
		$csv = new \Download_Csv_Member();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_MEMBER, $data);
	}

	/**
	 * 登録用メール送信画面-初期表示
	 *
	 * @param int $id 発注者ID
	 */
	public function action_id_mail($id) {
		$data = \Model_Member::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 登録用メール送信画面-初期表示
	 *
	 * @param int $id 発注者ID
	 */
	public function action_id_mail_send() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$member = \Model_Member::find($data['id']);
		if (empty($member)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if(empty($member->email)){
			Response::redirect('/manage/dialog/not_found');
		}

		$sendmail = new \Sendmail_Account();
		if (!$sendmail->send($data['id'])) {
			$this->set_error_message('メールの送信に失敗しました');
			$this->render(null, 'member/id_mail');
			return;
		}

		if(!$this->update_id_mail_sent_flg($member)){
			Response::redirect('/manage/dialog/not_found');
		}

		Response::redirect('/manage/member/complete_mail/' . $data['id']);
	}

	/**
	 * 登録用メール一斉送信-初期表示
	 */
	public function action_bulk_id_mail() {
		$this->render();
	}

	/**
	 * 登録用メール一斉送信
	 */
	public function action_bulk_id_mail_send() {
		$data = Input::post();

		if (!isset($data['mail_flg_id']) || empty($data['mail_flg_id'])) {
			$this->set_error_message('発注者が選択されていないため、メールを送信しませんでした。');
			Response::redirect('/manage/member');
		}

		foreach ( $data['mail_flg_id'] as $id ) {

			$member = \Model_Member::find($id);
			if (empty($member)) {
				Response::redirect('/manage/dialog/not_found');
			}

			//メールアドレスなしは送らない
			if(empty($member->email)){
				continue;
			}

			$sendmail = new \Sendmail_Account();
			if (!$sendmail->send($id)) {
				$this->set_error_message('メールの送信に失敗しました');
				$this->render(null, 'member/id_mail');
				return;
			}

			if(!$this->update_id_mail_sent_flg($member)){
				Response::redirect('/manage/dialog/not_found');
			}

		}

		$this->set_info_message('ID・パスワード通知メールを一斉送信しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * ログインIDを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_username($data) {
		$username = $data['username'];
		if (!is_null($username) && $username != '') {
			return $username;
		}

		while (true) {
			$username = \Common_Util::random_string(RANDOM_USERNAME_NUM);

			if (!\Model_Member::exists($username, 'username')) {
				return $username;
			}
		}
	}

	/**
	 * パスワードを生成する
	 *
	 * @param array $data フォームデータ
	 */
	private function create_password($data) {
		$password = $data['password'];
		if (!is_null($password) && $password != '') {
			return $password;
		}

		return \Common_Util::random_string(RANDOM_PASSWORD_NUM);
	}

	/**
	 * メール認証用メッセージを生成する
	 * @param $qr_key QR認証キー
	 */
	private function create_mail_auth_message($qr_key) {
		return 'MATMSG:TO:' . MAIL_AUTH_MAIL . ';SUB:' . MAIL_AUTH_TITLE
			. ';BODY:qrkey_' . $qr_key . ';;MAILTO:' . MAIL_AUTH_MAIL . 'SUBJECT:'
			. MAIL_AUTH_TITLE . 'BODY:qrkey_' . $qr_key;
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('code', '発注者コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'members', 'code');
		$validation->add('name', '発注者名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('member_group_id', 'グループ')
			->add_rule('exist', 'member_groups', 'id');
		$validation->add('corporation', '企業名')
			->add_rule('max_length', 40);
		$validation->add('store', '店舗名')
			->add_rule('max_length', 40);
		$validation->add('address', '住所')
			->add_rule('max_length', 500);
		$validation->add('tel', '電話番号')
			->add_rule('numhyphen')
			->add_rule('max_length', 14);
		$validation->add('fax', 'FAX')
			->add_rule('numhyphen')
			->add_rule('max_length', 14);
		$validation->add('username', 'ログインID')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'members', 'username');
		$validation->add('password', 'パスワード')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);
		$validation->add('email', 'メールアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.0', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.1', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.2', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.3', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.4', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('code', '発注者コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('unique', 'members', 'code', $data['id']);
		$validation->add('name', '発注者名')
			->add_rule('required')
			->add_rule('max_length', 40);
		$validation->add('member_group_id', 'グループ')
			->add_rule('exist', 'member_groups', 'id');
		$validation->add('corporation', '企業名')
			->add_rule('max_length', 40);
		$validation->add('store', '店舗名')
			->add_rule('max_length', 40);
		$validation->add('address', '住所')
			->add_rule('max_length', 500);
		$validation->add('tel', '電話番号')
			->add_rule('numhyphen')
			->add_rule('max_length', 14);
		$validation->add('fax', 'FAX')
			->add_rule('numhyphen')
			->add_rule('max_length', 14);
		$validation->add('username', 'ログインID')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 10)
			->add_rule('unique', 'members', 'username', $data['id']);
		$validation->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('min_length', 5)
			->add_rule('max_length', 15);
		$validation->add('email', 'メールアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.0', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.1', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.2', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.3', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('sub_email.4', 'サブアドレス')
			->add_rule('max_length', 255)
			->add_rule('simple_email');

		return $this->validate($validation, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('member_csv', true);
	}

	/**
	 * 削除処理
	 *
	 * @param Model_Member $member 元データ
	 */
	private function remove_member($member) {
		try {
			DB::start_transaction();

			if (!$member->soft_delete()) {
				DB::rollback_transaction();
				return false;
			}

			$this->soft_delete_favorites($member->id);

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * 発注者アカウントを登録する
	 *
	 * @param array $data フォームデータ
	 * @param string $qr_key QR認証キー
	 * @param string $username ログインユーザ名
	 * @param string $password ログインパスワード
	 */
	private function insert_member($data, $qr_key, $username, $password) {
		$fields = array('member_group_id', 'code', 'name', 'corporation', 'store', 'address', 'tel', 'fax', 'email');
		$values = \Common_Util::filter($data, $fields);
		$values['sub_email'] = implode(',', $data['sub_email']);
		$values['username'] = $username;
		$values['password'] = $password;
		$values['qr_key'] = $qr_key;
		$values['status'] = Config::get('define.member_status.enable');

		$model = \Model_Member::forge($values);
		if ($model->save() === false) {
			return false;
		}
		return $model->id;
	}

	/**
	 * 発注者アカウントを更新する
	 *
	 * @param Model_Member $member 元データ
	 * @param array $data フォームデータ
	 */
	private function update_member($member, $data) {
		$fields = array('member_group_id', 'code', 'name', 'corporation', 'store', 'address', 'tel', 'fax', 'email', 'username', 'password');
		\Common_Util::copy($member, $data, $fields);
		$member->sub_email = implode(',', $data['sub_email']);

		return $member->save() !== false;
	}

	/**
	 * お気に入りを削除する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function soft_delete_favorites($member_id) {
		return DB::update('favorites')
			->value('del_flg', DELETED)
			->value('update_user_id', $this->get_user_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('member_id', '=', $member_id)
			->execute();
	}

	/**
	 * ID・パスワード通知送信済みフラグをに1を立てる
	 *
	 * @param Model_Member $member ID・パスワード通知メール送信対象の発注者
	 */
	private function update_id_mail_sent_flg($member){
		$member->id_mail_sent_flg = 1;
		return $member->save() !== false;
	}

}