<?php
/**
 * お知らせ既読モデルクラス
 */
class Model_Notice_Read extends Model_Base {
	
	protected static $_properties = array(
		'id',
		'member_id',
		'notice_id',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}