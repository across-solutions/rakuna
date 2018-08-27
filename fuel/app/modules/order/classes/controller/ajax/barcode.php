<?php
namespace Order;
use Auth\Auth;
/**
 * バーコード非同期コントローラクラス
 */
class Controller_Ajax_Barcode extends Controller_Ajax_Base {

	/**
	 * 商品情報を取得する
	 *
	 * @param string $barcode バーコード
	 */
	public function get_item($barcode = '') {
		if (!Auth::check()) {
			return $this->response_error_auth();
		}
		
		$item = \Model_Item::query()
			->where('jan_code', '=', $barcode)
			->get_one();
		if (empty($item)) {
			return $this->response(array('id' => 'not_found'));
		}

		try {
			$cart = Common_Cart_Util::plus($this->get_member_id(), $item->id);
			if ($cart === false) {
				return $this->response_error_fatal();
			}
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
		
		return $this->response($this->create_response($item, $cart));
	}

	/**
	 * 商品情報を取得する
	 *
	 * @param string $barcode バーコード
	 */
	public function get_item_case($barcode = '') {
		if (!Auth::check()) {
			return $this->response_error_auth();
		}
	
		$item = \Model_Item::query()
		->where('jan_code', '=', $barcode)
		->get_one();
		if (empty($item)) {
			return $this->response(array('id' => 'not_found'));
		}
	
		try {
			$cart = Common_Cart_Util::plus_case($this->get_member_id(), $item->id);
			if ($cart === false) {
				return $this->response_error_fatal();
			}
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	
		return $this->response($this->create_response($item, $cart));
	}
	
	/**
	 * レスポンスデータを生成する
	 *
	 * @param Model_Item $item 商品
	 */
	private function create_response($item, $cart) {
		$response = array();
		$response['id'] = $item->id;
		$response['code'] = $item->code;
		$response['name'] = $item->name;
		$response['amount'] = $cart->amount;
		$response['amount_case'] = $cart->amount_case;
		$response['img'] = \Image_Item::url($item->code);
		$response['exist'] = Common_Cart::instance($this->get_member_id())->count_item() > 0;
		
		return $response;
	}
}