<?php
/**
 * 受注モデルクラス
 */
class Model_Order extends Model_Base {

	protected static $_properties = array(
		'id',
		'member_id',
		'member_code',
		'member_name',
		'member_email',
		'sales_person_code',
		'sales_person_name',
		'order_datetime',
		'amount',
		'amount_case',
		'payment',
		'payment_tax',
		'tax',
		'tax_rate',
		'delivery_kind',
		'delivery_code',
		'delivery_name',
		'delivery_receiver_name1',
		'delivery_receiver_name2',
		'delivery_zip',
		'delivery_address1',
		'delivery_address2',
		'delivery_address3',
		'delivery_tel',
		'delivery_fax',
		'delivery_date',
		'cancel_flg',
		'agency_order_flg',
		'order_download_id',
		'comment',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('member_code', 'member_name', 'member_email')
	);

	protected static $_has_many = array(
		'order_details' => array(
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where' => array(array('del_flg', '=', '0'))
			)
		)
	);
}