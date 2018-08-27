<?php
/**
 * いつものグループ割当モデルクラス
 */
class Model_Recommended_Group_Assign extends \Model_Base {
	
	/**
	 * フィールドリスト
	 */
	protected static $_properties = array(
		'id',
		'recommended_group_id',
		'member_id',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}