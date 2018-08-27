<?php
namespace Manage;
use Fuel\Core\Controller;
use Fuel\Core\Response;
use Fuel\Core\View;
use Auth\Auth;
use Fuel\Core\Cookie;
/**
 * エラーコントローラクラス
 */
class Controller_Error extends Controller_Base {

	/**
	 * 403エラー
	 */
	public function action_403() {
		return Response::forge(View::forge('error/403'), 403);
	}

	/**
	 * 404エラー
	 */
	public function action_404() {
		return Response::forge(View::forge('error/404'), 404);
	}
}