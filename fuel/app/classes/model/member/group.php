<?php
/**
 * 発注者グループモデルクラス
 */
class Model_Member_Group extends Model_Base {

	protected static $_properties = array(
		'id',
		'code',
		'name',
		'search_field',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);

	protected static $_search_fields = array(
		'search_field' => array('code', 'name')
	);

}