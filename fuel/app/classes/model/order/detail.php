<?php
/**
 * 受注明細モデルクラス
 */
class Model_Order_Detail extends Model_Base {

	protected static $_properties = array(
		'id',
		'order_id',
		'category_code',
		'category_name',
		'item_id',
		'item_code',
		'item_name',
		'item_unit_name',
		'item_unit_name_case',
		'item_smile_unit_name',
		'item_size',
		'item_size_case',
		'item_type',
		'jan_code',
		'price',
		'price_tax',
		'amount',
		'price_case',
		'price_case_tax',
		'amount_case',
		'total',
		'total_tax',
		'cost',
		'total_cost',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('item_code', 'item_name')
	);

	protected static $_belongs_to = array(
		'orders' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}