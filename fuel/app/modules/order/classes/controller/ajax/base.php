<?php
namespace Order;

use Fuel\Core\Controller_Rest;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Presenter;
use Fuel\Core\View;
use Fuel\Core\Request;
use Auth\Auth;
use Fuel\Core\Cache;
/**
 * 基底非同期コントローラクラス
 */
class Controller_Ajax_Base extends Controller_Rest {

	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = true;

	public function before() {
		parent::before();

		if (!Input::is_ajax() || $this->is_maintenance()) {
			throw new \Exception_403();
		}
	}

	/**
	 * ログイン中の発注者アカウントIDを取得する
	 */
	protected function get_member_id() {
		return  Auth::instance()->get_user_id()[1];
	}

	/**
	 * 商品入替レスポンスを取得する
	 */
	protected function response_item_renewal() {
		return $this->create_reposnse_error('item_renewal');
	}

	/**
	 * アラート出力エラーレスポンスを取得する
	 *
	 * @param string $message メッセージ
	 */
	protected function response_error_alert($message) {
		return $this->create_reposnse_error('alert', $message);
	}

	/**
	 * 権限エラーレスポンスを取得する
	 */
	protected function response_error_auth() {
		return $this->create_reposnse_error('auth');
	}

	/**
	 * 404エラーレスポンスを取得する
	 */
	protected function response_error_not_found() {
		return $this->create_reposnse_error('not_found');
	}

	/**
	 * 致命的エラーレスポンスを取得する
	 */
	protected function response_error_fatal() {
		return $this->create_reposnse_error('fatal');
	}

	/**
	 * エラー用レスポンスを生成する
	 *
	 * @param string $error エラーキー
	 * @param string $message エラーメッセージ
	 */
	private function create_reposnse_error($error, $message = '') {
		$data = array();
		$data['error'] = $error;
		$data['message'] = $message;
		return $this->response($data);
	}

	/**
	 * メンテナンス判定
	 *
	 * @return boolean true:表示 false:非表示
	 */
	private function is_maintenance() {
		$result = false;

		try {
			$maintenance_flg = Cache::get(CACHE_KEY_MAINTENANCE_FLG);

			if ($maintenance_flg === DISPLAY) {
				$result = true;
			}
		} catch (\CacheNotFoundException $e) {
			Cache::delete(CACHE_KEY_MAINTENANCE_FLG);

			$setting = \Model_Setting::find('first');
			if ($setting->maintenance_flg === DISPLAY) {
				$result = true;
			}

			// 有効期限24時間
			Cache::set(CACHE_KEY_MAINTENANCE_FLG, $setting->maintenance_flg, 3600 * 24);
		}

		return $result;
	}
}