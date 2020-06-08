<?php
namespace Order;
use Fuel\Core\Input;
use Fuel\Core\Arr;
/**
 * 発注履歴一覧プレゼンタクラス
 */
class Presenter_History_Index extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$start_year = $this->get_start_year();
		$end_year = date('Y') + 1;
		$this->years = \Common_Util::range_year2(intval($start_year), $end_year, '----');
		$this->months = \Common_Util::range_month('--');

		$this->year = Input::get('year');
		$this->month = Input::get('month');
		if (is_null($this->year) && is_null($this->month)) {
			$newest_order = $this->get_newest_order();
			if (!empty($newest_order)) {
				$this->year = date('Y', strtotime($newest_order->order_datetime));
				$this->month = date('m', strtotime($newest_order->order_datetime));
			} else {
				$this->year = date('Y');
				$this->month = date('m');
			}
		}

		$this->rows = $this->get_orders($this->year, $this->month);

		$this->cancelled = function($data) {
			return $this->cancelled($data);
		};
	}

	/**
	 * 開始年を取得する
	 */
	private function get_start_year() {
		$oldest_order = $this->get_oldest_order();
		if (empty($oldest_order)) {
			return date('Y');
		}
		return date('Y', strtotime($oldest_order->order_datetime));
	}

	/**
	 * 受注データを取得する
	 *
	 * @param string $date 日付
	 */
	private function get_orders($year, $month) {
		$start = date('Y-m-d H:i:s', strtotime($year . '-' . $month . '-01 00:00:00'));
		$end = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($start)));

		$orders = \Model_Order::find('all', array(
			'where' => array(
				'member_id' => $this->get_member_id(),
				array('order_datetime', '>=', $start),
				array('order_datetime', '<', $end)
			),
			'order_by' => array(
				'id' => 'desc'
			)
		));

		$results = array();
		foreach ($orders as $order) {
			$date = date('Ymd', strtotime($order->order_datetime));
			if (!isset($results[$date])) {
				$results[$date] = array();
			}
			$results[$date][] = $order;
		}
		return $results;
	}

	/**
	 * 最新の受注データを取得する
	 */
	private function get_newest_order() {
		return \Model_Order::find('last', array(
			'where' => array(
				'member_id' => $this->get_member_id()
			),
			'order_by' => array(
				'id' => 'desc'
			)
		));
	}

	/**
	 * 最古の受注データを取得する
	 */
	private function get_oldest_order() {
		return \Model_Order::find('last', array(
			'where' => array(
				'member_id' => $this->get_member_id()
			),
			'order_by' => array(
				'id' => 'asc'
			)
		));
	}

	/**
	 * キャンセルの有無
	 *
	 * @param Model_Order $order 受注
	 */
	private function cancelled($order) {
		$cancel_flg = Arr::get($order, 'cancel_flg');
		return !empty($cancel_flg);
	}
}