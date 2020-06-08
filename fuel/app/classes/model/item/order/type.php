<?php
/**
 * 商品発注タイプモデルクラス
 */
class Model_Item_Order_Type extends \Model_Base {

	/**
	 * フィールドリスト
	 */
	protected static $_properties = array(
		'id',
		'item_code',
		'member_id',
		'order_type',
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
		),
		'members' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		),
	);
}