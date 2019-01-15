<?php
namespace Order;
use Fuel\Core\DB;
use Fuel\Core\Response;
use Fuel\Core\Arr;
/**
 * いつものグループ詳細プレゼンタクラス
 */
class Presenter_Recommended_Group extends Presenter_Item_Index {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		$member_id = $this->get_member_id();

		$this->this_recommended_group_id = $this->data->id;

		$this->this_recommended_group_name = $this->data->name;

		$this->recommended_groups = $this->list_recommended_group($member_id, $this->this_recommended_group_id, $this->this_recommended_group_name);

		$view_auth_flag = $this->check_view_auth($member_id, $this->this_recommended_group_id);
		if ( $view_auth_flag === false ) {
			Response::redirect('/order/error/404');
		}

		parent::view();
	}

	/**
	 * @see \Order\Presenter_Item_Index::get_count()
	 */
	protected function get_count($data) {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select(DB::expr('COUNT(*) as count'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->and_on('item_categories.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_items', 'LEFT')
				->on('recommended_items.item_code', '=', 'items.code')
				->and_on('recommended_items.recommended_group_id', '=', DB::expr($this->this_recommended_group_id))
				->and_on('recommended_items.del_flg', '=', DB::expr(UNDELETED))
			->join('carts', 'LEFT')
				->on('carts.item_id', '=', 'items.id')
				->and_on('carts.member_id', '=', DB::expr($member_id))
			->join('favorites', 'LEFT')
				->on('favorites.item_code', '=', 'items.code')
				->and_on('favorites.member_id', '=', DB::expr($member_id))
				->and_on('favorites.del_flg', '=', DB::expr(UNDELETED))
			->join('order_frequencies', 'LEFT')
				->on('order_frequencies.item_code', '=', 'items.code')
				->and_on('order_frequencies.member_id', '=', DB::expr($member_id))
				->and_on('order_frequencies.del_flg', '=', DB::expr(UNDELETED));

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
			->on('item_assigns.item_code', '=', 'items.code')
			->and_on('item_assigns.member_id', '=', DB::expr($member_id))
			->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$this->add_condition($query, $data);

		$result = $query->execute()->current();

		return $result['count'];
	}

	/**
	 * @see \Order\Presenter_Item_Index::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select('items.id', 'items.code', 'items.name', 'items.comment',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size',
				 array('item_categories.code', 'category_code'), array('item_categories.name', 'category_name'), 'carts.amount',
				'carts.amount_case', array('favorites.id', 'favorite_id'), array('recommended_items.recommended_group_id', 'recommended_group_id'),
				'items.price', 'items.price_case',
				array('item_assigns.price_case', 'assign_price_case'),
				array('item_assigns.price', 'assign_price'),
				array('group_assigns.price_case', 'group_price_case'),
				array('group_assigns.price', 'group_price'),
				'item_assigns.hidden_flg_single', 'item_assigns.hidden_flg_case',
				array('recommended_items.sort_num', 'sort_num')
				 )
				->from('items')
				->join('item_categories', 'LEFT')
					->on('item_categories.id', '=', 'items.item_category_id')
					->and_on('item_categories.del_flg', '=', DB::expr(UNDELETED))
				->join('recommended_items', 'LEFT')
					->on('recommended_items.item_code', '=', 'items.code')
					->and_on('recommended_items.recommended_group_id', '=', DB::expr($this->this_recommended_group_id))
					->and_on('recommended_items.del_flg', '=', DB::expr(UNDELETED))
				->join('carts', 'LEFT')
					->on('carts.item_id', '=', 'items.id')
					->and_on('carts.member_id', '=', DB::expr($member_id))
				->join('favorites', 'LEFT')
					->on('favorites.item_code', '=', 'items.code')
					->and_on('favorites.member_id', '=', DB::expr($member_id))
					->and_on('favorites.del_flg', '=', DB::expr(UNDELETED))
				->join('order_frequencies', 'LEFT')
					->on('order_frequencies.item_code', '=', 'items.code')
					->and_on('order_frequencies.member_id', '=', DB::expr($member_id))
					->and_on('order_frequencies.del_flg', '=', DB::expr(UNDELETED));

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
			->on('item_assigns.item_code', '=', 'items.code')
			->and_on('item_assigns.member_id', '=', DB::expr($member_id))
			->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$this->add_condition($query, $data);
		$query->order_by('sort_num', 'desc');

		return $query->execute()->as_array();
	}

	/**
	 * @see \Order\Presenter_Item_Index::add_condition()
	 */
	protected function add_condition(&$query, $data) {
		parent::add_condition($query, $data);

		// いつものグループ選択セレクトボックス
		$select_recommended_group_id = Arr::get($data, 'recommended_group');
		if (!is_null($select_recommended_group_id) && trim($select_recommended_group_id) != '') {
			$query->where('recommended_items.recommended_group_id', '=', DB::expr($select_recommended_group_id) );
		}
		else {
			$query->where('recommended_items.recommended_group_id', '=', DB::expr($this->this_recommended_group_id) );
		}
	}

	/**
	 * いつものグループを見る権限があるかどうかのチェック
	 *
	 * @param int $member_id
	 * @param int $recommended_group_id
	 */
	private function check_view_auth($member_id, $recommended_group_id){

		$query = DB::select('recommended_groups.id')
			->from('recommended_groups')
				->where('recommended_groups.id', '=', DB::expr($recommended_group_id))
				->where('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_group_assigns', 'INNER')
				->on('recommended_group_assigns.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_group_assigns.recommended_group_id', '=', DB::expr($recommended_group_id))
				->and_on('recommended_group_assigns.member_id', '=', DB::expr($member_id))
				->and_on('recommended_group_assigns.del_flg', '=', DB::expr(UNDELETED));

		$result = $query->execute()->as_array();

		if ( count($result) > 0 ){
			return true;
		}

		return false;
	}

	/**
	 * いつものグループの一覧を取得
	 *
	 * @param int $member_id
	 * @param int $recommended_group_id
	 * @param string $recommended_group_name
	 */
	private function list_recommended_group($member_id, $recommended_group_id, $recommended_group_name) {
		$query = DB::select(array('recommended_groups.name', 'name'), array('recommended_groups.id', 'id'))
			->from('recommended_groups')
			->join('recommended_group_assigns', 'INNER')
				->on('recommended_group_assigns.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_group_assigns.member_id', '=', DB::expr($member_id) )
				->and_on('recommended_group_assigns.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_items', 'LEFT')
				->on('recommended_items.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_items.del_flg', '=', DB::expr(UNDELETED))
			->join('items', 'LEFT')
				->on('items.code', '=', 'recommended_items.item_code')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED));
/*
		if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'INNER')
				->on('items.code', '=', 'item_assigns.item_code')
				->and_on('item_assigns.member_id', '=', DB::expr($member_id))
				->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		}
*/
		$query->where('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->group_by('recommended_groups.id')
			->order_by('recommended_groups.id', 'asc');

		$recommended_groups = $query->execute();

		$list = array();
		$list[$recommended_group_id] = $recommended_group_name; //現在選択されているいつものグループを最初に

		foreach ($recommended_groups as $recommended_group) {
			$list[$recommended_group['id']] = $recommended_group['name'];
		}

		return $list;
	}

}