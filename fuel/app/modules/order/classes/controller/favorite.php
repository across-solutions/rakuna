<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\DB;
/**
 * [発注]お気に入りコントローラクラス
 */
class Controller_Favorite extends Controller_Base {

	/**
	 * 検索補助表示
	 */
	public $visible_support_search = true;

	/**
	 * ページタイトル
	 */
	protected $title = 'お気に入り';

	/**
	 * 一覧表示-初期表示・検索
	 */
	public function action_index() {
		$data = Input::post();
		if (empty($data) || !isset($data['sort_num'])) {
			$this->render();
			return;
		}

		$assigns = $this->list_sort($this->get_member_id());

		if (!$this->validate_sort($data, count($assigns))) {
			$this->render($data);
			return;
		}

		$sorted = $this->sort($data['sort_num'], $assigns);

		$this->update_sort($sorted);

		$this->render();
	}

	/**
	 * 並び順バリデート
	 *
	 * @param  array $data  フォームデータ
	 * @param  int   $count データ件数
	 * @return boolean
	 */
	private function validate_sort($data, $count) {
		$validation = $this->create_validation();

		$sorts = $data['sort_num'];
		foreach ($sorts as $assign_id => $sort) {
			$validation
				->add('sort_num.' . $assign_id, '並び順')
				->add_rule('numeric')
				->add_rule('numeric_between', 1, $count);
		}

		$error = false;

		$duplicates = array();
		foreach ($sorts as $assign_id => $sort) {
			if (empty($sort)) {
				continue;
			}
			if (!isset($duplicates[$sort])) {
				$duplicates[$sort] = array();
			}
			$duplicates[$sort][] = $assign_id;
		}
		foreach ($duplicates as $duplicate) {
			if (count($duplicate) > 1) {
				foreach ($duplicate as $duplicate_id) {
					parent::add_validate_error('sort_num.' . $duplicate_id, '並び順が重複しています');
					$error = true;
				}
			}
		}

		return $this->validate($validation, $data) && !$error;
	}

	/**
	 * 並び替え処理
	 *
	 * @param  array $sort_nums フォームデータ
	 * @param  array $assigns   並び替え前データ
	 * @return array $results   ソート済みデータ
	 */
	private function sort($sort_nums, $assigns) {
		$sorts = array();
		foreach ($sort_nums as $assign_id => $sort_num) {
			if (!empty($sort_num)) {
				$sorts[$sort_num] = $assign_id;
			}
		}

		$count = count($assigns);

		$results = array();
		for ($i = 1; $i < $count + 1; $i++) {
			if (isset($sorts[$i])) {
				$results[] = $sorts[$i];
				continue;
			}

			$assign_id = null;
			while (true) {
				$assign_id = array_shift($assigns);
				if (!is_null($assign_id) && array_search($assign_id, $sorts)) {
					continue;
				}
				break;
			}

			$results[] = $assign_id;
		}

		return $results;
	}

	/**
	 * 並び順を更新する
	 *
	 * @param  array $sorted 並び替え後データ
	 * @throws \Exception
	 * @return boolean
	 */
	private function update_sort($sorted) {
		try {
			DB::start_transaction();

			$count = count($sorted);
			foreach ($sorted as $index => $fav_id) {
				$this->update_fav_sort($fav_id, $count - $index);
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return true;
	}

	/**
	 * 並び順を更新する
	 *
	 * @param  int $fav_id お気に入りID
	 * @param  int $sort   並び順
	 * @return object \Model_Favorite
	 */
	private function update_fav_sort($fav_id, $sort) {
		return DB::update('favorites')
			->value('sort_num', $sort)
			->value('update_user_id', $this->get_member_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $fav_id)
			->execute();
	}

	/**
	 * ソートリストを取得する
	 *
	 * @param  string $member_id 発注者コード
	 * @return array  $list      お気に入りリスト
	 */
	private function list_sort($member_id) {
		$query = DB::select(array('favorites.id', 'id'), 'sort_num')
			->from('favorites')
				->join('items', 'INNER')
				->on('favorites.item_code', '=', 'items.code')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED));

		if (Common_Assign::has_assign($member_id)) {
			$query->join('item_assigns', 'INNER')
				->on('item_assigns.item_code', '=', 'items.code')
				->and_on('item_assigns.member_id', '=', DB::expr($member_id))
				->and_on('item_assigns.del_flg', '=', DB::expr(UNDELETED));
		}

		$query->where('favorites.member_id', '=', $member_id)
			->where('favorites.del_flg', '=', UNDELETED)
			->order_by('sort_num', 'desc');

		$sorts = $query->execute()->as_array();

		$list = array();
		foreach ($sorts as $sort) {
			$list[] = $sort['id'];
		}

		return $list;
	}
}