<?php
/**
 * 発注頻度モデルクラス
 */
class Model_Order_Frequency extends Model_Base {
	
	protected static $_properties = array(
		'id',
		'member_id',
		'item_code',
		'frequency',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}