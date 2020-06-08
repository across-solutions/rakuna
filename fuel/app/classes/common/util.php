<?php
use Fuel\Core\Asset;
use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * 共通処理クラス
 */
class Common_Util {

	/**
	 * 指定されたフィールドのみの配列を返す
	 *
	 * @param $array 配列
	 * @param $fields フィールドリスト
	 */
	public static function filter($array, $fields) {
		if (empty($fields) || !is_array($fields)) {
			return array();
		}

		$values = array();
		foreach ($fields as $field) {
			$values[$field] = isset($array[$field]) ? $array[$field] : null;
		}
		return $values;
	}

	/**
	 * 指定されたフィールドのみコピーする
	 *
	 * @param $to コピー先配列
	 * @param $from コピー元配列
	 * @param $fields フィールドリスト
	 */
	public static function copy(&$to, $from, $fields) {
		if (empty($fields) || !is_array($fields)) {
			return array();
		}

		foreach ($fields as $field) {
			$to[$field] = isset($from[$field]) ? $from[$field] : null;
		}
	}

	/**
	 * 配列内容を比較する
	 *
	 * @param array $array1 配列1
	 * @param array $array2 配列2
	 * @param array $fields 比較するフィールド
	 */
	public static function diff($array1, $array2, $fields) {
		if (empty($fields) || !is_array($fields)) {
			return false;
		}

		foreach ($fields as $field) {
			if (!isset($array1[$field]) && !isset($array2[$field])) {
				continue;
			}
			if (is_null($array1[$field]) && is_null($array2[$field])) {
				continue;
			}
			if (isset($array1[$field]) ^ isset($array2[$field])) {
				return true;
			}
			if ($array1[$field] != $array2[$field]) {
				return true;
			}
		}
		return false;
	}

	/**
	 * クエリ文字列を取得する
	 */
	public static function get_query_string() {
		$params = Input::get();
		if (empty($params)) {
			return '';
		}

		$list = array();
		foreach (Input::get() as $key => $value) {
			$list[] = $key . '=' . $value;
		}
		return '?' . implode('&', $list);
	}

	/**
	 * ランダムな半角英数字を生成する
	 * @param $num 文字数
	 */
	public static function random_string($num) {
		$str = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));

		$result = '';
		for ($i = 0; $i < $num; $i++) {
			$result .= $str[rand(0, count($str) - 1)];
		}
		return $result;
	}

	/**
	 * 日付フォーマット
	 *
	 * @param string $value 値
	 * @param string $format フォーマット
	 * @param string $default デフォルト値
	 */
	public static function format_date($value, $format = 'Y-m-d', $default = '') {
		return self::format_datetime($value, $format, $default);
	}

	/**
	 * 日付フォーマット（曜日有り）
	 *
	 * @param string $value 値
	 * @param string $format フォーマット
	 * @param string $default デフォルト値
	 */
	public static function format_date_with_week($value, $format = 'Y-m-d', $default = '') {
		if (empty($value)) {
			return $default;
		}

		return self::add_week_on_date($value, $format);
	}

	/**
	 * 日時フォーマット
	 *
	 * @param string $value 値
	 * @param string $format フォーマット
	 * @param string $default デフォルト値
	 */
	public static function format_datetime($value, $format = 'Y-m-d H:i:s', $default = '') {
		if (empty($value)) {
			return $default;
		}
		return date($format, strtotime($value));
	}

	/**
	 * 曜日フォーマット
	 *
	 * @param string $value 値
	 * @param string $default デフォルト値
	 */
	public static function format_week($value, $default = '') {
		if (empty($value)) {
			return $default;
		}

		$weeks = array('日', '月', '火', '水', '木', '金', '土');
		$w = date('w', strtotime($value));

		return '(' . $weeks[$w] . ')';
	}

	/**
	 * 曜日フォーマット(カッコなし)
	 *
	 * @param string $value 値
	 * @param string $default デフォルト値
	 */
	public static function format_week_only($value, $default = '') {
		if (empty($value)) {
			return $default;
		}

		$weeks = array('日', '月', '火', '水', '木', '金', '土');
		$w = date('w', strtotime($value));

		return $weeks[$w];
	}

	/**
	 * 金額フォーマット
	 *
	 * @param string $value 値
	 * @param $point 小数点桁数
	 */
	public static function format_price($value, $point = 0) {
		if (is_null($value) || !is_numeric($value)) {
			return '';
		}
		$value = number_format($value, $point);
		return (preg_match('/\./', $value)) ? preg_replace('/\.?0+$/', '', $value) : $value;
	}

	/**
	 * 数値フォーマット
	 *
	 * @param string $value 値
	 */
	public static function format_number($value) {
		if (is_null($value) || !is_numeric($value)) {
			return '';
		}
		return number_format($value);
	}

	/**
	 * 年リストを取得する
	 *
	 * @param int $before 前年数
	 * @param int $after 後年数
	 * @param int $start_year 開始年
	 */
	public static function range_year($before, $after, $start_year = null) {
		if (!is_int($before) || !is_int($after)) {
			return array();
		}
		if (!is_null($start_year) && !is_int($start_year)) {
			return array();
		}

		if (is_null($start_year)) {
			$start_year = date('Y');
		}

		$list = array();
		$list[''] = '';
		for ($i = -1 * $before; $i <= $after; $i++) {
			$year = $start_year + $i;
			$list[$year] = $year . '年';
		}
		return $list;
	}

	/**
	 * 年リストを取得する
	 *
	 * @param int $start_year 開始年
	 * @param int $end_year 終了年
	 * @param string $empty 空行タイトル
	 * @param string $suffix 接尾辞
	 */
	public static function range_year2($start_year, $end_year, $empty = false, $suffix = '') {
		if (!is_int($start_year) || !is_int($end_year)) {
			return array();
		}

		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = $start_year; $i <= $end_year; $i++) {
			$list[$i] = $i . $suffix;
		}
		return $list;
	}

	/**
	 * 月リストを取得する
	 *
	 * @param string $empty 空行タイトル
	 * @param string $suffix 接尾辞
	 */
	public static function range_month($empty = false, $suffix = '') {
		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = 1; $i <= 12; $i++) {
			$num = str_pad($i, 2, '0', STR_PAD_LEFT);
			$list[$num] = $num . $suffix;
		}
		return $list;
	}

	/**
	 * 日リストを取得する
	 *
	 * @param string $empty 空行タイトル
	 * @param string $suffix 接尾辞
	 */
	public static function range_day($empty = false, $suffix = '') {
		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = 1; $i <= 31; $i++) {
			$num = str_pad($i, 2, '0', STR_PAD_LEFT);
			$list[$num] = $num . $suffix;
		}
		return $list;
	}

	/**
	 * 時リストを取得する
	 *
	 * @param string $empty 空行タイトル
	 * @param string $suffix 接尾辞
	 */
	public static function range_hour($empty = false, $suffix = '') {
		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = 0; $i <= 23; $i++) {
			$num = str_pad($i, 2, '0', STR_PAD_LEFT);
			$list[$num] = $num . $suffix;
		}
		return $list;
	}

	/**
	 * 分リストを取得する
	 *
	 * @param string $empty 空行タイトル
	 * @param string $suffix 接尾辞
	 */
	public static function range_minute($empty = false, $suffix = '') {
		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = 0; $i <= 59; $i++) {
			$num = str_pad($i, 2, '0', STR_PAD_LEFT);
			$list[$num] = $num . $suffix;
		}
		return $list;
	}

	/**
	 * 年月日リストを取得する
	 *
	 * @param int $start 開始日
	 * @param int $num 日数
	 * @param string $empty 空行タイトル
	 * @param string $format フォーマット
	 */
	public static function range_date($start, $num, $empty = false, $format = 'Y年m月d日') {
		$list = array();
		if ($empty !== false) {
			$list[''] = $empty;
		}
		for ($i = 0; $i < $num; $i++) {
			$date = date('Y-m-d', strtotime($i . ' day', strtotime($start)));

			$list[date('Ymd', strtotime($date))] = self::add_week_on_date($date);
		}

		return $list;
	}

	/**
	 * 日付に表示用の曜日を追加する
	 *
	 * @param string $date 日付
	 * @param string $format 指定日付フォーマット
	 */
	public static function add_week_on_date($date, $format = 'Y年m月d日'){

		$week_number = date('w', strtotime($date));
		$week = '(－)';

		switch ($week_number) {
			case '0':
				$week = '(日)';
				break;
			case '1':
				$week = '(月)';
				break;
			case '2':
				$week = '(火)';
				break;
			case '3':
				$week = '(水)';
				break;
			case '4':
				$week = '(木)';
				break;
			case '5':
				$week = '(金)';
				break;
			case '6':
				$week = '(土)';
				break;
			default :
				$week = '(－)';
				break;
		}

		return date($format.$week, strtotime($date));

	}

	/**
	 * 曜日を返す
	 *
	 * @param string $date 日付
	 */
	public static function add_week($date){
		if (empty($date)) {
			return false;
		}

		$week_number = date('w', strtotime($date));
		$week = '(－)';

		switch ($week_number) {
			case '0':
				$week = '(日)';
				break;
			case '1':
				$week = '(月)';
				break;
			case '2':
				$week = '(火)';
				break;
			case '3':
				$week = '(水)';
				break;
			case '4':
				$week = '(木)';
				break;
			case '5':
				$week = '(金)';
				break;
			case '6':
				$week = '(土)';
				break;
			default :
				$week = '(－)';
				break;
		}

		return date($week, strtotime($date));
	}

	/**
	 * 税込金額を取得する
	 *
	 * @param int $value 税抜金額
	 * @param int $tax_rate 消費税率
	 * @param int $tax_rounding 端数処理方法
	 */
	public static function add_tax($value, $tax_rate = null, $tax_rounding = null) {
		$tax_rate = is_null($tax_rate) ? Common_Setting::get('tax_rate') : $tax_rate;
		$tax_rounding = is_null($tax_rounding) ? Common_Setting::get('tax_rounding') : $tax_rounding;
		return self::rounding(($value * (100 + $tax_rate)) / 100, $tax_rounding);
	}

	/**
	 * 消費税端数処理
	 *
	 * @param int $value 金額
	 * @param int $tax_rounding 端数処理方法
	 */
	public static function rounding($value, $tax_rounding) {
		switch ($tax_rounding) {
			case Config::get('define.tax_rounding.floor'):
				return floor($value);
			case Config::get('define.tax_rounding.ceil'):
				return ceil($value);
			case Config::get('define.tax_rounding.round'):
				return round($value);
			default:
				return floor($value);
		}
	}

	/**
	 * 全角半角スペースで分割する
	 *
	 * @param string $text 文字列
	 */
	public static function split_space($text) {
		return preg_split('/[\s|\x{3000}]+/u', $text);
	}

	/**
	 * 日付の年月日をハイフンで繋げる
	 *
	 * @param array $data 検索条件
	 * @param string $key_year 年キー
	 * @param string $key_month 月キー
	 * @param string $key_day 日キー
	 */
	public static function get_date($data, $key_year, $key_month, $key_day) {
		$year = Arr::get($data, $key_year);
		$month = Arr::get($data, $key_month);
		$day = Arr::get($data, $key_day);
		if (empty($year) && empty($month) && empty($day)) {
			return '';
		}

		return $year . '-' . $month . '-' . $day;
	}

	/**
	 * 検索用に文字列を整形する
	 *
	 * @param string $value 文字列
	 */
	public static function mb_convert($value) {
		$value = mb_convert_kana($value, 'KCVa');
		return mb_strtoupper($value);
	}

	/**
	 * 検索用に文字列を整形する
	 *
	 * @param array $values データ配列
	 * @param array $fields 対象フィールド名配列
	 */
	public static function mb_converts($values, $fields) {
		$results = array();
		foreach ($fields as $field) {
			if (isset($values[$field])) {
				$results[] = self::mb_convert($values[$field]);
			}
		}

		return implode(' ', $results);
	}

	/**
	 * 直近の指定曜日の日付を取得する
	 *
	 * @param string $date 日付
	 * @param int $week 曜日
	 */
	public static function previous_date($date, $week) {
		for ($i = 0; $i < 7; $i++) {
			if (date('w', strtotime($date)) == $week) {
				return $date;
			}
			$date = date('Y-m-d', strtotime($date . ' -1 day'));
		}
		return $date;
	}

	/**
	 * 直近の配達曜日の日付を取得する(非営業日を除く)
	 *
	 * @param string $table テーブル名
	 * @param string $member_code 発注者コード
	 * @param string $delivery_code 納品先コード
	 */
	public static function get_nearest_shipping_date($table, $member_code, $delivery_code = null) {
		$member = \Model_Member::query()->where('code', $member_code)->get_one();
		$lead_time = Arr::get($member, 'lead_time');

		$limit = 10;
		$day = 2;

		if (!is_null($lead_time)) {
			$day += intval($lead_time);
		}

		$start = date('Ymd', strtotime('+' . $day . ' day'));
		$end = date('Ymd', strtotime($limit . ' day', strtotime($start)));
		$date = $start;

		$list_holiday = self::get_list_holiday($start, $end);
		$list_week = self::get_list_week($table, $member_code, $delivery_code);

		if (empty($list_week)) {
			return null;
		}
		while (true) {
			if (array_key_exists(date('w', strtotime($date)), $list_week)) {
				if (!array_key_exists($date, $list_holiday)) {
					return $date;
				}
			}
			$date = date('Ymd', strtotime($date . ' +1 day'));
		}
	}

	/**
	 * 最短納品日を取得する
	 *
	 * @param string $member_code 発注者コード
	 */
	public static function get_nearest_delivery_date($member_code) {
		$member = \Model_Member::query()->where('code', $member_code)->get_one();
		$lead_time = Arr::get($member, 'lead_time');
		$limit = 10;

		$start = date('Ymd', strtotime('+1 day'));
		$end = date('Ymd', strtotime('+' . ($limit + 1) . ' day'));
		$nearest_ship_date = $start;

		$list_holiday = self::get_list_holiday($start, $end);
		while (true) {
			if (!array_key_exists($nearest_ship_date, $list_holiday)) {
				return date('Ymd', strtotime($nearest_ship_date . ' +' . ($lead_time + 1) . ' day'));
			}
			$nearest_ship_date = date('Ymd', strtotime($nearest_ship_date . ' +1 day'));
		}
	}

	/**
	 * 納期初期選択となる日付をリードタイム、配達曜日から算出する
	 *
	 * @param string $table テーブル名
	 * @param string $member_code 発注者コード
	 * @param string $delivery_code 納品先コード
	 */
	public static function get_nearest_delivery_week($table, $member_code, $delivery_code = null) {
		$list_week = self::get_list_week($table, $member_code, $delivery_code);
		if (empty($list_week)) {
			return null;
		}

		$delivery_date = self::get_nearest_delivery_date($member_code);
		while (true) {
			if (array_key_exists(date('w', strtotime($delivery_date)), $list_week)) {
				return $delivery_date;
			}
			$delivery_date = date('Ymd', strtotime($delivery_date . ' +1 day'));
		}
	}

	/**
	 * 選択納期から出荷日を逆算する
	 *
	 * @param string $member_code 発注者コード
	 * @param string $delivery_date 選択納期(YYYYMMDD)
	 */
	public static function calc_shipping_date($member_code, $delivery_date) {
		if (empty($delivery_date)) {
			return null;
		}
		$member = \Model_Member::query()->where('code', $member_code)->get_one();
		$lead_time = Arr::get($member, 'lead_time');

		$start = date('Ymd', strtotime('+1 day'));
		$end = date('Ymd', strtotime($delivery_date . ' -' . ($lead_time + 1) . ' day'));
		$shipping_date = $end;

		$list_holiday = self::get_list_holiday($start, $end);
		while (true) {
			if (!array_key_exists($shipping_date, $list_holiday)) {
				return $shipping_date;
			}
			$shipping_date = date('Ymd', strtotime($shipping_date . ' -1 day'));
		}

	}

	/**
	 * 非営業日リストを取得する
	 *
	 * @param string $start 開始日
	 * @param string $end 終了日
	 */
	private static function get_list_holiday($start, $end){
		$holidays = \Model_Holiday::query()
			->where('date', '>=', $start)
			->where('date', '<=', $end)
			->get();

		$list_holiday = array();
		foreach($holidays as $holiday){
			$key = date('Ymd', strtotime($holiday->date));
			$list_holiday[$key] = $key;
		}

		return $list_holiday;
	}

	/**
	 * 配達曜日リストを取得する
	 *
	 * @param string $table テーブル名
	 * @param string $member_code 発注者コード
	 * @param string $delivery_code 納品先コード
	 */
	private static function get_list_week($table, $member_code, $delivery_code) {
		$query = DB::select('delivery_week_code')
					->where('del_flg', '=', UNDELETED)
					->from($table);

		if ('members' == $table) {
			$query->where('code', '=', $member_code);
		} else {
			$query->where('member_code', '=', $member_code);
			$query->where('code', '=', $delivery_code);
		}

		$result = $query->execute()->current();

		if (empty($result)) {
			return array();
		}

		$delivery_week_code = Arr::get($result, 'delivery_week_code');
		if (empty($delivery_week_code)) {
			return array();
		}

		$query = DB::select('delivery_flg_sun', 'delivery_flg_mon', 'delivery_flg_tue',
							'delivery_flg_wed', 'delivery_flg_thu', 'delivery_flg_fri', 'delivery_flg_sat')
					->where('del_flg', '=', UNDELETED)
					->where('code', '=', $delivery_week_code)
					->from('delivery_weeks');

		$delivery_week = $query->execute()->current();
		if (empty($delivery_week)) {
			return array();
		}

		$list_week = array();
		if (Arr::get($delivery_week, 'delivery_flg_sun') == 1) {
			$list_week[0] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_mon') == 1) {
			$list_week[1] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_tue') == 1) {
			$list_week[2] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_wed') == 1) {
			$list_week[3] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_thu') == 1) {
			$list_week[4] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_fri') == 1) {
			$list_week[5] = true;
		}
		if (Arr::get($delivery_week, 'delivery_flg_sat') == 1) {
			$list_week[6] = true;
		}

		return $list_week;
	}

	/**
	 * 数値配列の要素に加算する
	 *
	 * @param array $array 配列
	 * @param mix $key キー
	 * @param numeric $value 加算値
	 */
	public static function add_array_numeric_value(&$array, $key, $value) {
		if (!array_key_exists($key, $array)) {
			$array[$key] = 0;
		}
		$array[$key] += $value;
	}
}