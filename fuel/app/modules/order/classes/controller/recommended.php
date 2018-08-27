<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\DB;
/**
 * [発注]いつものコントローラクラス
 */
class Controller_Recommended extends Controller_Base {

	/**
	 * 検索補助表示
	 */
	public $visible_support_search = false;

	/**
	 * ページタイトル
	 */
	protected $title = 'いつものグループ';

	/**
	 * 一覧表示-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 詳細画面-初期表示
	 *
	 * @param int $id いつものグループID
	 */
	public function action_group($id) {
		$recommended_group = \Model_Recommended_Group::find($id);
		if (empty($recommended_group)) {
			throw new \HttpNotFoundException();
		}

		$this->visible_support_search = true;

		$this->render($recommended_group);
	}
}