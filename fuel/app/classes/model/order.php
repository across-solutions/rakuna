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
		'order_datetime',
		'amount',
		'amount_case',
		'payment',
		'payment_tax',
		'tax',
		'tax_rate',
		'delivery_date',
		'cancel_flg',
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