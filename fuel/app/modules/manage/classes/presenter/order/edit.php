<?php
namespace Manage;

use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
/**
 * 受注編集プレゼンタクラス
 */
class Presenter_Order_Edit extends \Presenter_Base {

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

		$this->shipping_date = function($data, $key) {
			return $this->shipping_date($data, $key);
		};

		$this->delivery_date = function($data, $key) {
			return $this->delivery_date($data, $key);
		};

		$this->comment = function($data, $key) {
			return $this->comment($data, $key);
		};
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
}