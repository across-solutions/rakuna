<?php

use Fuel\Core\Response;
class Exception_Multiplelogin extends Exception_Base {
	
	public function response() {
		parent::response();

		Response::redirect('/order/information/multiple_login');
	}
}