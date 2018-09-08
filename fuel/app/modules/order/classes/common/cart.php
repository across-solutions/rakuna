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

	private $delivery_kind = null;

	private $member_code = null;

	private $member_name = null;

	private $member_zip = null;

	private $member_address1 = null;

	private $member_address2 = null;

	private $member_address3 = null;

	private $member_tel = null;

	private $member_fax = null;

	private $delivery_code = null;

	private $delivery_name = null;

	private $delivery_receiver_name1 = null;

	private $delivery_receiver_name2 = null;

	private $delivery_zip = null;

	private $delivery_address1 = null;

	private $delivery_address2 = null;

	private $delivery_address3 = null;

	private $delivery_tel = null;

	private $delivery_fax = null;

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
			$tax_price = \Common_Util::add_tax($cart['price'] * $cart['size'], $tax_rate, $tax_rounding);
			$tax_price_case = \Common_Util::add_tax($cart['price_case'] * $cart['size_case'], $tax_rate, $tax_rounding);

			$this->payment += $cart['price'] * $cart['size'] * $cart['amount'] + $cart['price_case'] * $cart['size_case'] * $cart['amount_case'];
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
	 * 納品先種類を取得する
	 */
	public function get_delivery_kind() {
		return $this->delivery_kind;
	}

	/**
	 * 納品先コード(自分)を取得する
	 */
	public function get_member_code() {
		return $this->member_code;
	}

	/**
	 * 納品先名(自分)を取得する
	 */
	public function get_member_name() {
		return $this->member_name;
	}

	/**
	 * 納品先郵便番号(自分)を取得する
	 */
	public function get_member_zip() {
		return $this->member_zip;
	}

	/**
	 * 納品先住所1(自分)を取得する
	 */
	public function get_member_address1() {
		return $this->member_address1;
	}

	/**
	 * 納品先住所2(自分)を取得する
	 */
	public function get_member_address2() {
		return $this->member_address2;
	}

	/**
	 * 納品先住所3(自分)を取得する
	 */
	public function get_member_address3() {
		return $this->member_address3;
	}

	/**
	 * 納品先電話番号(自分)を取得する
	 */
	public function get_member_tel() {
		return $this->member_tel;
	}

	/**
	 * 納品先FAX(自分)を取得する
	 */
	public function get_member_fax() {
		return $this->member_fax;
	}

	/**
	 * 納品先コード(納品先)を取得する
	 */
	public function get_delivery_code() {
		return $this->delivery_code;
	}

	/**
	 * 納品先名(納品先)を取得する
	 */
	public function get_delivery_name() {
		return $this->delivery_name;
	}

	/**
	 * 荷受け人名1(納品先)を取得する
	 */
	public function get_delivery_receiver_name1() {
		return $this->delivery_receiver_name1;
	}

	/**
	 * 荷受け人名2(納品先)を取得する
	 */
	public function get_delivery_receiver_name2() {
		return $this->delivery_receiver_name2;
	}

	/**
	 * 納品先郵便番号(納品先)を取得する
	 */
	public function get_delivery_zip() {
		return $this->delivery_zip;
	}

	/**
	 * 納品先住所1(納品先)を取得する
	 */
	public function get_delivery_address1() {
		return $this->delivery_address1;
	}

	/**
	 * 納品先住所2(納品先)を取得する
	 */
	public function get_delivery_address2() {
		return $this->delivery_address2;
	}

	/**
	 * 納品先住所3(納品先)を取得する
	 */
	public function get_delivery_address3() {
		return $this->delivery_address3;
	}

	/**
	 * 納品先電話番号(納品先)を取得する
	 */
	public function get_delivery_tel() {
		return $this->delivery_tel;
	}

	/**
	 * 納品先FAX(納品先)を取得する
	 */
	public function get_delivery_fax() {
		return $this->delivery_fax;
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
	 * 送付先種類を設定する
	 *
	 * @param string $delivery_kind 送付先種類
	 */
	public function set_delivery_kind($delivery_kind) {
		$this->delivery_kind = $delivery_kind;
	}

	/**
	 * 送付先コード(自分)を設定する
	 *
	 * @param string $member_code 送付先コード(自分)
	 */
	public function set_member_code($member_code) {
		$this->member_code = $member_code;
	}

	/**
	 * 送付先名(自分)を設定する
	 *
	 * @param string $member_name 送付先名(自分)
	 */
	public function set_member_name($member_name) {
		$this->member_name = $member_name;
	}

	/**
	 * 送付先郵便番号(自分)を設定する
	 *
	 * @param string $member_zip 送付先郵便番号(自分)
	 */
	public function set_member_zip($member_zip) {
		$this->member_zip = $member_zip;
	}

	/**
	 * 送付先住所1(自分)を設定する
	 *
	 * @param string $member_address1 送付先住所1(自分)
	 */
	public function set_member_address1($member_address1) {
		$this->member_address1 = $member_address1;
	}

	/**
	 * 送付先住所2(自分)を設定する
	 *
	 * @param string $member_address2 送付先住所2(自分)
	 */
	public function set_member_address2($member_address2) {
		$this->member_address2 = $member_address2;
	}

	/**
	 * 送付先住所3(自分)を設定する
	 *
	 * @param string $member_address3 送付先住所3(自分)
	 */
	public function set_member_address3($member_address3) {
		$this->member_address3 = $member_address3;
	}

	/**
	 * 送付先電話番号(自分)を設定する
	 *
	 * @param string $member_tel 送付先電話番号(自分)
	 */
	public function set_member_tel($member_tel) {
		$this->member_tel = $member_tel;
	}

	/**
	 * 送付先FAX(自分)を設定する
	 *
	 * @param string $member_fax 送付先FAX(自分)
	 */
	public function set_member_fax($member_fax) {
		$this->member_fax = $member_fax;
	}

	/**
	 * 送付先コード(納品先)を設定する
	 *
	 * @param string $delivery_code 送付先コード(納品先)
	 */
	public function set_delivery_code($delivery_code) {
		$this->delivery_code = $delivery_code;
	}

	/**
	 * 送付先名(納品先)を設定する
	 *
	 * @param string $delivery_name 送付先名(納品先)
	 */
	public function set_delivery_name($delivery_name) {
		$this->delivery_name = $delivery_name;
	}

	/**
	 * 荷受け人名1(納品先)を設定する
	 *
	 * @param string $delivery_receiver_name1 荷受け人名1(納品先)
	 */
	public function set_delivery_receiver_name1($delivery_receiver_name1) {
		$this->delivery_receiver_name1 = $delivery_receiver_name1;
	}

	/**
	 * 荷受け人名2(納品先)を設定する
	 *
	 * @param string $delivery_receiver_name2 荷受け人名2(納品先)
	 */
	public function set_delivery_receiver_name2($delivery_receiver_name2) {
		$this->delivery_receiver_name2 = $delivery_receiver_name2;
	}

	/**
	 * 送付先郵便番号(納品先)を設定する
	 *
	 * @param string $delivery_zip 送付先郵便番号(納品先)
	 */
	public function set_delivery_zip($delivery_zip) {
		$this->delivery_zip = $delivery_zip;
	}

	/**
	 * 送付先住所1(納品先)を設定する
	 *
	 * @param string $delivery_address1 送付先住所1(納品先)
	 */
	public function set_delivery_address1($delivery_address1) {
		$this->delivery_address1 = $delivery_address1;
	}

	/**
	 * 送付先住所2(納品先)を設定する
	 *
	 * @param string $delivery_address2 送付先住所2(納品先)
	 */
	public function set_delivery_address2($delivery_address2) {
		$this->delivery_address2 = $delivery_address2;
	}

	/**
	 * 送付先住所3(納品先)を設定する
	 *
	 * @param string $delivery_address3 送付先住所3(納品先)
	 */
	public function set_delivery_address3($delivery_address3) {
		$this->delivery_address3 = $delivery_address3;
	}

	/**
	 * 送付先電話番号(納品先)を設定する
	 *
	 * @param string $delivery_tel 送付先電話番号(納品先)
	 */
	public function set_delivery_tel($delivery_tel) {
		$this->delivery_tel = $delivery_tel;
	}

	/**
	 * 送付先FAX(納品先)を設定する
	 *
	 * @param string $delivery_fax 送付先FAX(納品先)
	 */
	public function set_delivery_fax($delivery_fax) {
		$this->delivery_fax = $delivery_fax;
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