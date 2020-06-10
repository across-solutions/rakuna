<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Validation;
/**
 * 受注一覧プレゼンタクラス
 */
class Presenter_Order_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();

		$start_year = $this->get_start_year();
		$end_year = date('Y') + 1;
		$this->years = \Common_Util::range_year2(intval($start_year), $end_year, '----', '年');
		$this->months = \Common_Util::range_month('--', '月');
		$this->days = \Common_Util::range_day('--', '日');

		$this->has_comment = function($row) {
			return $this->has_comment($row);
		};

		$this->cancelled = function($row) {
			return $this->cancelled($row);
		};

		$this->is_agency = function($row) {
			return $this->is_agency($row);
		};
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Order::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Order::query();
		$this->add_condition($query, $data);
		$query->order_by('id', 'desc');

		return $query->limit($limit)->offset($offset)->get();
	}

	/**
	 * @see Presenter_Pagination::validate_search()
	 */
	protected function validate_search(&$validation, &$data) {

		$order_start_date = \Common_Util::get_date($data, 'order_start_year', 'order_start_month', 'order_start_day');
		if (!empty($order_start_date)) {
			$data['order_start_date'] = $order_start_date;
			$validation->add('order_start_date', '受注日付範囲の開始日')
				->add_rule('valid_date', 'Y-m-d');
		}

		$order_end_date = \Common_Util::get_date($data, 'order_end_year', 'order_end_month', 'order_end_day');
		if (!empty($order_end_date)) {
			$data['order_end_date'] = $order_end_date;
			$validation->add('order_end_date', '受注日付範囲の終了日')
				->add_rule('valid_date', 'Y-m-d');
		}

		if ( !empty($order_start_date) && !empty($order_end_date) ) {
			$data['order_start_end_date'] = array( $data['order_start_date'], $data['order_end_date'] );
			$validation->add('order_start_end_date', '受注日付範囲')
				->add_rule('date_reversal');
		}

		$delivery_start_date = \Common_Util::get_date($data, 'delivery_start_year', 'delivery_start_month', 'delivery_start_day');
		if (!empty($delivery_start_date)) {
			$data['delivery_start_date'] = $delivery_start_date;
			$validation->add('delivery_start_date', '納品希望日付範囲の開始日')
				->add_rule('valid_date', 'Y-m-d');
		}

		$delivery_end_date = \Common_Util::get_date($data, 'delivery_end_year', 'delivery_end_month', 'delivery_end_day');
		if (!empty($delivery_end_date)) {
			$data['delivery_end_date'] = $delivery_end_date;
			$validation->add('delivery_end_date', '納品希望日付範囲の終了日')
				->add_rule('valid_date', 'Y-m-d');
		}

		if ( !empty($delivery_start_date) && !empty($delivery_end_date) ) {
			$data['delivery_start_end_date'] = array( $data['delivery_start_date'], $data['delivery_end_date'] );
			$validation->add('delivery_start_end_date', '納品希望日付範囲')
				->add_rule('date_reversal');
		}

	}

	/**
	 * 固定条件を付与する
	 *
	 * @param Query $query Query
	 */
	protected function add_default_condition(&$query) {
		$query->where('order_download_id', '=', null);
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data フォームデータ
	 */
	private function add_condition(&$query, $data) {
		$this->add_default_condition($query);

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}

		// 発注日付指定(From)
		$order_start_date = \Common_Util::get_date($data, 'order_start_year', 'order_start_month', 'order_start_day');
		if (!empty($order_start_date)) {
			$query->where('order_datetime', '>=' , $order_start_date);
		}

		// 発注日付指定(To)
		$order_end_date = \Common_Util::get_date($data, 'order_end_year', 'order_end_month', 'order_end_day');
		if (!empty($order_end_date)) {
			$query->where('order_datetime', '<', date('Y-m-d', strtotime($order_end_date . ' +1 day')));
		}

		// 納品日付指定(From)
		$delivery_start_date = \Common_Util::get_date($data, 'delivery_start_year', 'delivery_start_month', 'delivery_start_day');
		if (!empty($delivery_start_date)) {
			$query->where('delivery_date', '>=' , $delivery_start_date);
		}

		// 納品日付指定(To)
		$delivery_end_date = \Common_Util::get_date($data, 'delivery_end_year', 'delivery_end_month', 'delivery_end_day');
		if (!empty($delivery_end_date)) {
			$query->where('delivery_date', '<', date('Y-m-d', strtotime($delivery_end_date . ' +1 day')));
		}

		// 備考
		$comment = Arr::get($data, 'comment');
		if (!is_null($comment) && trim($comment) == '1') {
			$query->where('comment', '!=', NULL);
			$query->where('comment', '!=', '');
		}

		// 削除データ
		$del = Arr::get($data, 'del');
		if (is_null($del) || trim($del) != '1') {
			$query->where('cancel_flg', '=', 0);
		}
	}

	/**
	 * コメントの有無
	 *
	 * @param array $row 行データ
	 */
	private function has_comment($row) {
		$comment = Arr::get($row, 'comment');
		return !is_null($comment) && $comment != '';
	}

	/**
	 * キャンセルの有無
	 *
	 * @param array $row 行データ
	 */
	private function cancelled($row) {
		$cancel_flg = Arr::get($row, 'cancel_flg');
		return !empty($cancel_flg);
	}

	/**
	 * 代理発注の有無
	 *
	 * @param array $row 行データ
	 */
	private function is_agency($row) {
		$agency_order_flg = Arr::get($row, 'agency_order_flg');
		return !empty($agency_order_flg);
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
	 * 最古の受注データを取得する
	 */
	private function get_oldest_order() {
		return \Model_Order::find('last', array(
			'order_by' => array(
				'order_datetime' => 'asc'
			)
		));
	}
}