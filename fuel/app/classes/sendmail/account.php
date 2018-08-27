<?php
use Fuel\Core\Uri;
/**
 * ID・パスワード通知メール送信クラス
 */
class Sendmail_Account extends Sendmail_Base {

	/**
	 * システム設定
	 */
	private $setting = null;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		$this->setting = $this->get_setting();
	}

	/**
	 * ID・パスワード通知メールを送信する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	public function send($member_id) {
		$member = Model_Member::find($member_id);
		if (empty($member)) {
			return false;
		}

		$replaces = array();
		$replaces['{$manage-company}'] = $this->setting->corporation_name;
		$replaces['{$manage-name}'] = $this->setting->administrator_name;
		$replaces['{$order-name}'] = $member->name;
		$replaces['{$login-url}'] = ORDER_LOGIN_URL;
		$replaces['{$login-id}'] = $member->username;
		$replaces['{$login-password}'] = $member->password;

		return $this->sendmail($member->email, array(), $replaces);
	}

	/**
	 * @see Sendmail_Base::get_template_mail_div()
	 */
	protected function get_template_mail_div() {
		return Config::get('define.template_mail_div.login');
	}

	/**
	 * システム設定を取得する
	 */
	private function get_setting() {
		return \Model_Setting::query()->get_one();
	}
}