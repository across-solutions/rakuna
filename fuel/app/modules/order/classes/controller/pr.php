<?php
namespace Order;
/**
 * [発注]PR商品コントローラクラス
 */
class Controller_Pr extends Controller_Base {

	/**
	 * 検索補助表示
	 */
	public $visible_support_search = true;
	
	/**
	 * ページタイトル
	 */
	protected $title = 'PR商品';
	
	/**
	 * 一覧表示-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}
}