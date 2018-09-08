<?php
/**
 * 営業担当アカウントモデルクラス
 */
class Model_Sales_Representative extends Model_Base {

	protected static $_properties = array(
		'id',
		'sales_person_code',
		'sales_person_name',
		'username',
		'password',
		'auto_login_key',
		'auto_login_updatetime',
		'status',
		'search_field',
		'last_login',
		'login_hash',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('sales_section_code', 'sales_person_code', 'sales_person_name', 'username')
	);

	protected static $_has_one = array(
		'members' => array(
			'model_to' => 'Model_Member',
			'cascade_save' => false,
			'cascade_delete' => false,
			'key_from' => 'member_code',
			'key_to' => 'code',
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}