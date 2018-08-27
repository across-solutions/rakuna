<?php
/**
 * お気に入りモデルクラス
 */
class Model_Favorite extends Model_Base {

	protected static $_properties = array(
		'id',
		'member_id',
		'item_code',
		'sort_num',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}