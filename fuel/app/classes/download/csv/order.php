<?php
use Fuel\Core\Config;
use Fuel\Core\DB;
use Fuel\Core\Format;
/**
 * 受注CSVダウンロードクラス
 */
class Download_Csv_Order extends Download_Csv_Base {

	private $tmp_id = null;

	private $line_num = 0;

	/**
	 * CSVファイルを出力する
	 *
	 * @param int $order_download_id 受注ダウンロード履歴ID
	 * @param boolean $header ヘッダ有無
	 */
	public function output_csv($order_download_id, $header = false) {
		$params = array();
		$params['order_download_id'] = $order_download_id;
		$data = $this->get_csv_data($params, $header);

		return File::create(ORDER_CSV_PATH, $order_download_id . '.csv',
			mb_convert_encoding(Format::forge($data)->to_csv(), 'SJIS-win', 'UTF-8'));
	}

	/**
	 * @see Download_Csv_Base::get_format_div()
	 */
	protected function get_format_div() {
		return Config::get('define.csv_format_div.order');
	}

	/**
	 * @see Download_Csv_Base::get_data()
	 */
	protected function get_data($params) {
		$query = DB::select(
			array('orders.id', 'order_id'),
			array('orders.member_code', 'member_code'),
			array('orders.member_name', 'member_name'),
			array('orders.order_datetime', 'order_datetime'),
			array('orders.delivery_date', 'delivery_date'),
			array('orders.comment', 'comment'),
			array('order_details.id', 'order_detail_id'),
			array('order_details.category_code', 'category_code'),
			array('order_details.category_name', 'category_name'),
			array('order_details.item_code', 'item_code'),
			array('order_details.item_name', 'item_name'),
			array('order_details.price_case_tax', 'price_case'),
			array('order_details.amount_case', 'amount_case'),
			array('order_details.price_tax', 'price'),
			array('order_details.amount', 'amount')
		)
		->from('orders')
		->join('order_details', 'INNER')
			->on('order_details.order_id', '=', 'orders.id')
			->and_on('order_details.del_flg', '=', DB::expr(UNDELETED));

		$this->add_condition($query, $params);
		$query->order_by('orders.id', 'asc');
		$query->order_by('order_details.id', 'asc');

		return $query->execute();
	}

	/**
	 * @see Download_Csv_Base::modifier()
	 */
	protected function modifier($counter, $data, $key) {
		if ($data['order_id'] != $this->tmp_id) {
			$this->tmp_id = $data['order_id'];
			$this->line_num = 0;
		}

		if ($key == 'line_num') {
			$this->line_num++;
			return $this->line_num;
		}

		return parent::modifier($counter, $data, $key);
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('orders.order_download_id', '=', $data['order_download_id']);
	}
}