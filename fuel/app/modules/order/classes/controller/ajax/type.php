<?php
namespace Order;

use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
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
			$order_type = $this->get_order_type($order_type_id);
			if (empty($order_type)) {
				return $this->response_error_fatal();
			}
			return $this->create_response($order_type);
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
	 */
	private function create_response($order_type) {
		$result = array();
		$result['name'] = Arr::get($order_type, 'name');
		$result['code'] = Arr::get($order_type, 'code');
		$result['warehouse_code'] = Arr::get($order_type, 'warehouse_code');

		return $this->response($result);
	}
}