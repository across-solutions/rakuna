<?php
namespace  Manage;

use Fuel\Core\Session;

class Controller_Dialog extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('complete', 'not_found', 'forbidden');

	public function action_complete() {
		Session::keep_flash('info_message');
		$this->render();
	}

	public function action_not_found() {
		$this->render();
	}

	public function action_forbidden() {
		$this->render();
	}
}