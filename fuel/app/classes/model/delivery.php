<?php
/**
 * 納品先モデルクラス
 */
class Model_Delivery extends Model_Base {

	protected static $_properties = array(
		'id',
		'member_code',
		'code',
		'sales_person_code',
		'name',
		'name_kana',
		'receiver_name1',
		'receiver_name2',
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
		'comment',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('member_code', 'code', 'name', 'name_kana', 'receiver_name1', 'receiver_name2')
	);

	protected static $_belongs_to = array(
		'members' => array(
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