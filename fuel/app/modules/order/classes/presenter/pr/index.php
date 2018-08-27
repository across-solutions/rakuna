<?php
namespace Order;
/**
 * PR商品一覧プレゼンタクラス
 */
class Presenter_Pr_Index extends Presenter_Item_Index {
	
	/**
	 * @see \Order\Presenter_Item_Index::add_condition()
	 */
	protected function add_condition(&$query, $data) {
		parent::add_condition($query, $data);
		$query->where('items.pr_flg', '=', true);
	}
}