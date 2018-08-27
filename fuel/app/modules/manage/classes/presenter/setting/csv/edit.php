<?php
namespace Manage;

/**
 * CSV設定編集プレゼンタクラス
 */
class Presenter_Setting_Csv_Edit extends \Presenter_Base {
	
	/**
	 * @see Presenter_Base::before()
	 */
	public function before() {
		parent::before();
		
		$this->get_class = function($format) {
			if ($format->key == 'empty') {
				return 'empty_field';
			}
			return $format->required ? 'no' : '';
		};
	}
}