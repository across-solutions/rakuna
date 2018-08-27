<?php
/**
 * 受注ダウンロード履歴モデルクラス
 */
class Model_Order_Download extends Model_Base {
	
	protected static $_properties = array(
		'id',
		'user_id',
		'download_datetime',
		'record_count',
		'del_flg',
		'update_user_id',
		'created',
		'updated'
	);
}