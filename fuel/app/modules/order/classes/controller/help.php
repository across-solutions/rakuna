<?php
namespace Order;
/**
 * [発注]このページの使い方コントローラクラス
 */
class Controller_Help extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = 'このページの使い方';
	
	/**
	 * 初期表示
	 */
	public function action_index() {
		$this->render();
	}
}