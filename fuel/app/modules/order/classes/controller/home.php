<?php
namespace Order;

use Auth\Auth;
use Fuel\Core\Session;
use Fuel\Core\View;
use Fuel\Core\Response;
use Fuel\Core\Request;
/**
 * [発注]ホームコントローラクラス
 */
class Controller_Home extends Controller_Base {

	/**
	 * テンプレート
	 */
	public $template = 'layout/home';
	
	/**
	 * ページタイトル
	 */
	protected $title = 'ようこそMOSへ';
	
	/**
	 * 初期表示
	 */
	public function action_index() {
		$this->render();
	}
}
