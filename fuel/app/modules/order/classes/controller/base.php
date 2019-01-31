<?php
namespace Order;

use Fuel\Core\Controller_Template;
use Fuel\Core\Uri;
use Fuel\Core\Asset;
use Fuel\Core\View;
use Fuel\Core\Config;
use Fuel\Core\Inflector;
use Fuel\Core\Request;
use Fuel\Core\Presenter;
use Fuel\Core\Session;
use Auth\Auth;
use Fuel\Core\Response;
use Fuel\Core\Validation;
use Fuel\Core\Upload;
use Fuel\Core\Cache;

/**
 * 基底コントローラクラス
 */
class Controller_Base extends Controller_Template {

	/**
	 * テンプレート
	 */
	public $template = 'layout/default';

	/**
	 * 検索補助表示
	 */
	public $visible_support_search = false;

	/**
	 * カートクリア表示
	 */
	public $visible_clear_carts = false;

	/**
	 * ページタイトル
	 */
	protected $title = '';

	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = true;

	/**
	 * メンテナンスチェックの有無
	 */
	protected $check_maintenance = true;

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array();

	/**
	 * 固有JSファイルリスト
	 */
	protected $page_js = array();

	/**
	 * バリデートエラーメッセージ
	 */
	private $validate_errors = array();

	/**
	 * @see \Fuel\Core\Controller_Template::before()
	 */
	public function before() {
		if ($this->is_dialog()) {
			$this->template = 'layout/dialog';
		}
		parent::before();

		if ($this->check_maintenance && $this->is_maintenance()) {
			Response::redirect('/order/maintenance');
		}

		if ($this->check_auth && !Auth::check()) {
			Response::redirect('/order/login');
		}
		Asset::add_path('assets/order/images', 'img');
		Asset::add_path('assets/order/css', 'css');
		Asset::add_path('assets/order/js', 'js');

		Asset::js($this->page_js, array(), 'page_js', false);
	}

	/**
	 * @see \Fuel\Core\Controller_Template::after()
	 */
	public function after($response) {
		Session::set_flash('validate_errors', $this->validate_errors);

		return parent::after($response);
	}

	/**
	 * 描画処理
	 * @param $data データ
	 * @param $view ビュー
	 */
	protected function render($data = array(), $view = null) {
		if (is_null($view)) {
			$view = $this->create_view_name();
		}

		$presenter = $view;
		if (!$this->exist_presenter($view)) {
			$presenter = 'base';
		}

		$this->template->TITLE = $this->title;
		$this->template->content = Presenter::forge($presenter, 'view', null, $view)->set('data', $data);
	}

	/**
	 * お知らせメッセージを設定する
	 * @param $message お知らせメッセージ
	 */
	protected function set_info_message($message) {
		Session::set_flash(SESSION_KEY_INFO_MESSAGE, $message);
	}

	/**
	 * エラーメッセージを設定する
	 * @param $message エラーメッセージ
	 */
	protected function set_error_message($message) {
		Session::set_flash(SESSION_KEY_ERROR_MESSAGE, $message);
	}

	/**
	 * バリデートインスタンスを生成する
	 */
	protected function create_validation() {
		$validation = Validation::forge();
		$validation->add_callable('common_validation');

		return $validation;
	}

	/**
	 * バリデートエラーを追加する
	 *
	 * @param string $field フィールド名
	 * @param string $message メッセージ
	 */
	protected function add_validate_error($field, $message) {
		$this->validate_errors[$field] = $message;
	}

	/**
	 * バリデート処理
	 *
	 * @param $validation バリデートインスタンス
	 * @param $data バリデート対象データ
	 */
	protected function validate($validation, $data = null) {
		if ($validation->run($data)) {
			return true;
		}

		$errors = $validation->error();
		foreach ($errors as $key => $value) {
			$this->validate_errors[$key] = $value->get_message();
		}

		return false;
	}

	/**
	 * アップロードバリデート処理
	 * @param $field フィールド名
	 * @param $required 必須
	 */
	protected function validate_upload($field, $required = false) {
		$upload = \Common_Upload::instance();
		if ($upload->is_valid($field)) {
			return true;
		}

		$flg = true;
		$errors = $upload->get_errors($field);
		foreach ($errors as $key => $error) {
			if (!$required && $key == UPLOAD_ERR_NO_FILE) {
				continue;
			}

			$flg = false;
			$this->validate_errors[$field] = $error;
		}

		return $flg;
	}

	/**
	 * ログイン中の発注者アカウントIDを取得する
	 */
	protected function get_member_id() {
		return  Auth::instance()->get_user_id()[1];
	}

	/**
	 * メンテナンス判定
	 *
	 * @return boolean true:表示 false:非表示
	 */
	protected function is_maintenance() {
		$result = false;

		try {
			$maintenance_flg = Cache::get(CACHE_KEY_MAINTENANCE_FLG);

			if ($maintenance_flg === DISPLAY) {
				$result = true;
			}
		} catch (\CacheNotFoundException $e) {
			Cache::delete(CACHE_KEY_MAINTENANCE_FLG);

			$setting = \Model_Setting::find('first');
			if ($setting->maintenance_flg === DISPLAY) {
				$result = true;
			}

			// 有効期限24時間
			Cache::set(CACHE_KEY_MAINTENANCE_FLG, $setting->maintenance_flg, 3600 * 24);
		}

		return $result;
	}

	/**
	 * ダイアログか否かを返す
	 */
	private function is_dialog() {
		$action = Request::main()->action;
		return in_array($action, $this->dialogs);
	}

	/**
	 * ビュー名を生成する
	 */
	private function create_view_name() {
		$name = Request::active()->controller;
		$controller = substr($name, strpos($name, 'Controller_'));
		$controllers = explode('_', $controller);
		array_shift($controllers);

		return mb_strtolower(implode('/', $controllers)) . '/' . Request::active()->action;
	}

	/**
	 * プレゼンタ存在チェック
	 * @param $presenter プレゼンタ
	 */
	private function exist_presenter($presenter) {
		if (is_null($presenter)) {
			return false;
		}

		$presenter = Inflector::words_to_upper(str_replace(
				array('/', DS),
				'_',
				strpos($presenter, '.') === false ? $presenter : substr($presenter, 0, -strlen(strrchr($presenter, '.')))
		));
		$namespace = Request::active() ? ucfirst(Request::active()->module) : '';
		$class = $namespace.'\\Presenter_'.$presenter;

		return class_exists($class);
	}
}