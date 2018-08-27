<?php
use Fuel\Core\Presenter;
use Fuel\Core\Session;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Uri;
use Auth\Auth;
use Fuel\Core\Asset;

/**
 * 基底プレゼンタクラス
 */
class Presenter_Base extends Presenter {

	/**
	 * @see \Fuel\Core\Presenter::before()
	 */
	public function before() {
		parent::before();

		$this->message = function() {
			return $this->message();
		};

		$this->validate_error_message = function($field_name) {
			return $this->validate_error_message($field_name);
		};

		$this->validate_search_error_message = function($field_name) {
			return $this->validate_search_error_message($field_name);
		};

		$this->validate_upload_error_message = function() {
			return $this->validate_upload_error_message();
		};

		$this->format_price = function($data, $field) {
			return $this->format_price($data, $field);
		};

		$this->format_number = function($data, $field, $default = '---') {
			return $this->format_number($data, $field, $default);
		};

		$this->format_date = function($date, $field, $format = 'Y-m-d H:i:s', $default = '---') {
			return $this->format_date($date, $field, $format, $default);
		};

		$this->validate_error = function($field_name) {
			return $this->validate_error($field_name);
		};
	}

	/**
	 * ログインユーザIDを取得する
	 */
	protected function get_member_id() {
		return Auth::instance()->get_user_id()[1];
	}

	/**
	 * メッセージを表示する
	 */
	private function message() {
		$message = Session::get_flash(SESSION_KEY_ERROR_MESSAGE);
		if (!is_null($message)) {
			return html_tag('p', array('class' => 'error'), $message);
		}

		$message = Session::get_flash(SESSION_KEY_INFO_MESSAGE);
		if (!is_null($message)) {
			return html_tag('p', array('class' => 'info'), $message);
		}
		return '';
	}

	/**
	 * バリデートエラーメッセージを表示する
	 *
	 * @param $fieldName フィールド名
	 */
	private function validate_error_message($field_name) {
		$message = Session::get_flash('validate_errors.' . $field_name);
		if (is_null($message)) {
			return '';
		}
		return html_tag('p', array('class' => 'error'), $message);
	}

	/**
	 * 検索条件バリデートエラーメッセージを表示する
	 *
	 * @param $fieldName フィールド名
	 */
	private function validate_search_error_message($field_name) {
		$message = Session::get_flash('validate_search_errors.' . $field_name);
		if (is_null($message)) {
			return '';
		}
		return html_tag('p', array('class' => 'error'), $message);
	}

	/**
	 * アップロードエラーメッセージを表示する
	 */
	private function validate_upload_error_message() {
		$messages = Session::get_flash('validate_upload_errors');
		if (empty($messages)) {
			return '';
		}

		$result = '';
		foreach ($messages as $num => $message) {
			if (is_array($message)) {
				foreach ($message as $msg) {
					$result .= html_tag('p', array('class' => 'error'), $num . '行目:' . $msg) . "\n";
				}
			} else {
				$result .= html_tag('p', array('class' => 'error'), $message) . "\n";
			}
		}
		return $result;
	}

	/**
	 * 金額フォーマット
	 * @param $data 配列
	 * @param $field フィールド
	 */
	private function format_price($data, $field) {
		$price = Arr::get($data, $field);
		if (is_null($price) || !is_numeric($price)) {
			return '';
		}
		return number_format($price);
	}

	/**
	 * 数値フォーマット
	 *
	 * @param array $data データ配列
	 * @param string $field フィールド
	 * @param string $default デフォルト値
	 */
	private function format_number($data, $field, $default) {
		$number = Arr::get($data, $field);
		if (is_null($number) || !is_numeric($number)) {
			return $default;
		}
		return number_format($number);
	}

	/**
	 * 日付フォーマット
	 *
	 * @param array $data データ配列
	 * @param string $field フィールド
	 * @param string $format フォーマット
	 * @param string $default デフォルト値
	 */
	private function format_date($data, $field, $format, $default) {
		$datetime = Arr::get($data, $field);
		if (is_null($datetime)) {
			return $default;
		}
		return date($format, strtotime($datetime));
	}

	/**
	 * バリデートエラーがあるかチェック
	 *
	 * @param $fieldName フィールド名
	 */
	private function validate_error($field_name) {
		$validate = Session::get_flash('validate_errors.' . $field_name);
		$validate_upload = Session::get_flash('validate_upload_errors');
		if (!is_null($validate) || !is_null($validate_upload)) {
			return true;
		}

		return false;
	}
}