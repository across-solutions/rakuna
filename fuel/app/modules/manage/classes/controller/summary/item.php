<?php
namespace Manage;

use Fuel\Core\Input;
/**
 * 受注集計コントローラクラス
 */
class Controller_Summary_Item extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = '受注集計';

	/**
	 * 初期表示
	 */
	public function action_index() {
		$this->render();
	}
}