<?php
/**
 * カテゴリモデルクラス
 */
class Model_Item_Category extends \Model_Base {
	
	protected static $_properties = array(
		'id',
		'code',
		'name',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('code', 'name')
	);
	
	protected static $_has_many = array(
		'items' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}