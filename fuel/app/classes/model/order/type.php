<?php
/**
 * 発注タイプモデルクラス
 */
class Model_Order_Type extends \Model_Base {

	protected static $_properties = array(
		'id',
		'name',
		'code',
		'warehouse_code',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('name', 'code', 'warehouse_code')
	);
}