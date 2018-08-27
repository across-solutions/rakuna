<?php
use Orm\Model;
use Auth\Auth;

/**
 * 基底モデルクラス
 */
class Model_Base extends Model {

	protected static $_search_fields = array();

	protected static $_observers = array(
		'Orm\Observer_Self' => array(
			'events' => array('before_insert', 'before_save')
		),
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
			'property' => 'created'
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
			'property' => 'updated'
		)
	);

	/**
	 * クエリ
	 * @param $options
	 */
	public static function query($options = array()) {
		$query = parent::query($options);
		$query->where('del_flg', 0);

		return $query;
	}

	/**
	 * セレクトボックス用データ配列を取得する
	 * @param $key キー
	 * @param $value 値
	 * @param $orders 並び順
	 * @param $empty 未選択時
	 */
	public static function list_select($key, $value, $orders, $empty = '') {
		$query = self::query()->select($key, $value);
		foreach ($orders as $field => $direction) {
			$query->order_by($field, $direction);
		}
		$results = $query->get();

		$list = array();
		if (!is_null($empty) && $empty !== false) {
			$list[''] = $empty;
		}
		foreach($results as $result) {
			$list[$result->{$key}] = $result->{$value};
		}
		return $list;
	}

	/**
	 * 存在チェック
	 * @param $value 値
	 * @param $key キー名
	 */
	public static function exists($value, $key = 'id') {
		return self::query()->where($key, '=', $value)->count() > 0;
	}

	/**
	 * 論理削除
	 * @param $cascade
	 * @param $use_transaction
	 */
	public function soft_delete($cascade = null, $use_transaction = false) {
		$this->del_flg = 1;
		return parent::save($cascade, $use_transaction);
	}

	/**
	 * 登録前処理
	 */
	public function _event_before_insert() {

		$this->replace_from_empty_with_null();

		if (!is_null($this->property('del_flg')) && is_null($this->del_flg)) {
			$this->del_flg = 0;
		}
		if (!is_null($this->property('update_user_id'))) {
			$this->update_user_id = Auth::get_user_id()[1];
		}
	}

	/**
	 * 保存前処理
	 */
	public function _event_before_save() {

		$this->replace_from_empty_with_null();

		foreach (static::$_search_fields as $field => $columns) {
			if (!is_null($this->property($field))) {
				$values = array();
				foreach ($columns as $column) {
					if (!is_null($this->property($column))) {
						$values[] = Common_Util::mb_convert($this->{$column});
					}
				}
				$this->{$field} = implode(' ', $values);
			}
		}

		if (!is_null($this->property('update_user_id'))) {
			$this->update_user_id = Auth::get_user_id()[1];
		}
	}

	/**
	 * 空項目にNULLを代入
	 * MySQL5.6.5 Or Above 対応
	 */
	private function replace_from_empty_with_null() {

		switch (true) {
			case $this instanceof Model_Member:
				$fields = array(
					'member_group_id',
				);
				break;

			case $this instanceof Model_Item:
				$fields = array(
					'item_category_id',
					'price',
					'price_case',
				);
				break;

			case $this instanceof Model_Order_Detail:
				$fields = array(
					'amount',
					'amount_case',
					'price',
					'price_tax',
					'price_case',
					'price_case_tax',
					'total',
					'total_tax'
				);
				break;

			default:
				$fields = array();
				break;
		}

		foreach ($fields as $field) {
			if ($this->$field === '') {
				$this->$field = null;
			}
		}
	}
}