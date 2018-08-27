<?php
/**
 * メールテンプレートモデルクラス
 */
class Model_Template_Mail extends Model_Base {
	
	protected static $_properties = array(
		'id',
		'mail_div',
		'mail_from',
		'title',
		'message',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}