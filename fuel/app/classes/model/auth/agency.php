<?php
/**
 * 代理発注認証モデルクラス
 */
class Model_Auth_Agency extends Model_Base {

	protected static $_properties = array(
		'id',
		'sales_representative_id',
		'member_code',
		'auth_key',
		'del_flg',
		'created',
		'updated'
	);
}