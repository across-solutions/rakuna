<?php
namespace Order;
/**
 * [発注]商品バーコード読取コントローラクラス
 */
class Controller_Barcode extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = '商品バーコード読取';

	/**
	 * テンプレート
	 */
	public $template = 'layout/home';

	/**
	 * 固有JSファイルリスト
	 */
	protected $page_js = array('barcode.js');
	
	/**
	 * 商品バーコード読取一覧表示-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}
}