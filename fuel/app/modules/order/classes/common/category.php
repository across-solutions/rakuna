<?php
namespace Order;

use Fuel\Core\DB;
/**
 * 商品カテゴリ取得クラス
 */
class Common_Category {
	
	/**
	 * 商品カテゴリリストを取得する
	 * 
	 * @param int $member_id 発注者アカウントID
	 * @param string $empty 未選択時の文言
	 */
	public static function list_category($member_id, $empty = false) {
		$query = DB::select(array('item_categories.name', 'name'), array('item_categories.id', 'id'))
			->from('item_categories')
			->join('items', 'INNER')
				->on('item_categories.id', '=', 'items.item_category_id')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED));

		if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns')
				->on('items.code', '=', 'item_assigns.item_code')
				->and_on('item_assigns.member_id', '=', DB::expr($member_id))
				->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		}
		
		$query->group_by('item_categories.name', 'item_categories.id', 'item_categories.code');
		$query->order_by('item_categories.code', 'ASC');
		
		$categories = $query->execute();
		
		$list = array();
		if (!is_null($empty) && $empty !== false) {
			$list[''] = $empty;
		}
		foreach ($categories as $category) {
			$list[$category['id']] = $category['name'];
		}
		
		return $list;
	}
}