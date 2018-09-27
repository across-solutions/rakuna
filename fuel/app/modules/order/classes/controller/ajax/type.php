<?php
namespace Order;

use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\Input;
/**
 * 発注タイプ非同期コントローラクラス
 */
class Controller_Ajax_Type extends Controller_Ajax_Base {

	/**
	 * @see \Order\Controller_Ajax_Base::before()
	 */
	public function before() {
		parent::before();

		if (!Auth::check()) {
			return $this->response_error_auth();
		}
	}

	/**
	 * 発注タイプ取得処理
	 *
	 * @param string $order_type_id 発注タイプID
	 */
	public function get_data($order_type_id) {
		try {
			$delivery_kind = Input::get('delivery_kind');
			$member_code = Input::get('member_code');
			$delivery_code = Input::get('delivery_code');

			$order_type = $this->get_order_type($order_type_id);
			if (empty($order_type)) {
				return $this->response_error_fatal();
			}
			return $this->create_response($order_type, $delivery_kind, $member_code, $delivery_code);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 発注タイプデータを取得する
	 *
	 * @param int $order_type_id 発注タイプID
	 */
	private function get_order_type($order_type_id) {
		$query = DB::select('order_types.id', 'order_types.code', 'order_types.warehouse_code')
				->from('order_types')
				->where('order_types.id', '=', $order_type_id)
				->where('order_types.del_flg', '=', UNDELETED);

		return $query->execute()->current();
	}

	/**
	 * レスポンスを生成する
	 *
	 * @param $order_type 発注タイプ
	 * @param $delivery_kind 納品先種類
	 * @param $member_code 発注者コード
	 * @param $delivery_code 納品先コード
	 */
	private function create_response($order_type, $delivery_kind, $member_code, $delivery_code) {
		$result = array();
		$result['code'] = Arr::get($order_type, 'code');
		$result['warehouse_code'] = Arr::get($order_type, 'warehouse_code');

		$result['shipping_date'] = "";
		$result['delivery_date'] = "";

		if ($delivery_kind == 1) {
			$model = 'members';
		} else {
			$model = 'deliveries';
		}

		if ($result['code'] == '80') {
			$nearest_shipping_date = \Common_Util::get_nearest_shipping_date($model, $member_code, $delivery_code);
			$result['shipping_date'] = $nearest_shipping_date;
			if (!is_null($nearest_shipping_date)) {
				$result['delivery_date'] = $nearest_shipping_date;
			}
		} else if ($result['code'] == '90') {
			$nearest_shipping_date = \Common_Util::get_nearest_shipping_date($model, $member_code, $delivery_code);
			$result['shipping_date'] = $nearest_shipping_date;
			if (!is_null($nearest_shipping_date)) {
				$result['delivery_date'] = date('Ymd', strtotime($nearest_shipping_date . ' +1 day'));
			}
		}

		return $this->response($result);
	}
}