<?php
/**
 * 休日モデルクラス
 */
class Model_Holiday extends \Model_Base {

	protected static $_properties = array(
		'id',
		'date',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('date', 'name')
	);
}