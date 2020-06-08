<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\Response;
use Auth\Auth;
use Fuel\Core\DB;
use Fuel\Core\Session;
/**
 * カート非同期コントローラクラス
 */
class Controller_Ajax_Cart extends Controller_Ajax_Base {

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
	 * 更新処理
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_update($item_id) {
		$amount = Input::get('amount');
		if (is_null($amount) || !is_numeric($amount)) {
			return $this->response_error_fatal();
		}
		if (!Common_Cart_Util::enable_order_item_pack($this->get_member_id(), $item_id, false)) {
			return $this->response_error_alert('ケースとバラは混在できません');
		}
		if (!Common_Cart_Util::enable_order_item_type($this->get_member_id(), $item_id)) {
			return $this->response_error_alert('在庫品と在庫品以外は混在できません');
		}

		try {
			$cart = Common_Cart_Util::update($this->get_member_id(), $item_id, $amount);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 更新処理(ケース)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_update_case($item_id) {
		$amount = Input::get('amount');
		if (is_null($amount) || !is_numeric($amount)) {
			return $this->response_error_fatal();
		}
		if (!Common_Cart_Util::enable_order_item_pack($this->get_member_id(), $item_id, true)) {
			return $this->response_error_alert('ケースとバラは混在できません');
		}
		if (!Common_Cart_Util::enable_order_item_type($this->get_member_id(), $item_id)) {
			return $this->response_error_alert('在庫品と在庫品以外は混在できません');
		}

		try {
			$cart = Common_Cart_Util::update_case($this->get_member_id(), $item_id, $amount);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount_case'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 加算処理(バラ+1)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_plus($item_id) {
		if (!Common_Cart_Util::enable_order_item_pack($this->get_member_id(), $item_id, false)) {
			return $this->response_error_alert('ケースとバラは混在できません');
		}
		if (!Common_Cart_Util::enable_order_item_type($this->get_member_id(), $item_id)) {
			return $this->response_error_alert('在庫品と在庫品以外は混在できません');
		}
		try {
			$cart = Common_Cart_Util::plus($this->get_member_id(), $item_id);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 加算処理(ケース+1)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_plus_case($item_id) {
		if (!Common_Cart_Util::enable_order_item_pack($this->get_member_id(), $item_id, true)) {
			return $this->response_error_alert('ケースとバラは混在できません');
		}
		if (!Common_Cart_Util::enable_order_item_type($this->get_member_id(), $item_id)) {
			return $this->response_error_alert('在庫品と在庫品以外は混在できません');
		}
		try {
			$cart = Common_Cart_Util::plus_case($this->get_member_id(), $item_id);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount_case'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 減算処理(バラ-1)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_minus($item_id) {
		try {
			$cart = Common_Cart_Util::minus($this->get_member_id(), $item_id);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 減算処理(ケース-1)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_minus_case($item_id) {
		try {
			$cart = Common_Cart_Util::minus_case($this->get_member_id(), $item_id);
			if (empty($cart)) {
				return $this->create_response(0, $item_id);
			}
			return $this->create_response($cart['amount_case'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 削除処理(バラ)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_del($item_id) {
		try {
			$cart = Common_Cart_Util::delete($this->get_member_id(), $item_id);
			if (is_null($cart)) {
				return $this->create_response(0, $item_id);
			}
			if ($cart === false) {
				return $this->response_error_fatal();
			}
			return $this->create_response($cart['amount'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 削除処理(ケース)
	 *
	 * @param string $item_id 商品ID
	 */
	public function get_del_case($item_id) {
		try {
			$cart = Common_Cart_Util::delete_case($this->get_member_id(), $item_id);
			if (is_null($cart)) {
				return $this->create_response(0, $item_id);
			}
			if ($cart === false) {
				return $this->response_error_fatal();
			}
			return $this->create_response($cart['amount_case'], $item_id);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * レスポンスを生成する
	 *
	 * @param $amount 数量
	 */
	private function create_response($amount, $item_id) {
		$cart = Common_Cart::instance($this->get_member_id());
		Session::set(SESSION_KEY_CART, $cart);

		$result = array();
		$result['amount'] = $amount;
		$result['exist'] = $cart->count_item() > 0;
		$result['payment_tax'] = $cart->get_payment_tax();
		$result['payment'] = $cart->get_payment();
		$result['tax'] = $cart->get_tax();
		$result['count_item'] = $cart->count_item();
		$result['total_amount'] = $cart->get_total_amount();
		$result['total_amount_case'] = $cart->get_total_amount_case();

		$order_type = Common_Cart_Util::get_item_order_type($this->get_member_id(), $item_id);
		$result['order_type'] = $order_type;
		$result['order_type_payment_tax'] = $cart->get_order_type_payment_tax($order_type);
		$result['order_type_payment'] = $cart->get_order_type_payment($order_type);
		$result['order_type_tax'] = $cart->get_order_type_tax($order_type);
		$result['order_type_amount'] = $cart->get_order_type_amount($order_type);
		$result['order_type_amount_case'] = $cart->get_order_type_amount_case($order_type);

		return $this->response($result);
	}
}