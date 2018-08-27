<?php
namespace Manage;

use Fuel\Core\Arr;
/**
 * 受注編集プレゼンタクラス
 */
class Presenter_Order_Edit extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->delivery_date = function($data, $key) {
			return $this->delivery_date($data, $key);
		};

		$this->comment = function($data, $key) {
			return $this->comment($data, $key);
		};
	}

	/**
	 * 納品希望日を取得する
	 *
	 * @param array $data データ配列
	 * @param string $key キー
	 */
	private function delivery_date($data, $key) {
		$delivery_date = Arr::get($data, $key);
		if (empty($delivery_date)) {
			return '納品希望日指定なし';
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