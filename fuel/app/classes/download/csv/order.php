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
			chr(255) . chr(254). mb_convert_encoding(Format::forge($data)->to_csv(null, "\t", null, array()), 'UTF-16LE', 'UTF-8'));
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
			array('orders.sales_person_code', 'sales_person_code'),
			array('orders.sales_person_name', 'sales_person_name'),
			array('orders.department_code', 'department_code'),
			array('orders.tax_rate', 'tax_rate'),
			array('orders.delivery_kind', 'delivery_kind'),
			array('orders.delivery_code', 'delivery_code'),
			array('orders.delivery_name', 'delivery_name'),
			array('orders.delivery_receiver_name1', 'delivery_receiver_name1'),
			array('orders.delivery_receiver_name2', 'delivery_receiver_name2'),
			array('orders.delivery_zip', 'delivery_zip'),
			array('orders.delivery_address1', 'delivery_address1'),
			array('orders.delivery_address2', 'delivery_address2'),
			array('orders.delivery_address3', 'delivery_address3'),
			array('orders.delivery_tel', 'delivery_tel'),
			array('orders.delivery_fax', 'delivery_fax'),
			array('orders.order_type_name', 'order_type_name'),
			array('orders.order_datetime', 'order_datetime'),
			array('orders.shipping_date', 'shipping_date'),
			array('orders.delivery_date', 'delivery_date'),
			array('orders.shipping_div', 'shipping_div'),
			array('orders.warehouse_div', 'warehouse_div'),
			array('orders.order_no', 'order_no'),
			array('orders.comment', 'comment'),
			array('order_details.id', 'order_detail_id'),
			array('order_details.category_code', 'category_code'),
			array('order_details.category_name', 'category_name'),
			array('order_details.item_code', 'item_code'),
			array('order_details.item_name', 'item_name'),
			array('order_details.item_size_case', 'item_size_case'),
			array('order_details.item_type', 'item_type'),
			array('order_details.item_unit_name_case', 'item_unit_name_case'),
			array('order_details.price_case_tax', 'price_case'),
			array('order_details.amount_case', 'amount_case'),
			array('order_details.item_size', 'item_size'),
			array('order_details.item_unit_name', 'item_unit_name'),
			array('order_details.price_tax', 'price'),
			array('order_details.amount', 'amount'),
			array('order_details.total', 'total'),
			array('order_details.cost', 'cost'),
			array('order_details.total_cost', 'total_cost')
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
		if ($key == 'order_datetime1') {
			return date('Ymd', strtotime($data['order_datetime']));
		}

		if ($data['order_id'] != $this->tmp_id) {
			$this->tmp_id = $data['order_id'];
			$this->line_num = 0;
		}

		if ($key == 'line_num') {
			$this->line_num++;
			return $this->line_num;
		}

		if ($key == 'sales_person_code1') {
			return $data['sales_person_code'];
		}

		if ($key == 'sales_person_code2') {
			return $data['sales_person_code'];
		}

		if ($key == 'sales_person_code3') {
			return $data['sales_person_code'];
		}

		// 発注側で表示する金額は0%の税率にしたいが、出力時は8%で出力したい対応
		if ($key == 'tax_rate') {
			return '8';
		}

		if ($key == 'delivery_code') {
			if ($data['delivery_kind'] == '1') {
				return '';
			}
		}

		if ($key == 'shipping_attribute') {
			return '1';
		}

		if ($key == 'delivery_date') {
			return date('Ymd', strtotime($data[$key]));
		}

		if ($key == 'item_size') {
			return '';
		}

		if ($key == 'item_size_case') {
			return '';
		}

		if ($key == 'total_amount') {
			$total_amount = $data['item_size'] * $data['amount'] + $data['item_size_case'] * $data['amount_case'];
			return $total_amount;
		}

		if ($key == 'total1') {
			return $data['total'];
		}

		if ($key == 'profit') {
			return $data['total'] - $data['total_cost'];
		}

		if ($key == 'order_datetime2') {
			return date('Ymd', strtotime($data['order_datetime']));
		}

		if ($key == 'total2') {
			return $data['total'];
		}

		if ($key == 'shipping_date') {
			return date('Ymd', strtotime($data[$key]));
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