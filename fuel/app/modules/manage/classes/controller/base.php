<?php
namespace Manage;

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
use Fuel\Core\Format;
use Fuel\Core\Lang;


/**
 * 基底コントローラクラス
 */
class Controller_Base extends Controller_Template {

	/**
	 * テンプレート
	 */
	public $template = 'layout/default';

	/**
	 * ページタイトル
	 */
	protected $title = '';

	/**
	 * ログインチェックの有無
	 */
	protected $check_auth = true;

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array();

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

		if ($this->check_auth && !Auth::check()) {
			Response::redirect('/manage/login');
		}

		$manage_mosauth_config = Config::load('manage::mosauth');

		$controller_name = Request::main()->controller;
 		$controllers_for_user = $manage_mosauth_config['controllers_for_user'];
 		$controllers_no_permission_check = $manage_mosauth_config['controllers_no_permission_check'];

		if  ( array_key_exists( $controller_name, $controllers_no_permission_check ) ) {
			$page_auth = '';
		}
		else if ( array_key_exists( $controller_name, $controllers_for_user ) ) {
			$page_auth = Config::get('define.manage_page_label.2');
		}
		else {
			$page_auth = Config::get('define.manage_page_label.1');
		}

		if(!empty($page_auth)){
			$auth = Auth::instance();
			if( !$auth->has_access( $page_auth, $auth->get_user_mosgroup() ) ){
				//Response::redirect('/manage/dialog/forbidden');
				Response::redirect('/manage/error/403');
			}
		}

		Asset::add_path('assets/manage/images', 'img');
		Asset::add_path('assets/manage/css', 'css');
		Asset::add_path('assets/manage/js', 'js');
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

		$this->template->title = $this->title;
		$this->template->content = Presenter::forge($presenter, 'view', null, $view)->set('data', $data);
	}

	/**
	 * CSVファイルをダウンロードする
	 *
	 * @param string $filename ファイル名
	 * @param array $data データ配列
	 */
	protected function csv_download($filename, $data) {
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/csv');
		$response->set_header('Content-Disposition', 'attachment; filename="'. $filename .'"');
		$response->set_header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		$response->set_header('Expires', 'Thu, 01 Dec 1994 16:00:00 GMT');
		$response->set_header('Pragma', 'no-cache');
		$response->body(chr(255) . chr(254). mb_convert_encoding(Format::forge($data)->to_csv(), 'UTF-16LE', 'UTF-8'));
		return $response;
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
	 * ログイン中の管理者アカウントIDを取得する
	 */
	protected function get_user_id() {
		return  Auth::instance()->get_user_id()[1];
	}

	/**
	 * ファイルアップロード
	 *
	 * @param string $config_key フィールド名
	 */
	protected function process_upload($config_key) {
		Upload::process(Config::get('upload.setting.' . $config_key));
	}

	protected function get_upload_file($field) {
		$file = Upload::get_files($field);
		if (empty($file)) {
			return null;
		}
		return $file['file'];
	}

	/**
	 * ファイルアップロード保存処理
	 *
	 * @param function $before 前処理
	 * @param function $after 後処理
	 */
	protected function save_upload($before = null, $after = null) {
		if (!is_null($before)) {
			Upload::register('before', $before);
		}
		if (!is_null($after)) {
			Upload::register('after', $after);
		}
		Upload::save();
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
	protected function validate($validation, $data) {
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

	protected function validate_file_upload($field, $required = false) {
		$flg = true;
		foreach (Upload::get_errors() as $errors) {
			if ($errors['field'] == $field) {
				foreach ($errors['errors'] as $error) {
					if (!$required && $error['error'] == UPLOAD_ERR_NO_FILE) {
						continue;
					}
					$this->validate_errors[$field] = $this->replace_upload_error_message($error['message'], $field);
					$flg = false;
				}
			}
		}
		return $flg;
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

	/**
	 * アップロードエラーメッセージ中の置換文字列を置換する
	 *
	 * @param string $message メッセージ
	 * @param string $field フィールド名
	 */
	private function replace_upload_error_message($message, $field) {
		$replaces = Config::get('upload.message_replaces.' . $field);
		foreach ($replaces as $key => $value) {
			$message = str_replace(':' . $key, $value, $message);
		}
		return $message;
	}
}