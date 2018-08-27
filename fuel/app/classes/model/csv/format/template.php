<?php
/**
 * CSVフォーマットテンプレートモデルクラス
 */
class Model_Csv_Format_Template extends Model_Base {
	
	protected static $_properties = array(
		'id',
		'div',
		'key',
		'name',
		'required',
		'sort',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}