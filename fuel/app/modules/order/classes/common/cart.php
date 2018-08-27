<?php
namespace Order;

use Fuel\Core\Config;
/**
 * カート情報保持クラス
 */
class Common_Cart {

	private $member_id = null;

	private $payment = 0;

	private $payment_tax = 0;

	private $amount = 0;

	private $amount_case = 0;

	private $delivery_date_check = 0;

	private $delivery_date = null;

	private $comment = null;

	private $carts = array();

	private $setting = array();

	/**
	 * コンストラクタ
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function __construct($member_id) {
		$this->carts = Common_Cart_Util::gets($member_id);
		$this->setting = \Model_Setting::find('first');

		$tax_rate = $this->get_tax_rate();
		$tax_rounding = $this->get_tax_rounding();

		foreach ($this->carts as $cart) {
			$tax_price = \Common_Util::add_tax($cart['price'], $tax_rate, $tax_rounding);
			$tax_price_case = \Common_Util::add_tax($cart['price_case'], $tax_rate, $tax_rounding);

			$this->payment += $cart['price'] * $cart['amount'] + $cart['price_case'] * $cart['amount_case'];
			$this->payment_tax += $tax_price * $cart['amount'] + $tax_price_case * $cart['amount_case'];
			$this->amount += $cart['amount'];
			$this->amount_case += $cart['amount_case'];
		}
		$this->member_id = $member_id;
	}

	/**
	 * インスタンスを生成する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	public static function instance($member_id) {
		return new Common_Cart($member_id);
	}

	/**
	 * カート内情報更新チェック
	 */
	public function check() {
		$carts = Common_Cart_Util::gets($this->member_id);

		if ($this->count_item() == 0 || $this->count_item() != count($carts)) {
			throw new \Exception_Cartupdated();
		}

		foreach ($this->carts as $cart) {
			foreach ($carts as $db_cart) {
				if ($cart['code'] == $db_cart['code']) {
					if ($cart['updated'] < $db_cart['updated']) {
						return false;
					}
					break;
				}
			}
		}
		return true;
	}

	/**
	 * カート内情報を取得する
	 */
	public function get_carts() {
		return $this->carts;
	}

	/**
	 * 商品数を取得する
	 */
	public function count_item() {
		return count($this->carts);
	}

	/**
	 * 合計数(バラ)を取得する
	 */
	public function get_total_amount() {
		return $this->amount;
	}

	/**
	 * 合計数(ケース)を取得する
	 */
	public function get_total_amount_case() {
		return $this->amount_case;
	}

	/**
	 * 合計金額(税抜)を取得する
	 */
	public function get_payment() {
		return $this->payment;
	}

	/**
	 * 合計金額(税込)を取得する
	 */
	public function get_payment_tax() {
		return $this->payment_tax;
	}

	/**
	 * 消費税額を取得する
	 */
	public function get_tax() {
		return $this->get_payment_tax() - $this->get_payment();
	}

	/**
	 * 消費税率を取得する
	 */
	public function get_tax_rate() {
		return $this->setting->tax_rate;
	}

	/**
	 * 端数処理方法を取得する
	 */
	public function get_tax_rounding() {
		return $this->setting->tax_rounding;
	}

	/**
	 * 納品希望日チェックを取得する
	 */
	public function get_delivery_date_check() {
		return $this->delivery_date_check;
	}

	/**
	 * 納品希望日を取得する
	 */
	public function get_delivery_date() {
		return $this->delivery_date;
	}

	/**
	 * 備考を取得する
	 */
	public function get_comment() {
		return $this->comment;
	}

	/**
	 * 納品希望日を設定する
	 *
	 * @param string $delivery_date 納品希望日
	 */
	public function set_delivery_date($delivery_date) {
		$this->delivery_date = $delivery_date;
	}

	/**
	 * 備考を設定する
	 *
	 * @param string $comment 備考
	 */
	public function set_comment($comment) {
		$this->comment = $comment;
	}
}