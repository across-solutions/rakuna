<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
/**
 * 集計一覧プレゼンタクラス
 */
class Presenter_Summary_Item_Index extends \Presenter_Pagination {

	/**
	 * 受注日付指定可能期間
	 */
	protected $search_days = 366;

	/**
	 * @see \Fuel\Core\Presenter::before()
	 */
	public function before() {
		parent::before();

		$this->sort_class = function($key) {
			$sort = Input::get('sort');
			if (Config::get('define.search_sort_summary_item.' . $key) == $sort) {
				return 'selected';
			}
			if (is_null($sort) && ('amount_desc' == $key)) {
				return 'selected';
			}
			return '';
		};
	}

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();

		$start_year = ORDER_START_YEAR;
		$end_year = date('Y') + 1;

		$this->years = \Common_Util::range_year2(intval($start_year), $end_year, '----', '年');
		$this->months = \Common_Util::range_month('--', '月');
		$this->days = \Common_Util::range_day('--', '日');
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
  		$query = DB::select(DB::expr('COUNT(*) as count'))
			->from('items')
			->join('order_details', 'LEFT')
				->on('order_details.item_code', '=', 'items.code')
				->and_on('order_details.del_flg', '=', DB::expr(UNDELETED))
			->join('orders', 'LEFT')
				->on('orders.id', '=', 'order_details.order_id')
				->and_on('orders.del_flg', '=', DB::expr(UNDELETED))
			->group_by('items.code', 'items.name', 'order_details.item_name' );

 		$this->add_condition($query, $data);

 		$result = $query->execute()->as_array();

		return count($result);
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select(
			array('items.id', 'item_id'),
			array('items.code', 'item_code'),
			array(DB::expr("IF ( ISNULL(order_details.item_name), items.name, order_details.item_name )"), 'item_name' ),
			array(DB::expr('sum(order_details.amount)'), 'amount'),
			array(DB::expr('sum(order_details.amount_case)'), 'amount_case'))
			->from('items')
			->join('order_details', 'LEFT')
				->on('order_details.item_code', '=', 'items.code')
				->and_on('order_details.del_flg', '=', DB::expr(UNDELETED))
			->join('orders', 'LEFT')
				->on('orders.id', '=', 'order_details.order_id')
				->and_on('orders.del_flg', '=', DB::expr(UNDELETED))
			->limit($limit)
			->offset($offset)
			->group_by('items.code', 'items.name', 'order_details.item_name' );

		$this->add_condition($query, $data);
		$this->add_sort($query, Input::get());

		$result = $query->execute()->as_array();

		return $result;
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
		else {
			$data['order_start_date'] = $order_start_date;
			$validation->add('order_start_date', '受注日付範囲の開始日')
				->add_rule('required');
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

		if ( !empty($order_start_date) ) {
			if ( !empty($order_end_date) ) {
				$data['order_end_date'] = $order_end_date;
			} else {
				$data['order_end_date'] = date('Y-m-d');
			}
			$data['order_start_end_date_interval'] = array( $data['order_start_date'], $data['order_end_date'] );
			$validation->add('order_start_end_date_interval', '受注日付範囲の日数が'.$this->search_days )
				->add_rule('date_interval', $this->search_days );
		}

	}

	/**
	 * 固定条件を付与する
	 *
	 * @param Query $query Query
	 */
	protected function add_default_condition(&$query) {
		$query->where('items.del_flg', '=', DB::expr(UNDELETED));

		//発注されていない商品を含みつつ受注未確定の受注を除くためのWHERE条件
		$query->where_open()
			->or_where('order_download_id',  '!=', null)
			->or_where('order_id', '=', null)
		->where_close();

		$query->where_open()
			->or_where('cancel_flg', '=', 0 )
			->or_where('cancel_flg', '=', null)
		->where_close();
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data フォームデータ
	 */
	private function add_condition(&$query, $data) {
		$this->add_default_condition($query);

		// 商品名または商品コード
		$search_field = Arr::get($data, 'item_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$query->where_open();
				$query->where('items.code',  trim($search_field) );
				$query->or_where_open();
					$query->where('order_details.item_name', trim($search_field) );
					$query->where('order_details.item_name', '!=', null ); //発注されたことのある商品の場合
				$query->or_where_close();
				$query->or_where_open();
					$query->where('items.name', trim($search_field) );
					$query->where('order_details.item_name', '=', null ); //発注されていない商品の場合
				$query->or_where_close();
			$query->where_close();
		}

		// 日付指定(From)
		$start_date = \Common_Util::get_date($data, 'order_start_year', 'order_start_month', 'order_start_day');
		if (!empty($start_date)) {
			$query->where_open();
				$query->where('orders.order_datetime', '>=' , $start_date);
				$query->or_where('orders.order_datetime', '=' , null);
			$query->where_close();
		}

		// 日付指定(To)
		$end_date = \Common_Util::get_date($data, 'order_end_year', 'order_end_month', 'order_end_day');
		if (!empty($end_date)) {
			$query->where_open();
				$query->where('orders.order_datetime', '<', date('Y-m-d', strtotime($end_date . ' +1 day')));
				$query->or_where('orders.order_datetime', '=' , null);
			$query->where_close();
		}
	}

	/**
	 * 並び順を付与する
	 *
	 * @param Query $query Query
	 * @param array $data GETデータ
	 */
	private function add_sort(&$query, $data) {
		$sort = Arr::get($data, 'sort');
		switch ($sort) {
			case Config::get('define.search_sort_summary_item.item_code_desc'):
				$query->order_by('item_code', 'desc');
				break;
			case Config::get('define.search_sort_summary_item.item_code_asc'):
				$query->order_by('item_code', 'asc');
				break;
			case Config::get('define.search_sort_summary_item.item_name_desc'):
				$query->order_by('item_name', 'desc');
				break;
			case Config::get('define.search_sort_summary_item.item_name_asc'):
				$query->order_by('item_name', 'asc');
				break;
			case Config::get('define.search_sort_summary_item.amount_desc'):
				$query->order_by('amount', 'desc');
				break;
			case Config::get('define.search_sort_summary_item.amount_asc'):
				$query->order_by('amount', 'asc');
				break;
			case Config::get('define.search_sort_summary_item.amount_case_desc'):
				$query->order_by('amount_case', 'desc');
				break;
			case Config::get('define.search_sort_summary_item.amount_case_asc'):
				$query->order_by('amount_case', 'asc');
				break;
			default:
				$query->order_by('amount', 'desc');
				break;
		}
	}
}