<?php
namespace Manage;
use Fuel\Core\Response;
use Fuel\Core\File;
use Fuel\Core\Input;
use Fuel\Core\Arr;
/**
 * ダウンロード履歴コントローラクラス
 */
class Controller_History_Download extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = 'ダウンロード履歴';
	
	/**
	 * ダウンロード履歴画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}
	
	/**
	 * ダウンロード履歴画面-ダウンロード処理
	 * 
	 * @param int $id 受注ダウンロード履歴ID
	 */
	public function action_download($id) {
		$file = ORDER_CSV_PATH . $id . '.csv';
		if (!File::exists($file)) {
			$this->set_error_message('ファイルが存在しません');
			$this->render(null, 'history/download/index');
			return;
		}
		File::download($file, 'order.csv');
	}
}