<?php
namespace Order;

use Fuel\Core\Response;
use Fuel\Core\DB;
use Fuel\Core\Arr;
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
		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select('items.id', 'items.code', 'items.name', 'items.comment',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size', 'items.type',
				array('item_categories.name', 'category_name'), 'carts.amount',
				'carts.amount_case', array('favorites.id', 'favorite_id'),
				'items.price', 'items.price_case',
				array('item_assigns.price_case', 'assign_price_case'),
				array('item_assigns.price', 'assign_price'),
				array('group_assigns.price_case', 'group_price_case'),
				array('group_assigns.price', 'group_price'),
				'item_assigns.hidden_flg_single', 'item_assigns.hidden_flg_case')
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
			->where('items.hidden_flg', '=', UNDELETED)
			->where('items.del_flg', '=', UNDELETED);

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$item = $query->execute()->current();

		if (!empty($item)) {
			$tax_rate = \Common_Setting::get('tax_rate');
			$tax_rounding = \Common_Setting::get('tax_rounding');
			$price = $this->value($item, 'price', 'assign_price', 'group_price');
			$price_case = $this->value($item, 'price_case', 'assign_price_case', 'group_price_case');
			$item['price'] = $price * $item['size'];
			$item['price_case'] = $price_case * $item['size_case'];
			$item['price_tax'] = \Common_Util::add_tax($item['price']);
			$item['price_case_tax'] = \Common_Util::add_tax($item['price_case']);
			$item['amount'] = is_null($item['amount']) ? 0 : $item['amount'];
			$item['amount_case'] = is_null($item['amount_case']) ? 0 : $item['amount_case'];
		}

		return $item;
	}

	/**
	 * 値を取得する(後のキーが優先される)
	 *
	 * @param array $data データ
	 * @param string $key1 キー1
	 * @param string $key2 キー2
	 * @param string $key3 キー3
	 */
	private function value($data, $key1, $key2 = null, $key3 = null) {
		$value = $data[$key1];

		if (!is_null($key2) && !is_null($data[$key2])) {
			$value = $data[$key2];
		}

		if (!is_null($key3) && !is_null($data[$key3])) {
			$value = $data[$key3];
		}

		return $value;
	}
}