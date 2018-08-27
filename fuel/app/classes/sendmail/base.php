<?php
use Fuel\Core\Config;
use Fuel\Core\Uri;
use Auth\Auth;
/**
 * テンプレートメール送信クラス
 */
abstract class Sendmail_Base {

	/**
	 * メールテンプレート区分を返す
	 */
	abstract protected function get_template_mail_div();

	/**
	 * メールを送信する
	 *
	 * @param string $to 宛先
	 * @param array $cc CC
	 * @param array $replaces 置換文字列配列
	 */
	protected function sendmail($to, $cc, $replaces) {
		$template_mail = $this->get_template_mail();
		if (empty($template_mail)) {
			return false;
		}

		if (empty($to)) {
			return true;
		}

		$title = $this->replace_text($template_mail->title, $replaces);
		$body = mb_convert_encoding(mb_convert_kana($this->replace_text($template_mail->message, $replaces)),'ISO-2022-JP');

		$email = \Email::forge();
		$email->from($template_mail->mail_from);
		$email->return_path($template_mail->mail_from);
		$email->to($to);
		$email->subject($title);
		$email->body($body);

		if (!empty($cc)) {
			$email->cc($cc);
		}

		return $email->send();
	}

	/**
	 * 文字列を置換する
	 *
	 * @param string $text 文字列
	 * @param array $replaces 置換文字列配列
	 */
	private function replace_text($text, $replaces) {
		return str_replace(array_keys($replaces), array_values($replaces), $text);
	}

	/**
	 * メールテンプレートを取得する
	 */
	protected function get_template_mail() {
		return \Model_Template_Mail::query()
			->where('mail_div', $this->get_template_mail_div())
			->get_one();
	}
}