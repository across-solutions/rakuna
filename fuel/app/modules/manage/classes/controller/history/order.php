<?php
namespace Manage;

use Fuel\Core\Input;
/**
 * 受注履歴管理コントローラクラス
 */
class Controller_History_Order extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('download');

	/**
	 * ページタイトル
	 */
	protected $title = '受注履歴';

	/**
	 * 初期表示
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 * CSVダウンロード画面-初期表示
	 */
	public function action_download() {
		$this->render();
	}

	/**
	 * CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_save() {
		$csv = new \Download_Csv_History();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_ORDER_HISTORY, $data, "\t", null, array());
	}
}