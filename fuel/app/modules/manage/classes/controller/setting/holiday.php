<?php
namespace Manage;

use Fuel\Core\Validation;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Pagination;
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\DB;
use Fuel\Core\Arr;
/**
 * 非営業日コントローラクラス
 */
class Controller_Setting_Holiday extends Controller_Base {

	/**
	 * ダイアログリスト
	 */
	protected $dialogs = array('upload_csv', 'upload_csv_save', 'download_csv');

	/**
	 * ページタイトル
	 */
	protected $title = '非営業日設定';

	/**
	 * 一覧画面-初期表示・検索
	 */
	public function action_index() {
		$this->render();
	}

	/**
	 *  一覧画面-保存処理
	 */
	public function action_save() {

		$data = Input::post();

		if (!$this->validate_save($data)){
			$this->set_error_message('不正な値が含まれています');
			$this->render($data, 'setting/holiday/index');
			return;
		}

		if (!$this->save_holidays($data)) {
			$this->set_error_message('登録に失敗しました');
			$this->render($data, 'setting/holiday/index');
			return;
		}

		$this->set_info_message('保存しました');
		Response::redirect('/manage/setting/holiday?'.\Uri::build_query_string(
			array('year' => Arr::get($data, 'year'))
		));
	}

	/**
	 * CSVアップロード画面-初期表示
	 */
	public function action_upload_csv() {
		$this->render();
	}

	/**
	 * CSVアップロード画面-アップロード処理
	 */
	public function action_upload_csv_save() {
		$this->process_upload('holiday_csv');

		if (!$this->validate_csv_upload()) {
			$this->render(null, 'setting/holiday/upload_csv');
			return;
		}

		$csv = new \Upload_Csv_Holiday($this->get_upload_file('holiday_csv'));
		$csv->parse();
		if ($csv->has_error()) {
			$this->render(null, 'setting/holiday/upload_csv');
			return;
		}

		if (!$csv->save()) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('登録しました');
		Response::redirect('/manage/dialog/complete');
	}

	/**
	 * CSVダウンロード画面-初期表示
	 */
	public function action_download_csv() {
		$this->render();
	}

	/**
	 * CSVダウンロード画面-CSVダウンロード処理
	 */
	public function action_download_csv_save() {
		$csv = new \Download_Csv_Holiday();
		$data = $csv->get_csv_data(Input::get(), true);

		return $this->csv_download(FILE_NAME_DOWNLOAD_HOLIDAY, $data);
	}

	/**
	 * CSVアップロードバリデート
	 */
	private function validate_csv_upload() {
		return $this->validate_file_upload('holiday_csv', true);
	}

	/**
	 * 保存バリデート
	 *
	 * @param $data フォームデータ
	 */
	private function validate_save($data){
		$validation = $this->create_validation();

		$validation->add('year', '年')
				   ->add_rule('required')
				   ->add_rule('numeric');

		$calendar = Arr::get($data, 'calendar');
		foreach ((array)$calendar as $key => $val) {
			$validation->add('calendar.' . $key, '日付')
					   ->add_rule('valid_date', 'Y-m-d');
		}

		return $this->validate($validation, $data);
	}

	/**
	 * 保存処理
	 *
	 * @param $data フォームデータ
	 */
	private function save_holidays($data) {
		try {
			DB::start_transaction();

			$year = Arr::get($data, 'year');

			$this->delete_holidays($year);

			if(!$this->insert_holidays($data)){
				DB::rollback_transaction();
				return false;
			}
			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
			return false;
		}

		return true;
	}

	/**
	 * 非営業日の削除
	 *
	 * @param  int   $year 対象年
	 *
	 */
	private function delete_holidays($year){
		$start_of_year = new \DateTime($year.'-01-01');
		$end_of_year = clone $start_of_year;
		$end_of_year->add(new \DateInterval('P1Y'));
		$end_of_year->sub(new \DateInterval('P1D'));

		return DB::delete('holidays')
			->where('date', '>=', $start_of_year->format('Y-m-d'))
			->where('date', '<=', $end_of_year->format('Y-m-d'))
			->execute();

	}

	/**
	 * 非営業日の登録
	 *
	 *　@param array $data フォームデータ
	 */
	private function insert_holidays($data){
		$calendar = Arr::get($data, 'calendar');

		if(is_null($calendar)){
			return true;
		}

		foreach ($calendar as $val) {

			$values = array(
				'date' => $val,
			);

			$model = \Model_Holiday::forge($values);

			if (($model->save() !== false) === false) {
				return false;
			}
		}

		return true;
	}

}