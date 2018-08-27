<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;

/**
 * 非営業日一覧プレゼンタクラス
 */
class Presenter_Setting_Holiday_Index extends \Presenter_Base {

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();

		$this->year = Input::get('year', date('Y'));
		$this->calendar = $this->get_calendar($this->year);
	}

	function get_calendar($year){
		$holidays = $this->get_holidays($this->year);
		$year_month_dates = $this->get_year_dates($this->year);

		$calendar = array();
		for($i = 1; $i <= 12; $i++){
			$dates = $year_month_dates[$i - 1];

			$month = array();
			$week_dates = array();
			foreach($dates as $date){
				$week_dates []= array(
					'date'			=> $date->format('Y-m-d'),
					'day'			=> $date->format('j'),
					'current_month' => $date->format('m') == $i,
					'is_holiday'	=> array_key_exists($date->format('Y-m-d'), $holidays)
				);

				if(count($week_dates) == 7){ //一週間分
					$month []= $week_dates;
					$week_dates = array();
				}
			}

			$calendar []= $month;
		}
		return $calendar;
	}

	/**
	 *  非営業日リストを取得
	 */
	function get_holidays($year){
		$start_of_year = new \DateTime($year.'-01-01');
		$end_of_year = clone $start_of_year;
		$end_of_year->add(new \DateInterval('P1Y'));
		$end_of_year->sub(new \DateInterval('P1D'));

		$holidays = \Model_Holiday::query()
			->where('date', '>=', $start_of_year->format('Y-m-d'))
			->where('date', '<=', $end_of_year->format('Y-m-d'))
			->get();

		$ret = array();
		foreach($holidays as $holiday){
			$ret[$holiday->date] = true;
		}
		return $ret;
	}

	/**
	 *  指定した年のカレンダー用の日付配列を取得
	 */
	function get_year_dates($year){
		$months = array();

		for($i = 1; $i <= 12 ;$i++){
			$months []= $this->get_month_dates($year, $i);
		}

		return $months;
	}

	/**
	 *  指定した月のカレンダー用の日付配列を取得
	 */
	function get_month_dates($year, $month){

		$first_of_month_date = new \DateTime($year.'-'.$month.'-01');

		if($this->is_sunday($first_of_month_date)){
			$calendar_start = clone $first_of_month_date;
		}else{
			$calendar_start = $this->get_previous_sunday($first_of_month_date);
		}

		$end_of_month_date = $this->get_end_of_month_date($first_of_month_date);
		$calendar_end = $this->get_next_sunday($end_of_month_date);

		$period = new \DatePeriod($calendar_start, new \DateInterval('P1D'), $calendar_end);

		$dates = array();
		foreach($period as $date){
			$dates [] = $date;
		}

		return $dates;
	}

	/**
	 *  月末の日付を取得
	 */
	function get_end_of_month_date($firt_of_date){
		$d = clone $firt_of_date;
		$d->add(new \DateInterval('P1M'));
		$d->sub(new \DateInterval('P1D'));
		return $d;
	}

	/**
	 *  前の日曜日の日付を取得
	 */
	function get_previous_sunday($date){
		$d = clone $date;
		$d->sub(new \DateInterval('P1D'));
		while(!$this->is_sunday($d)){
			$d->sub(new \DateInterval('P1D'));
		}
		return $d;
	}

	/**
	 *  次の日曜日の日付を取得
	 */
	function get_next_sunday($date){
		$d = clone $date;
		$d->add(new \DateInterval('P1D'));
		while(!$this->is_sunday($d)){
			$d->add(new \DateInterval('P1D'));
		}
		return $d;
	}

	function is_sunday($date){
		return $date->format('w') == 0;
	}
}
