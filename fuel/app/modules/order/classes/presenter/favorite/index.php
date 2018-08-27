<?php
namespace Order;

use Fuel\Core\DB;
use Fuel\Core\Arr;
use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\Cookie;
use Fuel\Core\Session;

/**
 * お気に入り一覧プレゼンタクラス
 */
class Presenter_Favorite_Index extends Presenter_Item_Index {

	/**
	 * @see Presenter_Item_Index::before()
	 */
	public function before() {
		parent::before();

		$this->sort_mode = $this->get_sort_mode();
		$this->sortable = $this->can_sort();

		$this->sort_num = array();
		if (is_array($this->data) && isset($this->data['sort_num'])) {
			$this->sort_num = $this->data['sort_num'];
		}

		$this->sort_class = function($key) {
			$sort = Input::get('sort');
			if (Config::get('define.search_sort.' . $key) == $sort) {
				return 'selected';
			}
			if (is_null($sort) && ('favorite_sort' == $key)) {
				return 'selected';
			}
			return '';
		};

		$this->sort_icon = function($key) {
			$sort = Input::get('sort');
			if (Config::get('define.search_sort.' . $key) == $sort) {
				return 'icon-chevron-down';
			}
			if (is_null($sort) && ('favorite_sort' == $key)) {
				return 'icon-chevron-down';
			}
			return '';
		};
	}

	/**
	 * @see \Order\Presenter_Item_Index::add_condition()
	 */
	protected function add_condition(&$query, $data) {
		parent::add_condition($query, $data);
		$query->where('favorites.member_id', '=', DB::expr($this->get_member_id()));
	}

	/**
	 * 並び順を付与する
	 *
	 * @param Query $query Query
	 * @param array $data GETデータ
	 */
	protected function add_sort(&$query, $data) {
		$sort = Arr::get($data, 'sort');
		switch ($sort) {
			case Config::get('define.search_sort.favorite_sort'):
				$query->order_by('favorites.sort_num', 'desc');
				break;
			case Config::get('define.search_sort.frequency'):
				$query->order_by('order_frequencies.frequency', 'desc');
				break;
			default:
				$query->order_by('favorites.sort_num', 'desc');
				break;
		}
		parent::add_sort($query, $data);
	}

	/**
	 * ソート順を取得する
	 */
	private function get_sort_mode() {
		$sort = Arr::get(Input::get(), 'sort');
		if (empty($sort)) {
			$sort = Cookie::get(COOKIE_KEY_SORT_MODE, Config::get('define.search_sort.favorite_sort'));
		}

		Cookie::set(COOKIE_KEY_SORT_MODE, $sort, COOKIE_EXPIRATION_SORT_MODE);

		return $sort;
	}

	/**
	 * 並び替え可能かを返す
	 */
	private function can_sort() {
		if (!empty($this->sort_mode) && $this->sort_mode != Config::get('define.search_sort.favorite_sort')) {
			return false;
		}
		$category = Input::get('category');
		if (!is_null($category) && trim($category) != '') {
			return false;
		}
		$freeword = Input::get('freeword');
		if (!is_null($freeword) && trim($freeword) != '') {
			return false;
		}
		return true;
	}
}