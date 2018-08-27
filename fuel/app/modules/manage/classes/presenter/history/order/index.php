<?php
namespace Manage;
/**
 * 受注履歴一覧プレゼンタクラス
 */
class Presenter_History_Order_Index extends Presenter_Order_Index {
	
	/**
	 * 固定条件を付与する
	 *
	 * @param Query $query Query
	 */
	protected function add_default_condition(&$query) {
		$query->where('order_download_id', '!=', null);
	}
}