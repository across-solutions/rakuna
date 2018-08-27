<?php
namespace Order;

use Order\Controller_Base;
use Fuel\Core\DB;
use Auth\Auth;
use Fuel\Core\Cookie;
/**
 * インフォメーションコントローラクラス
 */
class Controller_Information extends Controller_Base {

	/**
	 * テンプレート
	 */
	public $template = 'layout/none';
	
	/**
	 * ページタイトル
	 */
	protected $title = 'お知らせ';
	
	/**
	 * 商品入れ替え
	 */
	public function action_item_renewal() {
		$member_id = $this->get_member_id();
		
		$this->clear_cart($member_id);
		
		$this->render();
	}
	
	/**
	 * カート更新
	 */
	public function action_cart_updated() {
		$this->render();
	}
	
	/**
	 * 同時ログイン
	 */
	public function action_multiple_login() {
		Cookie::delete(COOKIE_KEY_ORDER_AUTO_LOGIN);
		Auth::logout();
		
		$this->render();
	}
	
	/**
	 * カートを削除する
	 * 
	 * @param int $member_id 発注者アカウントID
	 */
	private function clear_cart($member_id) {
		return DB::delete('carts')->where('member_id', '=', $member_id)->execute();
	}
}