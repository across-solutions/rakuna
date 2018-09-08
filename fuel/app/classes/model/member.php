<?php
/**
 * 発注者アカウントモデルクラス
 */
class Model_Member extends Model_Base {

	protected static $_properties = array(
		'id',
		'member_group_id',
		'sales_person_code',
		'code',
		'name',
		'corporation',
		'store',
		'zip',
		'address1',
		'address2',
		'address3',
		'tel',
		'fax',
		'delivery_flg_mon',
		'delivery_flg_tue',
		'delivery_flg_wed',
		'delivery_flg_thu',
		'delivery_flg_fri',
		'delivery_flg_sat',
		'delivery_flg_sun',
		'email',
		'sub_email',
		'username',
		'password',
		'id_mail_sent_flg',
		'last_login',
		'login_hash',
		'qr_key',
		'auto_login_key',
		'auto_login_updatetime',
		'status',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('code', 'name')
	);

	protected static $_belongs_to = array(
		'member_groups' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		),
		'sales_representatives' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'key_from' => 'sales_person_code',
			'key_to' => 'sales_person_code',
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);

}