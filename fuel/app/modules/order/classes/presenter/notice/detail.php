<?php
namespace Order;
use Fuel\Core\Input;
use Fuel\Core\DB;
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

		$query = DB::select('items.code', 'items.name', 'items.comment', 'items.size', 'items.price_case',
				'items.id', 'items.price', array('item_categories.name', 'category_name'), 'carts.amount',
				'carts.amount_case', array('favorites.id', 'favorite_id'))
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
			->where('items.del_flg', '=', DB::expr(UNDELETED));

		$rows = $query->execute()->as_array();

		$tax_rate = \Common_Setting::get('tax_rate');
		$tax_rounding = \Common_Setting::get('tax_rounding');
		foreach ($rows as &$row) {
			$row['price_tax'] = \Common_Util::add_tax($row['price']);
			$row['price_case_tax'] = \Common_Util::add_tax($row['price_case']);
			$row['amount'] = is_null($row['amount']) ? 0 : $row['amount'];
			$row['amount_case'] = is_null($row['amount_case']) ? 0 : $row['amount_case'];
		}
		return $rows;
	}
}