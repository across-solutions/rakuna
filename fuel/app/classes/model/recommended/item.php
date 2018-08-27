<?php
/**
 * いつもの商品モデルクラス
 */
class Model_Recommended_Item extends \Model_Base {

	protected static $_properties = array(
		'id',
		'recommended_group_id',
		'item_code',
		'sort_num',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('item_code')
	);

	protected static $_belongs_to = array(
		'recommended_groups' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		),
		'item' => array(
			'key_from' => 'item_code',
			'key_to' => 'code',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);

}