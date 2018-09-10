<?php
namespace Manage;

use Fuel\Core\Arr;
/**
 * 受注詳細プレゼンタクラス
 */
class Presenter_Order_View extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

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