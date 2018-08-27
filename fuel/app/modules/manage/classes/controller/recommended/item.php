<?php
namespace Manage;

use Fuel\Core\Validation;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\Arr;
use Fuel\Core\DB;
/**
 * いつもの商品管理コントローラクラス
 */
class Controller_Recommended_Item extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('add', 'add_save', 'edit', 'edit_save', 'delete', 'delete_save', 'upload_csv', 'upload_csv_save', 'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = 'いつもの商品管理';

	/**
	 * 一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	public function post_index(){
		$data = Input::post();

		$recommended_group_code = Arr::get($data, 'recommended_group_code');
		$recommended_group = \Model_Recommended_Group::query()
						   ->where('code', '=', $recommended_group_code)->get_one();

		if(is_null($recommended_group)){
			$this->set_error_message('いつものグループを選択してください。');
			$this->render($data);
			return;
		}

		$recommended_items = $this->list_sort($recommended_group->id);
		$count = count($recommended_items);

		if(!$this->validate_sort($data, count($recommended_items))){
			$this->render($data);
			return;
		}

		$sorted = $this->sort($data['sort_num'], $recommended_items);

		$this->update_sort($sorted);
		$this->render(array());
	}

	private function validate_sort($data, $count){
		$validation = $this->create_validation();
		$sorts = $data['sort_num'];
		foreach ($sorts as $assign_id => $sort) {
			$validation
				->add('sort_num.' . $assign_id, 'いつもの商品が全' . $count . '件ですので、並び順')
				->add_rule('numeric')
				->add_rule('numeric_between', 1, $count);
		}

		$error = false;

		$duplicates = array();
		foreach ($sorts as $id => $sort) {
			if (empty($sort)) {
				continue;
			}
			if (!isset($duplicates[$sort])) {
				$duplicates[$sort] = array();
			}
			$duplicates[$sort][] = $id;
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
	 * @param  array $recommended_items   並び替え前データ
	 * @return array $results   ソート済みデータ
	 */
	private function sort($sort_nums, $recommended_items) {
		$sorts = array();
		foreach ($sort_nums as $recommended_item_id => $sort_num) {
			if (!empty($sort_num)) {
				$sorts[$sort_num] = $recommended_item_id;
			}
		}

		$count = count($recommended_items);

		$results = array();
		for ($i = 1; $i < $count + 1; $i++) {
			if (isset($sorts[$i])) {
				$results[] = $sorts[$i];
				continue;
			}

			$recommended_item_id = null;
			while (true) {
				$recommended_item_id = array_shift($recommended_items);
				if (!is_null($recommended_item_id) && array_search($recommended_item_id, $sorts)) {
					continue;
				}
				break;
			}

			$results[] = $recommended_item_id;
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
			foreach ($sorted as $index => $id) {
				$this->update_recommend_item_sort($id, $count - $index);
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}

		return true;
	}

	private function update_recommend_item_sort($id, $sort){
		return DB::update('recommended_items')
			->value('sort_num', $sort)
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $id)
			->execute();
	}

	private function list_sort($recommended_group_id){
		$sorts = DB::select(array('recommended_items.id', 'id'),
										'sort_num')
						->from('recommended_items')
							->join('items', 'INNER')
							->on('recommended_items.item_code', '=', 'items.code')
							->and_on('items.del_flg', '=', DB::expr(UNDELETED))
						->where('recommended_items.del_flg', UNDELETED)
						->where('recommended_group_id', '=', $recommended_group_id)
						->order_by('sort_num', 'DESC')
						->execute()
						->as_array();

		$list = array();

		foreach ($sorts as $sort) {
			$list[] = $sort['id'];
		}

		return $list;
	}


	/**
	 * 追加画面-初期表示
	 */
	public function action_add() {
		$data = array();
		$data['recommended_group_code'] = Input::get('recommended_group_code');
		$this->render($data);
	}

	/**
	 * 追加画面-保存処理
	 */
	public function action_add_save() {
		$data = Input::post();
		if (!$this->validate_add($data)) {
			$this->render($data, 'recommended/item/add');
			return;
		}

		$recommended_group = \Model_Recommended_Group::query()
					->where('code', $data['recommended_group_code'])->get_one();

		if ($this->exist_recommended_item($recommended_group->id, $data['item_code'])) {
			$this->set_error_message('登録済みです');
			$this->render($data, 'recommended/item/add');
			return;
		}

		if (!$this->insert_recommended_item($recommended_group->id, $data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'recommended/item/add');
			return;
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	public function action_delete($id) {

		$data = \Model_Recommended_Item::find($id);
		if (empty($data)) {
			Response::redirect('/manage/dialog/not_found');
		}

		$this->render($data);
	}

	/**
	 * 編集画面-削除処理
	 */
	public function action_delete_save() {
		$data = Input::post();
		if (!isset($data['id']) || empty($data['id'])) {
			Response::redirect('/manage/dialog/not_found');
		}

		$recommended_item = \Model_Recommended_Item::find($data['id']);
		if (empty($recommended_item)) {
			Response::redirect('/manage/dialog/not_found');
		}

		if (!$recommended_item->soft_delete()) {
			$this->set_error_message('削除に失敗しました');
			Response::redirect('/manage/dialog/complete');
			return;
		}

		$this->set_info_message('削除しました');
		Response::redirect('/manage/dialog/complete');
	}


	/**
	 * いつもの商品CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * いつもの商品CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('recommended_item_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'recommended/item/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Recommended_Item($this->get_upload_file('recommended_item_csv'));

		$csv->parse();
		$csv->validate_data();

		if ($csv->has_error()) {
			$this->render(null, 'recommended/item/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('recommended_item_csv', true);
	}

	/**
	 * いつもの商品CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * いつもの商品CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Recommended_Item();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_RECOMMENDED_ITEM, $data);
	}

	/**
	 * 追加バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_add($data) {
		$validation = $this->create_validation();

		$validation->add('recommended_group_code', 'いつもの商品グループ')
			->add_rule('required')
			->add_rule('exist', 'recommended_groups', 'code');

		$validation->add('item_code', '商品コード')
			->add_rule('required')
			->add_rule('alphanum')
			->add_rule('max_length', 20)
			->add_rule('exist', 'items', 'code');


		$recommended_group = \Model_Recommended_Group::query()
						   ->where('code', $data['recommended_group_code'])->get_one();

		if(!is_null($recommended_group)){
			$recommended_items = $this->list_sort($recommended_group->id);
			$validation->add('sort_num', '順番')
				->add_rule('numeric')
				->add_rule('numeric_between', 1, count($recommended_items) + 1);
		}

		return $this->validate($validation, $data);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 */
	private function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('sort_num', '順番')
			->add_rule('required')
			->add_rule('numeric')
			->add_rule('numeric_between', 0, 9999999);

		return $this->validate($validation, $data);
	}


	/**
	 * 登録済みチェック
	 *
	 * @param int $recommended_group_id いつものグループID
	 * @param string $item_code 商品コード
	 */
	private function exist_recommended_item($recommended_group_id, $item_code) {
		return \Model_Recommended_Item::query()
			->where('recommended_group_id', $recommended_group_id)
			->where('item_code', $item_code)
			->count() > 0;
	}

	/**
	 * いつもの商品を登録する
	 *
	 * @param int $recommended_group_id いつものグループID
	 * @param array $data フォームデータ
	 */
	private function insert_recommended_item($recommended_group_id, $data) {
		$fields = array('item_code', 'sort_num');
		$values = \Common_Util::filter($data, $fields);
		$values['recommended_group_id'] = $recommended_group_id;

		$recommended_items = $this->list_sort($recommended_group_id);
		$recommended_item = \Model_Recommended_Item::forge($values);

		try{
			DB::start_transaction();
			if(!$recommended_item->save()){
				DB::rollback_transaction();
				return false;
			}

			if(empty($data['sort_num'])){
				$recommended_items[] = $recommended_item->id;
			} else {
				$insert_pos = (int)$data['sort_num']-1;
				array_splice($recommended_items, $insert_pos, 0, $recommended_item->id);
			}

			$this->update_sort($recommended_items);

			DB::commit_transaction();
		}catch (Exception $e){
			DB::rollback_transaction();
			return false;
		}

		return true;
	}

	/**
	 * いつもの商品を更新する
	 *
	 * @param Model_Recommended_Item $recommended_item 元データ
	 * @param array $data フォームデータ
	 */
	private function update_recommended_item($recommended_item, $data) {
		$recommended_item['sort_num'] = $data['sort_num'];

		return $recommended_item->save() !== false;
	}
}