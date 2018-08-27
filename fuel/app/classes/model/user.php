<?php
/**
 * 管理者アカウントモデルクラス
 */
class Model_User extends Model_Base {

	protected static $_properties = array(
		'id',
		'corporation_name',
		'name',
		'username',
		'password',
		'mosgroup',
		'last_login',
		'login_hash',
		'status',
		'auto_login_key',
		'auto_login_updatetime',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}