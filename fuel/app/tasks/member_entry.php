<?php
namespace Fuel\Tasks;
use Fuel\Core\Log;
use Fuel\Core\Config;
/**
 * メールアドレス登録バッチ
 */
class Member_Entry {

	/**
	 * バッチ処理
	 */
	public static function run() {
		$structure = self::decode();
		if (empty($structure)) {
			Log::error('mail_decode_error');
			return;
		}

		$from = self::get_from($structure);
		if (empty($from)) {
			Log::error('from_address_not_found');
			return;
		}

		$body = self::get_body($structure);
		if (empty($body)) {
			Log::error('body_not_found');
			return;
		}

		$member = self::get_member($body);
		if (empty($member)) {
			Log::error('unknown_qr_key[' . $from . '][' . $body . ']');
			return;
		}
		if (!empty($member->email)) {
			Log::error('registered_email[' . $from . '][' . $member->id . ']');
			return;
		}
		if (!self::update_member($member, $from)) {
			Log::error('member_update_error[' . $from . '][' . $member->id . ']');
			return;
		}

		$sendmail = new \Sendmail_Account();
		if (!$sendmail->send($member->id)) {
			Log::error('mail_send_error[' . $from . '][' . $member->id . ']');
			return;
		}

		Log::error('member_update_complete[' . $from . '][' . $member->id . ']');
	}

	/**
	 * デコード処理
	 */
	private static function decode() {
		require_once('Mail/mimeDecode.php');

		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;

		$input = file_get_contents('php://stdin');

		$decoder = new \Mail_mimeDecode($input);
		return $decoder->decode($params);
	}

	/**
	 * メールアドレスを取得する
	 *
	 * @param $structure デコード結果
	 */
	private static function get_from($structure) {
		$from = $structure->headers['from'];
		$from = mb_decode_mimeheader($from);
		$from = mb_convert_encoding($from, mb_internal_encoding(), 'auto');
		if (preg_match('/<(.*)>/', $from, $matches) === 0) {
			return $from;
		}
		return $matches[1];
	}

	/**
	 * メール本文を取得する
	 *
	 * @param $structure デコード結果
	 */
	private static function get_body($structure) {
		$body = '';
		switch(strtolower($structure->ctype_primary)){
			case 'text':
				$body = $structure->body;
				break;
			case 'multipart':
				foreach ($structure->parts as $part) {
					switch(strtolower($part->ctype_primary)){
						case 'text':
							$body = $part->body;
							break;
					}
				}
				break;
		}

		preg_match('/qrkey_(.{'.RANDOM_QR_KEY_NUM.'})/',$body,$keyval);
		return $keyval[1];
	}

	/**
	 * 発注者を取得する
	 *
	 * @param $key QRキー
	 */
	private static function get_member($key) {
		return \Model_Member::query()
			->where('qr_key', '=', $key)
			->get_one();
	}

	/**
	 * メールアドレスを登録する
	 *
	 * @param $member 発注者情報
	 * @param $email メールアドレス
	 */
	private static function update_member($member, $email) {
		$member->email = $email;
		$member->status = Config::get('define.member_status.enable');

		return $member->save() !== false;
	}
}