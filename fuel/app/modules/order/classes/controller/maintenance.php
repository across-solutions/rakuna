<?php
namespace Order;

use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Input;
use Fuel\Core\Cache;
/**
 * [発注]メンテナンスコントローラクラス
 */
class Controller_Maintenance extends Controller_Base {

	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = false;

	/**
	 * メンテナンスチェックの有無
	 */
	protected $check_maintenance = false;

	/**
	 * ページタイトル
	 */
	protected $title = 'メンテナンス';

	/**
	 * 初期表示
	 */
	public function action_index() {
		if ($this->is_maintenance() === false) {
			Cache::delete(CACHE_KEY_MAINTENANCE_FLG);
			Response::redirect('/order/home');
		}

		return Response::forge(View::forge('maintenance/index'));
	}
}