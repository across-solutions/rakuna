<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\Cookie;
use Fuel\Core\Session;

/**
 * 商品一覧プレゼンタクラス
 */
class Presenter_Item_Index extends \Presenter_Pagination {

	/**
	 * @see \Fuel\Core\Presenter::before()
	 */
	public function before() {
		parent::before();

		$this->sort_class = function($key) {
			$sort = Input::get('sort');
			if (Config::get('define.search_sort.' . $key) == $sort) {
				return 'selected';
			}
			if (is_null($sort) && ('frequency' == $key)) {
				return 'selected';
			}
			return '';
		};

		$this->sort_icon = function($key) {
			$sort = Input::get('sort');
			if (Config::get('define.search_sort.' . $key) == $sort) {
				return 'icon-chevron-down';
			}
			if (is_null($sort) && ('frequency' == $key)) {
				return 'icon-chevron-down';
			}
			return '';
		};

		$this->validate_error_all = function() {
			$messages  = Session::get_flash('validate_errors');
			if (empty($messages)) {
				return '';
			}

			$result = array();
			foreach ($messages as $message) {
				if (!in_array($message, $result)) {
					$result[] = $message;
				}
			}

			return html_tag('p', array('class' => 'info'), implode('<br/>', $result));
		};

		$this->validate_error_class = function($field_name) {
			$message = Session::get_flash('validate_errors.' . $field_name);
			if (is_null($message)) {
				return '';
			}
			return 'error_field';
		};
	}

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		$this->categories = Common_Category::list_category($this->get_member_id(), 'すべてのカテゴリ');
		$this->mode = $this->get_mode();

		parent::view();
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select(DB::expr('COUNT(*) as count'))
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->join('carts', 'LEFT')
				->on('carts.item_id', '=', 'items.id')
				->on('carts.member_id', '=', DB::escape($member_id))
			->join('favorites', 'LEFT')
				->on('favorites.item_code', '=', 'items.code')
				->on('favorites.member_id', '=', DB::escape($member_id))
				->on('favorites.del_flg', '=', DB::escape(UNDELETED))
			->join('order_frequencies', 'LEFT')
				->on('order_frequencies.item_code', '=', 'items.code')
				->on('order_frequencies.member_id', '=', DB::escape($member_id))
				->on('order_frequencies.del_flg', '=', DB::escape(UNDELETED));

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$this->add_condition($query, $data);

		$result = $query->execute()->current();

		return $result['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$member_id = $this->get_member_id();

		$member = \Model_Member::find($member_id);
		$member_group_code = Arr::get($member, 'member_groups.code');

		$query = DB::select('items.id', 'items.code', 'items.name', 'items.comment',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size', 'items.type',
				array('item_categories.name', 'category_name'), 'carts.amount',
				'carts.amount_case', array('favorites.id', 'favorite_id'),
				'items.price', 'items.price_case',
				array('item_assigns.price_case', 'assign_price_case'),
				array('item_assigns.price', 'assign_price'),
				array('group_assigns.price_case', 'group_price_case'),
				array('group_assigns.price', 'group_price'),
				'item_assigns.hidden_flg_single', 'item_assigns.hidden_flg_case')
			->from('items')
			->join('item_categories', 'LEFT')
				->on('item_categories.id', '=', 'items.item_category_id')
				->on('item_categories.del_flg', '=', DB::escape(UNDELETED))
			->join('carts', 'LEFT')
				->on('carts.item_id', '=', 'items.id')
				->on('carts.member_id', '=', DB::escape($member_id))
			->join('favorites', 'LEFT')
				->on('favorites.item_code', '=', 'items.code')
				->on('favorites.member_id', '=', DB::escape($member_id))
				->on('favorites.del_flg', '=', DB::escape(UNDELETED))
			->join('order_frequencies', 'LEFT')
				->on('order_frequencies.item_code', '=', 'items.code')
				->on('order_frequencies.member_id', '=', DB::escape($member_id))
				->on('order_frequencies.del_flg', '=', DB::escape(UNDELETED))
			->limit($limit)
			->offset($offset);

		//if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->on('item_assigns.member_id', '=', DB::escape($member_id))
				->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		//}

		$query->join('group_assigns', 'LEFT')
			->on('group_assigns.item_code', '=', 'items.code')
			->on('group_assigns.member_group_code', '=', DB::escape($member_group_code))
			->on('group_assigns.del_flg', '=', DB::escape(UNDELETED));

		$this->add_condition($query, $data);
		$this->add_sort($query, Input::get());

		return $query->execute()->as_array();
	}

	/**
	 * @see Presenter_Pagination::modifier()
	 */
	protected function modifier(&$row) {
		$tax_rate = \Common_Setting::get('tax_rate');
		$tax_rounding = \Common_Setting::get('tax_rounding');
		$price = $this->value($row, 'price', 'assign_price', 'group_price');
		$price_case = $this->value($row, 'price_case', 'assign_price_case', 'group_price_case');
		$row['price'] = $price * $row['size'];
		$row['price_case'] = $price_case * $row['size_case'];
		$row['price_tax'] = \Common_Util::add_tax($row['price']);
		$row['price_case_tax'] = \Common_Util::add_tax($row['price_case']);
		$row['amount'] = is_null($row['amount']) ? 0 : $row['amount'];
		$row['amount_case'] = is_null($row['amount_case']) ? 0 : $row['amount_case'];
	}

	/**
	 * @see Presenter_Pagination::per_page()
	 */
	protected function per_page() {
		return \Common_Setting::get('item_num');
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data GETデータ
	 */
	protected function add_condition(&$query, $data) {
		$query->where('items.del_flg', '=', DB::expr(UNDELETED));

		// カテゴリ
		$item_category_id = Arr::get($data, 'category');
		if (!is_null($item_category_id) && trim($item_category_id) != '') {
			$query->where('item_category_id', '=', $item_category_id);
		}

		// フリーワード
		$search_field = Arr::get($data, 'freeword');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('items.search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}
	}

	private function create_query($fields) {

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
			case Config::get('define.search_sort.frequency'):
				$query->order_by('order_frequencies.frequency', 'desc');
				break;
			case Config::get('define.search_sort.item_name'):
				$query->order_by('items.name', 'asc');
				break;
			default:
				$query->order_by('order_frequencies.frequency', 'desc');
				break;
		}
		$query->order_by('items.code', 'asc');
	}

	/**
	 * 表示形式を取得する
	 */
	private function get_mode() {
		$mode = null;
		switch (Input::get('mode')) {
			case Config::get('define.search_mode.normal'):
				$mode = Config::get('define.search_mode.normal');
				break;
			case Config::get('define.search_mode.image'):
				$mode = Config::get('define.search_mode.image');
				break;
			case Config::get('define.search_mode.list'):
				$mode = Config::get('define.search_mode.list');
				break;
			default:
				$mode = Cookie::get(COOKIE_KEY_ITEM_MODE, Config::get('define.search_mode.normal'));
				break;
		}
		Cookie::set(COOKIE_KEY_ITEM_MODE, $mode, COOKIE_EXPIRATION_ITEM_MODE);
		return $mode;
	}

	/**
	 * 値を取得する(後のキーが優先される)
	 *
	 * @param array $data データ
	 * @param string $key1 キー1
	 * @param string $key2 キー2
	 * @param string $key3 キー3
	 */
	private function value($data, $key1, $key2 = null, $key3 = null) {
		$value = $data[$key1];

		if (!is_null($key2) && !is_null($data[$key2])) {
			$value = $data[$key2];
		}

		if (!is_null($key3) && !is_null($data[$key3])) {
			$value = $data[$key3];
		}

		return $value;
	}

}