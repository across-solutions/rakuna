<?php
namespace Order;

class Controller_Dialog extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('not_found');

	public function action_not_found() {
		$this->render();
	}
}