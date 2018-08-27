<?php
/**
 * システム設定モデルクラス
 */
class Model_Setting extends Model_Base {

	protected static $_properties = array(
		'id',
		'tax_rate',
		'tax_rounding',
		'pr_title',
		'pr_image_name',
		'corporation_name',
		'administrator_name',
		'item_num',
		'price_flg',
		'case_flg',
		'maintenance_flg',
		'created',
		'updated'
	);
}