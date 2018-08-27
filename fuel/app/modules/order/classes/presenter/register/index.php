<?php
namespace Order;
use Fuel\Core\DB;
/**
 * レジプレゼンタクラス
 */
class Presenter_Register_Index extends Presenter_Item_Index {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->dates = $this->get_delivery_dates();
	}

	/**
	 * 納品希望日取得
	 *
	 * @return array $dates 納品希望日
	 */
	private function get_delivery_dates() {

		$limit = 10;
		$start = date('Y-m-d', strtotime('+1 day'));
		$end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));

		$dates = \Common_Util::range_date($start, $limit, '指定しない');

		$holidays = \Model_Holiday::query()
			->where('date', '>=', $start)
			->where('date', '<=', $end)
			->get();

		foreach($holidays as $holiday){
			$key = date('Ymd', strtotime($holiday->date));
			unset($dates[$key]);
		}

		return $dates;
	}

	/**
	 * @see \Order\Presenter_Item_Index::add_condition()
	 */
	protected function add_condition(&$query, $data) {
		$query->where('items.del_flg', '=', DB::expr(UNDELETED));
		$query->where(DB::expr('EXISTS (select 1 from carts where items.id = carts.item_id and member_id = ' . $this->get_member_id() . ')'));
	}

	/**
	 * @see Presenter_Pagination::per_page()
	 */
	protected function per_page() {
		return 9999;
	}
}