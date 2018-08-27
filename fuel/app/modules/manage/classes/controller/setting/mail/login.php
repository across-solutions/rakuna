<?php
namespace Manage;
use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\Response;
/**
 * メール設定-登録用メール設定コントローラクラス
 */
class Controller_Setting_Mail_Login extends Controller_Setting_Mail_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = 'メール設定-登録用メール設定';

	/**
	 * @see \Manage\Controller_Setting_Mail_Base::get_template_mail_div()
	 */
	protected function get_template_mail_div() {
		return Config::get('define.template_mail_div.login');
	}
	
	/**
	 * 保存処理
	 */
	public function action_save() {
		$data = Input::post();
		if (empty($data)) {
			throw new \Exception_403();
		}
		
		$template_mail = $this->get_template_mail();
		if (empty($template_mail)) {
			throw new \HttpServerErrorException();
		}
		
		if (!$this->validate_edit($data)) {
			$this->render($data, 'setting/mail/login/index');
			return;
		}

		if (!$this->update_template_mail($template_mail, $data)) {
			$this->set_error_message('更新に失敗しました');
			$this->render($data, 'setting/mail/login/index');
			return;
		}
		
		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/mail/login');
	}
}