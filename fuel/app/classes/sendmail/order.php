<?php
/**
 * 発注受付メール送信クラス
 */
class Sendmail_Order extends Sendmail_Base {

	/**
	 * システム設定
	 */
	private $setting = null;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		$this->setting = $this->get_setting();
	}

	/**
	 * 発注受付メールを送信する
	 *
	 * @param int $order_id 受注ID
	 */
	public function send($order_id) {
		$order = Model_Order::find($order_id);
		if (empty($order)) {
			return false;
		}

		$member = Model_Member::find($order->member_id);
		if (empty($member)) {
			return false;
		}
		$sub_email = explode(',', $member->sub_email);
		$sub_addresses = array();
		foreach ($sub_email as $email) {
			if (!empty($email)) {
				$sub_addresses[] = $email;
			}
		}

		$replaces = array();
		$replaces['{$manage-company}'] = $this->setting->corporation_name;
		$replaces['{$manage-name}'] = $this->setting->administrator_name;
		$replaces['{$order-name}'] = $order->member_name;
		$replaces['{$order-date}'] = Common_Util::format_datetime($order->order_datetime, 'Y年m月d日 H時i分');
		$replaces['{$order-delivery-list}'] = $this->create_order_delivery_list($order);
		$replaces['{$order-type}'] = $order->order_type_name;
		$replaces['{$order-shipping-date}'] = Common_Util::format_date_with_week($order->shipping_date, 'Y年m月d日', '');
		$replaces['{$order-delivery-date}'] = Common_Util::format_date_with_week($order->delivery_date, 'Y年m月d日', '');
		$replaces['{$order-shipping_div}'] = $order->shipping_div;
		$replaces['{$order-warehouse_div}'] = $order->warehouse_div;
		$replaces['{$order-no}'] = $order->order_no;
		$replaces['{$order-comment}'] = $order->comment;
		$replaces['{$order-list}'] = $this->create_order_list($order);
		$replaces['{$order-amount-case}'] = Common_Util::format_number($order->amount_case);
		$replaces['{$order-amount}'] = Common_Util::format_number($order->amount);
		$replaces['{$order-payment}'] = Common_Util::format_number($order->payment_tax);
		$replaces['{$order-tax}'] = Common_Util::format_number($order->tax);

		return $this->sendmail($order->member_email, $sub_addresses, $replaces);
	}

	/**
	 * 納品先用テキストを取得する
	 *
	 * @param Model_Order $order 受注情報
	 */
	private function create_order_delivery_list($order) {
		$text = '';
		$text .= '納品先コード : ' . $order->delivery_code . "\n";
		$text .= '納品先名 : ' . $order->delivery_name . "\n";
		if (!empty($order->delivery_receiver_name1)) {
			$text .= '荷受け人名1 : ' . $order->delivery_receiver_name1 . "\n";
		}
		if (!empty($order->delivery_receiver_name2)) {
			$text .= '荷受け人名2 : ' . $order->delivery_receiver_name2 . "\n";
		}
		$text .= '郵便番号 : ' . $order->delivery_zip . "\n";
		$text .= '住所1 : ' . $order->delivery_address1 . "\n";
		$text .= '住所2 : ' . $order->delivery_address2 . "\n";
		$text .= '住所3 : ' . $order->delivery_address3 . "\n";
		$text .= '電話番号 : ' . $order->delivery_tel . "\n";
		$text .= 'FAX : ' . $order->delivery_fax . "\n";
		$text .= "\n";
		return $text;
	}

	/**
	 * 明細用テキストを取得する
	 *
	 * @param Model_Order $order 受注情報
	 */
	private function create_order_list($order) {
		$text = '';
		foreach ($order->order_details as $detail) {
			$text .= '商品コード : ' . $detail->item_code . "\n";
			$text .= '商品名 : ' . $detail->item_name . "\n";
			if (Common_Setting::is_price()) {
				if (Common_Setting::is_case() && $detail->amount_case > 0) {
					$text .= $detail->item_unit_name_case . ' : ' . Common_Util::format_number(\Common_Util::add_tax($detail->price_case * $detail->item_size_case, $order->tax_rate, 1)) . '円 × '
						. Common_Util::format_number($detail->amount_case) . ' = '
						. Common_Util::format_number(\Common_Util::add_tax($detail->price_case * $detail->item_size_case * $detail->amount_case, $order->tax_rate, 1)) . "円\n";
				}
				if ($detail->amount > 0) {
					$text .= Common_Setting::is_case() ? $detail->item_unit_name . ' : ' : '金額　： ';
					$text .= Common_Util::format_number(\Common_Util::add_tax($detail->price * $detail->item_size, $order->tax_rate, 1)) . '円 × '
						. Common_Util::format_number($detail->amount) . ' = '
						. Common_Util::format_number(\Common_Util::add_tax($detail->price * $detail->item_size * $detail->amount, $order->tax_rate, 1)) . "円\n";
				}
			} else {
				if (Common_Setting::is_case() && $detail->amount_case > 0) {
					$text .= $detail->item_unit_name_case . '  : ' . Common_Util::format_number($detail->amount_case) . "\n";
				}
				if ($detail->amount > 0) {
					$text .= Common_Setting::is_case() ? $detail->item_unit_name . ' : ' : '数量　： ';
					$text .= Common_Util::format_number($detail->amount) . "\n";
				}
			}
			$text .= "\n";
		}
		return $text;
	}

	/**
	 * @see Sendmail_Base::get_template_mail_div()
	 */
	protected function get_template_mail_div() {
		return Config::get('define.template_mail_div.order');
	}

	/**
	 * システム設定を取得する
	 */
	private function get_setting() {
		return Model_Setting::query()->get_one();
	}
}