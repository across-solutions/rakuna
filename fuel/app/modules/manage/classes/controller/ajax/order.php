<?php
namespace Manage;

use Auth\Auth;
/**
 * 受注非同期コントローラクラス
 */
class Controller_Ajax_Order extends Controller_Ajax_Base {

	public function get_info($id) {
		$order = \Model_Order::find($id);

		if(empty($order)){
			$this->response_error_not_found();
			return;
		}

		return $this->response($order);
	}
}