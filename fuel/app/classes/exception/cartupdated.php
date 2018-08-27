<?php

use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Request;
class Exception_Cartupdated extends Exception_Base {
	
	public function response() {
		parent::response();

		Response::redirect('/order/information/cart_updated');
	}
}