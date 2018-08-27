<?php
use Fuel\Core\HttpException;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Request;

/**
 * 403エラー
 */
class Exception_403 extends Exception_Base {

	/**
	 * @see Exception_Base::response()
	 */
	public function response() {
		parent::response();

		return new Response(View::forge(Request::main()->module . '::error/403'), 403);
	}
}