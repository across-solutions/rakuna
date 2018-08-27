<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Arr;
/**
 * ダウンロード履歴一覧プレゼンタクラス
 */
class Presenter_History_Download_Index extends \Presenter_Pagination {
	
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
	}
	
	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Order_Download::query();
		$this->add_condition($query, $data);
		
		return $query->count();
	}
	
	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Order_Download::query();
		$this->add_condition($query, $data);
		$query->order_by('download_datetime', 'desc');
		
		return $query->limit($limit)->offset($offset)->get();
	}
	
	/**
	 * @see Presenter_Pagination::validate_search()
	 */
	protected function validate_search(&$validation, &$data) {
		$start_date = \Common_Util::get_date($data, 'start_year', 'start_month', 'start_day');
		if (!empty($start_date)) {
			$data['start_date'] = $start_date;
			$validation->add('start_date', '開始年月日')
				->add_rule('valid_date', 'Y-m-d');
		}

		$end_date = \Common_Util::get_date($data, 'end_year', 'end_month', 'end_day');
		if (!empty($end_date)) {
			$data['end_date'] = $end_date;
			$validation->add('end_date', '終了年月日')
				->add_rule('valid_date', 'Y-m-d');
		}

		if ( !empty($start_date) && !empty($end_date) ) {
			$data['start_end_date'] = array( $data['start_date'], $data['end_date'] );
			$validation->add('start_end_date', '日付範囲')
				->add_rule('date_reversal');
		}
	}

	/**
	 * 検索条件を付与する
	 * 
	 * @param Query $query Query
	 * @param array $data フォームデータ
	 */
	private function add_condition(&$query, $data) {
		// 日付指定(From)
		$start_date = \Common_Util::get_date($data, 'start_year', 'start_month', 'start_day');
		if (!empty($start_date)) {
			$query->where('download_datetime', '>=' , $start_date);
		}
		
		// 日付指定(To)
		$end_date = \Common_Util::get_date($data, 'end_year', 'end_month', 'end_day');
		if (!empty($end_date)) {
			$query->where('download_datetime', '<', date('Y-m-d', strtotime($end_date . ' +1 day')));
		}
	}
	
	/**
	 * 開始年を取得する
	 */
	private function get_start_year() {
		$oldest = $this->get_oldest_order_download();
		if (empty($oldest)) {
			return date('Y');
		}
		return date('Y', strtotime($oldest->download_datetime));
	}

	/**
	 * 最古のダウンロード履歴を取得する
	 */
	private function get_oldest_order_download() {
		return \Model_Order_Download::find('last', array(
			'order_by' => array(
				'download_datetime' => 'asc'
			)
		));
	}
}