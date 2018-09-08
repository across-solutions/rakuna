<?php
/**
 * 割当価格モデルクラス
 */
class Model_Price_Assign extends \Model_Base {

	/**
	 * フィールドリスト
	 */
	protected static $_properties = array(
		'id',
		'item_code',
		'member_code',
		'price',
		'recommend',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}