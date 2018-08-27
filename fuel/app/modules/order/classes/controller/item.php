<?php
namespace Order;

use Fuel\Core\Response;
/**
 * [発注]商品コントローラクラス
 */
class Controller_Item extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('detail');

	/**
	 * 検索補助表示
	 */
	public $visible_support_search = true;

	/**
	 * ページタイトル
	 */
	protected $title = '商品検索';

	/**
	 * 商品一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * 商品詳細画面-初期表示
	 *
	 * @param int $id 商品ID
	 */
	public function action_detail($id) {
		$item = $this->get_item($id, $this->get_member_id());

		if (empty($item)) {
			Response::redirect('/order/dialog/not_found');
		}

		$this->render($item);
	}

	/**
	 * 商品を取得する
	 *
	 * @param int $item_id 商品ID
	 * @param int $member_id 発注者ID
	 */
	private function get_item($item_id, $member_id) {
		$query = DB::select('items.code', 'items.name', 'items.comment', 'items.size', 'items.price_case',
				'items.id', 'items.price', array('item_categories.name', 'category_name'), 'carts.amount',
				'carts.amount_case', array('favorites.id', 'favorite_id'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->join('carts', 'LEFT')
				->on('carts.item_id', '=', 'items.id')
				->on('carts.member_id', '=', DB::escape($member_id))
			->join('favorites', 'LEFT')
				->on('favorites.item_code', '=', 'items.code')
				->on('favorites.member_id', '=', DB::escape($member_id))
				->on('favorites.del_flg', '=', DB::escape(UNDELETED))
			->join('order_frequencies', 'LEFT')
				->on('order_frequencies.item_code', '=', 'items.code')
				->on('order_frequencies.member_id', '=', DB::escape($member_id))
				->on('order_frequencies.del_flg', '=', DB::escape(UNDELETED))
			->where('items.id', '=', $item_id)
			->where('items.del_flg', '=', UNDELETED);

		if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'INNER')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		}

		return $query->execute()->current();
	}
}