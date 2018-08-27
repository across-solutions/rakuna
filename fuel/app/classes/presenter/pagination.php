<?php
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\Validation;

/**
 * ページネート用プレゼンタクラス
 */
abstract class Presenter_Pagination extends Presenter_Base {

	/**
	 * データ件数を取得する
	 *
	 * @param $data 検索条件
	 */
	abstract protected function get_count($data);

	/**
	 * ページデータを取得する
	 *
	 * @param $data 検索条件
	 * @param $limit limit
	 * @param $offset offset
	 */
	abstract protected function get_rows($data, $limit, $offset);

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$data = $this->get_search_data();

		if (!$this->validate($data)) {
			$this->_view->set_safe('pager', null);
			$this->_view->set_safe('data_count', 0);
			$this->rows = array();
			return;
		}

		$count = $this->get_count($data);

		$pagination = Pagination::forge('paginate', array(
			'total_items' => $count,
			'per_page' => $this->per_page(),
			'uri_segment' => 'page',
			'show_first' => true,
			'show_last' => true
		));

		$this->_view->set_safe('pager', $pagination->render());
		$this->_view->set_safe('data_count', $count);
		$this->_view->set_safe('page_index', $pagination->offset);

		$results = $this->get_rows($data, $pagination->per_page, $pagination->offset);
		array_walk($results, array($this, 'modifier'));

		$this->rows = $results;
	}

	/**
	 * 検索条件を取得する
	 */
	protected function get_search_data() {
		return Input::get();
	}

	/**
	 * 検索条件バリデート
	 *
	 * @param Validation $validation Validation
	 * @param array $data パラメータ
	 */
	protected function validate_search(&$validation, &$data) {
	}

	/**
	 * 検索条件バリデート
	 *
	 * @param array $data パラメータ
	 */
	protected function validate($data) {
		$validation = Validation::forge('validate_search');
		$validation->add_callable('common_validation');

		$this->validate_search($validation, $data);

		if ($validation->run($data)) {
			return true;
		}

		$errors = $validation->error();
		$validate_errors = array();
		foreach ($errors as $key => $value) {
			$validate_errors[$key] = $value->get_message();
		}
		Session::set_flash('validate_search_errors', $validate_errors);

		return false;
	}

	/**
	 * ページ当りの表示件数を取得する
	 */
	protected function per_page() {
		return Config::get('define.default_per_page');
	}

	/**
	 * 行データを加工する
	 * @param $row 行データ
	 */
	protected function modifier(&$row) {
	}
}