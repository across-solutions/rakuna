<?php
/**
 * 商品モデルクラス
 */
class Model_Item extends \Model_Base {

	protected static $_properties = array(
		'id',
		'item_category_id',
		'code',
		'name',
		'yomigana',
		'unit_name',
		'unit_name_case',
		'size',
		'size_case',
		'type',
		'comment',
		'jan_code',
		'price',
		'price_case',
		'cost',
		'hidden_flg',
		'pr_flg',
		'renewal_datetime',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('name', 'yomigana', 'comment')
	);

	protected static $_belongs_to = array(
		'item_categories' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);

	protected static $_has_many = array(
		'item_assigns' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'key_from' => 'code',
			'key_to' => 'item_code',
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}