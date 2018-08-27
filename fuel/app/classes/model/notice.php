<?php
/**
 * お知らせモデルクラス
 */
class Model_Notice extends Model_Base {

	protected static $_properties = array(
		'id',
		'member_group_id',
		'title',
		'message',
		'entry_datetime',
		'item_code',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('title', 'message', 'item_code')
	);

	protected static $_belongs_to = array(
		'member_groups' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}