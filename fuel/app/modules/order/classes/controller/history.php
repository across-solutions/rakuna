<?php
namespace Order;
/**
 * [発注]発注履歴コントローラクラス
 */
class Controller_History extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = '発注履歴';
	
	/**
	 * 一覧表示-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}
	
	/**
	 * 詳細画面-初期表示
	 * 
	 * @param int $id 受注ID
	 */
	public function action_detail($id) {
		$order = \Model_Order::find($id, array(
			'where' => array(
				'member_id' => $this->get_member_id()
			)
		));
		if (empty($order)) {
			throw new \HttpNotFoundException();
		}
		
		$this->render($order);
	}
}