<?php
/**
 * 割当商品モデルクラス
 */
class Model_Item_Assign extends \Model_Base {

	/**
	 * フィールドリスト
	 */
	protected static $_properties = array(
		'id',
		'item_code',
		'member_id',
		'renewal_datetime',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_belongs_to = array(
		'items' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'key_from' => 'item_code',
			'key_to' => 'code',
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}