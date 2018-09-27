<?php
/**
 * 配達曜日モデルクラス
 */
class Model_Delivery_Week extends Model_Base {

	protected static $_properties = array(
		'id',
		'code',
		'delivery_flg_mon',
		'delivery_flg_tue',
		'delivery_flg_wed',
		'delivery_flg_thu',
		'delivery_flg_fri',
		'delivery_flg_sat',
		'delivery_flg_sun',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('code')
	);

}