<?php
namespace Order;

use Common_Util;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\Arr;
/**
 * レジプレゼンタクラス
 */
class Presenter_Register_Index extends Presenter_Item_Index {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->order_types = $this->get_order_type_list();

		$this->shipping_div = array('' => '');
		$this->shipping_div += Config::get('define.shipping_div');

		$this->warehouse_div = array('' => '');
		$this->warehouse_div += Config::get('define.warehouse_div');

		$this->shipping_dates = $this->get_shipping_dates();

		$this->delivery_dates = $this->get_delivery_dates();

		$this->deliveries = $this->get_delivery_list();
	}

	/**
	 * @see Presenter_Item_Index::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$result = parent::get_rows($data, $limit, $offset);
		$list = array();
		foreach ($result as $row) {
			parent::modifier($row);
			$order_type = is_null(Arr::get($row, 'order_type')) ? 1 : Arr::get($row, 'order_type');
			if (!isset($list[$order_type])) {
				$list[$order_type] = array();
			}
			$list[$order_type][] = $row;
		}
		ksort($list);
		return $list;
	}

	/**
	 * @see Presenter_Item_Index::modifier()
	 */
	protected function modifier(&$row) {
	}

	/**
	 * 出荷日取得
	 *
	 * @return array $dates 出荷日
	 */
	private function get_shipping_dates() {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$lead_time = Arr::get($member, 'lead_time');

		$limit = 28;
		$day = 2;

		if (!is_null($lead_time)) {
			$day += intval($lead_time);
		}

		$start = date('Y-m-d', strtotime('+' . $day . ' day'));
		$end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));

		$dates = \Common_Util::range_date($start, $limit, '');

		return $dates;
	}

	/**
	 * 納期取得
	 *
	 * @return array $dates 納期
	 */
	private function get_delivery_dates() {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		// $lead_time = Arr::get($member, 'lead_time');

		$limit = 30;
		// $day = 2;

		// if (!is_null($lead_time)) {
		// 	$day += intval($lead_time);
		// }

		// $start = date('Y-m-d', strtotime('+' . $day . ' day'));
		// $end = date('Y-m-d', strtotime($limit . ' day', strtotime($start)));
		$start = Common_Util::get_nearest_delivery_date($member->code);

		$dates = \Common_Util::range_date($start, $limit, '');

		return $dates;
	}

	/**
	 * 発注タイプリストを取得する
	 */
	private function get_order_type_list() {
		$query = DB::select('order_types.id', 'order_types.name')
					->from('order_types')
					->where('order_types.del_flg', '=', DB::escape(UNDELETED))
					->order_by('order_types.id', 'asc');

		$order_types = $query->execute()->as_array();

		$list = array();
		if (!empty($order_types)) {
			$list[''] = '';
			foreach ($order_types as $order_type) {
				$list[$order_type['id']] = $order_type['name'];
			}
		}

		return $list;
	}

	/**
	 * 納品先リストを取得する
	 */
	private function get_delivery_list() {
		$member_id = $this->get_member_id();
		$member_code = \Common_Member::get_member_code();

		$query = DB::select('deliveries.code', 'deliveries.name')
					->from('deliveries')
					->where('deliveries.member_code', '=', $member_code)
					->where('deliveries.del_flg', '=', DB::escape(UNDELETED))
					->order_by('deliveries.member_code', 'asc')
					->order_by('deliveries.code', 'asc');

		$deliveries = $query->execute()->as_array();

		$list = array();
		if (!empty($deliveries)) {
			$list[''] = '';
			foreach ($deliveries as $delivery) {
				$list[$delivery['code']] = $delivery['name'];
			}
		}

		return $list;
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