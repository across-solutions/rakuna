<?php
use Fuel\Core\Uri;
/**
 * お知らせメール送信クラス
 */
class Sendmail_Notice extends Sendmail_Base {

	/**
	 * システム設定
	 */
	private $setting = null;

	/**
	 * お知らせ
	 */
	private $notice = null;

	/**
	 * コンストラクタ
	 */
	public function __construct($notice_id) {
		$this->setting = $this->get_setting();

		$this->notice = Model_Notice::find($notice_id);
	}

	/**
	 * お知らせメールを送信する
	 *
	 * @param int $member_id 発注者ID
	 */
	public function send($member_id) {
		$member = Model_Member::find($member_id);
		if (empty($member)) {
			return false;
		}
		$sub_email = explode(',', $member->sub_email);
		$sub_addresses = array();
		foreach ($sub_email as $email) {
			if (!empty($email)) {
				$sub_addresses[] = $email;
			}
		}

		$replaces = array();
		$replaces['{$manage-company}'] = $this->setting->corporation_name;
		$replaces['{$manage-name}'] = $this->setting->administrator_name;
		$replaces['{$order-name}'] = $member->name;
		$replaces['{$info-title}'] = $this->notice->title;
		$replaces['{$info-text}'] = $this->notice->message;
		$replaces['{$info-url}'] = Uri::base(false) . 'order/notice/detail/' . $this->notice->id;

		return $this->sendmail($member->email, $sub_addresses, $replaces);
	}

	/**
	 * @see Sendmail_Base::get_template_mail_div()
	 */
	protected function get_template_mail_div() {
		return Config::get('define.template_mail_div.notice');
	}

	/**
	 * システム設定を取得する
	 */
	private function get_setting() {
		return \Model_Setting::query()->get_one();
	}
}