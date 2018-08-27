<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;

/**
 * 非営業日管理編集プレゼンタクラス
 */
class Presenter_Setting_Holiday_Edit extends \Presenter_Base {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		parent::view();

		$start_year = $this->get_start_year();
		$end_year = date('Y') + 1;
		$this->years = \Common_Util::range_year2(intval($start_year), $end_year, '----', '年');
		$this->months = \Common_Util::range_month('--', '月');
		$this->days = \Common_Util::range_day('--', '日');
	}

	/**
	 * 開始年を取得する
	 */
	private function get_start_year() {
		$oldest_holiday = $this->get_oldest_holiday();
		if (empty($oldest_holiday)) {
			return date('Y');
		}
		return date('Y', strtotime($oldest_holiday->date));
	}

	/**
	 * 最古の非営業日データを取得する
	 */
	private function get_oldest_holiday() {
		return \Model_Holiday::find('last', array(
			'order_by' => array(
				'date' => 'asc'
			)
		));
	}
}