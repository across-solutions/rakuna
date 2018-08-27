<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\DB;
use Fuel\Core\Arr;
use Fuel\Core\Session;
/**
 * いつもの商品管理一覧プレゼンタクラス
 */
class Presenter_Recommended_Item_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Base::view()
	 */
	public function view() {
		$this->recommended_groups = \Model_Recommended_Group::list_select('code', 'name', array('code' => 'asc'));
		$this->recommended_group_code = Input::get('recommended_group_code');
		$this->is_show_sort_button = $this->is_show_sort_button();

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

			return html_tag('p', array('class' => 'error'), implode('<br/>', $result));
		};

		$this->validate_error_class = function($field_name) {
			$message = Session::get_flash('validate_errors.' . $field_name);
			if (is_null($message)) {
				return '';
			}
			return 'error_field';
		};

		parent::view();
	}

	/**
	 * ソート機能を表示するかどうかを返す
	 */
	private function is_show_sort_button(){
		$recommended_group_code = Input::get('recommended_group_code');
		$item_code = Input::get('item_code');

		return !empty($recommended_group_code) && empty($item_code);
	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = DB::select(array(DB::expr('count(1)'), 'count'))
			->from('recommended_items')
			->join('items', 'INNER')
				->on('recommended_items.item_code', '=', 'items.code')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_groups', 'INNER')
				->on('recommended_items.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED));
		$this->add_condition($query, $data);

		$result = $query->execute()->as_array();

		return $result[0]['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = DB::select('recommended_items.id',
							'recommended_items.sort_num',
							array('recommended_groups.code', 'recommended_group_code'),
							array('recommended_groups.name', 'recommended_group_name'),
				array('items.code', 'item_code'), array('items.name', 'item_name'))
			->from('recommended_items')
			->join('items', 'INNER')
				->on('recommended_items.item_code', '=', 'items.code')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED))
			->join('recommended_groups', 'INNER')
				->on('recommended_items.recommended_group_id', '=', 'recommended_groups.id')
				->and_on('recommended_groups.del_flg', '=', DB::expr(UNDELETED))
			->order_by('recommended_groups.code', 'asc')
			->order_by('recommended_items.sort_num', 'desc')
			->limit($limit)
			->offset($offset);
		$this->add_condition($query, $data);

		return $query->execute()->as_array();
	}

	/**
	 * 検索条件を付与する
	 *
	 * @param Query $query Query
	 * @param array $data 検索条件
	 */
	private function add_condition(&$query, $data) {
		$query->where('recommended_items.del_flg', '=', DB::expr(UNDELETED));

		$recommended_group_code = Arr::get($data, 'recommended_group_code');
		if (!is_null($recommended_group_code) && trim($recommended_group_code) != '') {
			$query->where('recommended_groups.code', '=', $recommended_group_code);
		}

		$item_code = Arr::get($data, 'item_code');
		if (!is_null($item_code) && trim($item_code) != '') {
			$query->where('recommended_items.item_code', '=', $item_code);
		}
	}
}