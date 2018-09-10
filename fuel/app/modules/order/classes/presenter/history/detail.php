<?php
namespace Order;
use Fuel\Core\Arr;
/**
 * 発注履歴詳細プレゼンタクラス
 */
class Presenter_History_Detail extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->before_latest = $this->get_before_latest($this->data->order_datetime);
		$this->after_latest = $this->get_after_latest($this->data->order_datetime);

		$this->shipping_date = function($data, $key) {
			return $this->shipping_date($data, $key);
		};

		$this->delivery_date = function($data, $key) {
			return $this->delivery_date($data, $key);
		};

		$this->comment = function($data, $key) {
			return $this->comment($data, $key);
		};

		$this->history_url = function($data) {
			return $this->history_url($data);
		};
	}

	/**
	 * ひとつ前の受注を取得する
	 *
	 * @param datetime $order_datetime 受注日時
	 */
	private function get_before_latest($order_datetime) {
		return \Model_Order::query()
			->where('member_id', '=', $this->get_member_id())
			->where('order_datetime', '<', $order_datetime)
			->order_by('order_datetime', 'desc')
			->get_one();
	}

	/**
	 * ひとつ後の受注を取得する
	 *
	 * @param datetime $order_datetime 受注日時
	 */
	private function get_after_latest($order_datetime) {
		return \Model_Order::query()
			->where('member_id', '=', $this->get_member_id())
			->where('order_datetime', '>', $order_datetime)
			->order_by('order_datetime', 'asc')
			->get_one();
	}

	/**
	 * 出荷予定日を取得する
	 *
	 * @param array $data データ配列
	 * @param string $key キー
	 */
	private function shipping_date($data, $key) {
		$shipping_date = Arr::get($data, $key);
		if (empty($shipping_date)) {
			return '';
		}

		return \Common_Util::add_week_on_date($shipping_date);
	}

	/**
	 * 納期を取得する
	 *
	 * @param array $data データ配列
	 * @param string $key キー
	 */
	private function delivery_date($data, $key) {
		$delivery_date = Arr::get($data, $key);
		if (empty($delivery_date)) {
			return '';
		}

		return \Common_Util::add_week_on_date($delivery_date);
	}

	/**
	 * 備考を取得する
	 *
	 * @param array $data データ配列
	 * @param string $key キー
	 */
	private function comment($data, $key) {
		$comment = Arr::get($data, $key);
		if (is_null($comment) || $comment == '') {
			return 'メッセージはありません';
		}
		return nl2br($comment);
	}

	/**
	 * 発注履歴一覧画面URLを取得する
	 *
	 * @param array $data データ配列
	 */
	private function history_url($data) {
		$url = '/order/history';

		$order_datetime = Arr::get($data, 'order_datetime');
		if (empty($order_datetime)) {
			return $url;
		}

		$url .= '?year=' . date('Y', strtotime($order_datetime))
			. '&month=' . date('m', strtotime($order_datetime))
			. '&day=' . date('d', strtotime($order_datetime));

		$url .= '#' . date('Ymd', strtotime($order_datetime));

		return $url;
	}
}