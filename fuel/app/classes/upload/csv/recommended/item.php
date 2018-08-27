<?php
use Fuel\Core\DB;
/**
 * いつもの商品CSVアップロードクラス
 */
class Upload_Csv_Recommended_Item extends Upload_Csv_Base {

	/**
	 * ヘッダ行の有無
	 */
	protected $has_header = true;

	/**
	 * いつものグループコードリスト
	 */
	private $recommended_groups = array();

	/**
	 * 商品コードリスト
	 */
	private $items = array();

	/**
	 * いつもの商品リスト
	 */
	private $recommended_items = array();


	private $recommend_group_data = array();

	/**
	 * コンストラクタ
	 *
	 * @param array $file CSVファイル
	 */
	public function __construct($file) {
		parent::__construct($file);

		$this->recommended_groups = $this->list_recommended_group_code();
		$this->items = $this->list_item_code();
		$this->recommended_items = $this->list_recommended_item();
	}

	/**
	 * 追加バリデーション
	 */
	public function validate_data(){

		if(!$this->validate_duplicate_code()){
			Session::set_flash('validate_upload_errors', $this->get_errors());
			return false;
		}

		$values = array_values($this->recommended_groups);

		foreach($values as $val){
			$this->recommend_group_data[$val] = array (
				'database' => array(),
				'csv' => array(),
				'delete' => array(),
				'sort' => array(),
				'count' => 0,
				'empty_sort_num' => array(),
			);
		}

		foreach($this->recommended_items as $recommended_group_id => $items){
			foreach($items as $item){
				$this->recommend_group_data[$recommended_group_id]['database'][$item['item_code']]= $item;
			}
		}

		$num = 1;
		$csv_data = $this->get_data();

		foreach($csv_data as $line){
			$group_code = $line['recommended_group_code'];
			$group_id = $this->recommended_groups[$group_code];
			$item_code = $line['item_code'];
			$sort_num = $line['sort_num'];

			if($line['control_code'] == '1'){ //削除
				$this->recommend_group_data[$group_id]['delete'][$item_code] = $line;
				unset($this->recommend_group_data[$group_id]['database'][$item_code]);

			} else {
				$this->recommend_group_data[$group_id]['csv'][$item_code] = $sort_num;

				if(empty($sort_num)){
					$this->recommend_group_data[$group_id]['empty_sort_num'][$item_code] = $line;

				}else{
					if(isset($this->recommend_group_data[$group_id]['sort'][$sort_num])){
						parent::set_error($num, '順番が重複しています。順番['.$sort_num. ']　いつものグループコード['.$group_code.'] 商品コード['.$item_code.']');
					}

					$this->recommend_group_data[$group_id]['sort'][$sort_num] = $item_code;
				}
			}
			$num += 1;
		}

		$this->validate_sort_num_max();

		if (!empty($this->get_errors())) {
			Session::set_flash('validate_upload_errors', $this->get_errors());
		}
	}


	/**
	 * 並び順の最大バリデーション
	 */
	private function validate_sort_num_max(){
		$csv_data = $this->get_data();

		foreach($this->recommend_group_data as $group_id => $recommend_group){
			$database_item_codes = array_keys($this->recommend_group_data[$group_id]['database']);
			$csv_item_codes = array_keys($this->recommend_group_data[$group_id]['csv']);
			$item_codes = array_unique(array_merge($database_item_codes, $csv_item_codes));
			$item_count = count($item_codes);
			$this->recommend_group_data[$group_id]['count'] = $item_count;
		}

		$num = 1;
		foreach($csv_data as $line){

			if($line['control_code'] == '1'){ //削除
				continue;
			}

			$group_code = $line['recommended_group_code'];
			$group_id = $this->recommended_groups[$group_code];
			$item_code = $line['item_code'];
			$sort_num = $line['sort_num'];

			if($sort_num > $this->recommend_group_data[$group_id]['count']){
				parent::set_error($num, $group_code.'の並び順は'.$this->recommend_group_data[$group_id]['count'].'以下の数字を入れてください。');
			}

			$num += 1;
		}

	}

	/**
	 * 重複バリデーション
	 */
	private function validate_duplicate_code(){
		$duplicate_check = array();
		$num = 1;

		$csv_data = $this->get_data();
		foreach($csv_data as $line){
			$group_code = $line['recommended_group_code'];
			$item_code = $line['item_code'];
			if(!isset($duplicate_check[$group_code])){
				$duplicate_check[$group_code] = array();
			}

			if(isset($duplicate_check[$group_code][$item_code])){
				parent::set_error($num, 'いつものグループコード['.$group_code. ']と商品コード['.$item_code.']が重複しています。');
			}

			$duplicate_check[$group_code][$item_code] = true;

			$num += 1;
		}

		return empty($this->get_errors());
	}

	/**
	 * @see Upload_Csv_Base::get_csv_format_div()
	 */
	protected function get_csv_format_div() {
		return Config::get('define.csv_format_div.recommended_item');
	}

	/**
	 * @see Upload_Csv_Base::validate()
	 */
	protected function validate(&$data, $num) {
		foreach ($data as $key => $value) {
			switch ($key) {
				case 'recommended_group_code':
					$this->validate_recommended_group_code($value, $num);
					break;
				case 'item_code':
					$this->validate_item_code($value, $num);
					break;
				case 'sort_num':
					$this->validate_sort_num($value, $num);
					break;
				case 'control_code':
					$this->validate_control_code($value, $num);
					break;
			}
		}

		return true;
	}

	/**
	 *  override
	 */
	public function save(){

		DB::start_transaction();
		try{
			foreach($this->recommend_group_data as $group_id => $recommend_group){
				$delete_items = $recommend_group['delete'];
				$natural_sorted_items = $recommend_group['database'];
				$empty_sort_items = $recommend_group['empty_sort_num'];
				$sort_items = $recommend_group['sort'];

				foreach($sort_items as $i => $code){
					unset($natural_sorted_items[$code]);
				}

				foreach($empty_sort_items as $code => $v){
					//既に登録されている場合は現状の並びのままでスキップする。
					if(isset($natural_sorted_items[$code])){
						continue;
					}

					$natural_sorted_items[$code] = array(
						'sort_num' => '',
						'item_code' => $code
					);
				}

				$sorted_items = array();
				$count = $recommend_group['count'];

				for($i = 1; $i <= $count; $i++){
					
					$sort_num = $count - $i + 1;

					if(isset($sort_items[$i])){
						$item = array(
							'item_code' => $sort_items[$i],
							'sort_num' => $sort_num
						);
					} else {
						$item = array_shift($natural_sorted_items);
					}
					$item['sort_num'] = $sort_num;
					$sorted_items[] = $item;
				}

				if(!$this->save_items($group_id, $sorted_items, $delete_items)){
					DB::rollback_transaction();
					return false;
				}
			}

			DB::commit_transaction();
		} catch (Exception $e){
			DB::rollback_transaction();
			return false;
		}

		return true;
	}

	protected function save_items($group_id, $sorted_items, $delete_items){

		foreach($delete_items as $item){
			$recommended_group_id = $group_id;
			$item_code = $item['item_code'];
			if (isset($this->recommended_items[$recommended_group_id][$item_code])) {
				$id = $this->recommended_items[$recommended_group_id][$item_code]['id'];
				if (!$this->delete_recommended_item($id)) {
					return false;
				}
			}
		}

		foreach($sorted_items as $item){
			$recommended_group_id = $group_id;
			$item_code = $item['item_code'];
			$sort_num = $item['sort_num'];

			if (!isset($this->recommended_items[$recommended_group_id][$item_code])) {
				if (!$this->insert_recommended_item($item_code, $recommended_group_id, $sort_num)) {
					return false;
				}
			}else{
				if (!$this->update_recommended_item($item_code, $recommended_group_id, $sort_num)) {
					return false;
				}
			}

		}

		return true;
	}

	protected function save_line($data) {
		return true;
	}


	/**
	 * いつものグループコードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_recommended_group_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, 'いつものグループコードを入力してください');
			return false;
		}
		if (!array_key_exists($value, $this->recommended_groups)) {
			parent::set_error($num, 'いつものグループコードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 商品コードバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_item_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '商品コードを入力してください');
			return false;
		}

		if (!array_key_exists($value, $this->items)) {
			parent::set_error($num, '商品コードが存在しません[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 順番バリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_sort_num($value, $num) {
		if ($value == '') {
			// parent::set_error($num, '順番を入力してください');
			return true;
		}
		if (!is_numeric($value)) {
			parent::set_error($num, '順番は数値で入力してください[' . $value . ']');
			return false;
		}
		if ($value < 0 || $value > 9999999) {
			parent::set_error($num, '順番は0以上、9999999以下で入力してください[' . $value . ']');
			return false;
		}
		return true;
	}

	/**
	 * 制御フラグバリデート
	 *
	 * @param string $value 値
	 * @param int $num 行番号
	 */
	private function validate_control_code($value, $num) {
		if ($value == '') {
			parent::set_error($num, '制御コードを入力してください');
			return false;
		}
		if ($value != '0' && $value != '1') {
			parent::set_error($num, '制御コードは0、または、1で入力してください[' .$value .']');
			return false;
		}
		return true;
	}

	/**
	 * いつものグループコードリストを取得する
	 */
	private function list_recommended_group_code() {
		return DB::select('code', 'id')
			->from('recommended_groups')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code', 'id');
	}

	/**
	 * 商品コードリストを取得する
	 */
	private function list_item_code() {
		return DB::select('code')
			->from('items')
			->where('del_flg', UNDELETED)
			->order_by('code', 'asc')
			->execute()
			->as_array('code');
	}

	/**
	 * いつもの商品リストを取得する
	 */
	private function list_recommended_item() {
		$recommended_items = DB::select(array('recommended_items.id', 'id'), 'item_code', 'recommended_group_id', 'sort_num')
			->from('recommended_items')
			->join('items', 'INNER')
				->on('recommended_items.item_code', '=', 'items.code')
				->and_on('items.del_flg', '=', DB::expr(UNDELETED))
			->where('recommended_items.del_flg', UNDELETED)
			->order_by('recommended_group_id', 'ASC')
			->order_by('sort_num', 'DESC')
			->execute()
			->as_array();

		$results = array();
		foreach ($recommended_items as $recommended_item) {
			if (!isset($results[$recommended_item['item_code']])) {
				$results[$recommended_item['item_code']] = array();
			}
			$results[$recommended_item['recommended_group_id']][$recommended_item['item_code']] = $recommended_item;
		}
		return $results;
	}

	/**
	 * いつもの商品を登録する
	 *
	 * @param string $item_code 商品コード
	 * @param int $recommended_group_id いつものグループID
	 * @param int $sort_num 順番
	 */
	private function insert_recommended_item($item_code, $recommended_group_id, $sort_num) {
		$values = array();
		$values['item_code'] = $item_code;
		$values['recommended_group_id'] = $recommended_group_id;
		$values['sort_num'] = $sort_num;

		$model = \Model_Recommended_Item::forge($values);

		return $model->save() !== false;
	}

	/**
	 * いつもの商品を更新する
	 *
	 * @param string $item_code 商品コード
	 * @param int $recommended_group_id いつものグループID
	 * @param int $sort_num 順番
	 */
	private function update_recommended_item($item_code, $recommended_group_id, $sort_num) {

		$recommended_item = $this->recommended_items[$recommended_group_id][$item_code];

		if ($recommended_item['sort_num'] == $sort_num){
			return true;
		}
		$values = array();
		$values['sort_num'] = $sort_num;

		$query = DB::update('recommended_items')
			   ->value('sort_num', $sort_num)
			   ->where('id', '=', $recommended_item['id']);


		return $query->execute() !== false;
	}

	/**
	 * いつもの商品を削除する
	 *
	 * @param int $id いつもの商品ID
	 */
	private function delete_recommended_item($id) {
		return DB::update('recommended_items')
			->value('del_flg', DELETED)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('id', $id)
			->execute();
	}
}
