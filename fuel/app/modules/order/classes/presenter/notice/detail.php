<?php
namespace Order;
use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * お知らせ詳細プレゼンタクラス
 */
class Presenter_Notice_Detail extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$member = \Model_Member::query()->where('id', '=', $this->get_member_id())->get_one();

		$this->before_latest = $this->get_before_latest($member, $this->data->entry_datetime);
		$this->after_latest = $this->get_after_latest($member, $this->data->entry_datetime);

		$this->rows = $this->get_rows();
	}

	/**
	 * ひとつ前のお知らせを取得する
	 *
	 * @param datatime $entry_datetime 登録日時
	 */
	private function get_before_latest($member, $entry_datetime) {
		$query = DB::select('notices.id', 'notices.title')
			->from('notices');
		Common_Notice::add_condition($member, $query);

		$result = $query->where('entry_datetime', '<', $entry_datetime)
				->order_by('entry_datetime', 'desc')
				->limit(1)
				->execute()->as_array();

		if(count($result) > 0){
			return $result[0];
		} else {
			return null;
		}
	}

	/**
	 * ひとつ後のお知らせを取得する
	 *
	 * @param datetime $entry_datetime 登録日時
	 */
	private function get_after_latest($member, $entry_datetime) {
		$query = DB::select('notices.id', 'notices.title')
			->from('notices');
		Common_Notice::add_condition($member, $query);

		$result = $query->where('entry_datetime', '>', $entry_datetime)
				->order_by('entry_datetime', 'asc')
				->limit(1)
				->execute()->as_array();

		if(count($result) > 0){
			return $result[0];
		} else {
			return null;
		}
	}

	/**
	 * 商品情報を取得する
	 */
	protected function get_rows() {
		if (empty($this->data->item_code)) {
			return array();
		}

		$member_id = $this->get_member_id();

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
				->and_on('item_categories.del_flg', '=', DB::expr(UNDELETED))
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
				->and_on('order_frequencies.del_flg', '=', DB::expr(UNDELETED))
			->where('items.code', '=', $this->data->item_code)
			->where('items.hidden_flg', '=', DB::expr(UNDELETED))
			->where('items.del_flg', '=', DB::expr(UNDELETED));

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

		$rows = $query->execute()->as_array();

		$tax_rate = \Common_Setting::get('tax_rate');
		$tax_rounding = \Common_Setting::get('tax_rounding');
		foreach ($rows as &$row) {
			$price = $this->value($row, 'price', 'group_price', 'assign_price');
			$price_case = $this->value($row, 'price_case', 'group_price_case', 'assign_price_case');
			$row['price'] = $price * $row['size'];
			$row['price_case'] = $price_case * $row['size_case'];
			$row['price_tax'] = \Common_Util::add_tax($row['price']);
			$row['price_case_tax'] = \Common_Util::add_tax($row['price_case']);
			$row['amount'] = is_null($row['amount']) ? 0 : $row['amount'];
			$row['amount_case'] = is_null($row['amount_case']) ? 0 : $row['amount_case'];
		}
		return $rows;
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