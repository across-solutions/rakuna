<?php
namespace Manage;

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

		 if (!Input::is_ajax()) {
		 	throw new \Exception_403();
		 }

		if ($this->check_auth && !Auth::check()) {
			return $this->response_error_auth();
		}
	}

	/**
	 * ログイン中の発注者アカウントIDを取得する
	 */
	protected function get_member_id() {
		return  Auth::instance()->get_user_id()[1];
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

}