<?php
use Fuel\Core\Config;
/**
 * CSVダウンロード基底クラス
 */
abstract class Download_Csv_Base {
	
	/**
	 * CSVフォーマット区分を返す
	 */
	abstract protected function get_format_div();
	
	/**
	 * CSVデータを返す
	 * 
	 * @param $params 抽出条件
	 */
	abstract protected function get_data($params);
	
	/**
	 * CSV出力用データを取得する
	 * 
	 * @param $params 抽出条件
	 * @param $header ヘッダ出力
	 */
	public function get_csv_data($params = array(), $header = false) {
		$rows = $this->get_data($params);
		$formats = $this->get_csv_format($this->get_format_div());
		
		$list = array();
		
		if ($header) {
			$list[] = $this->get_header($formats);
		}
		
		$counter = 0;
		foreach ($rows as $row) {
			$counter++;

			$data = array();
			foreach ($formats as $format) {
				$data[] = $this->modifier($counter, $row, $format->key);
			}
			$list[] = $data;
		}
		return $list;
	}
	
	/**
	 * データを加工する
	 * 
	 * @param int $counter 行番号
	 * @param array $data 行データ配列
	 * @param string $key フィールド名
	 */
	protected function modifier($counter, $data, $key) {
		return Arr::get($data, $key);
	}
	
	/**
	 * ヘッダデータを取得する
	 * 
	 * @param array $formats CSVフォーマット
	 */
	private function get_header($formats) {
		$data = array();
		foreach ($formats as $format) {
			$data[] = $format->name;
		}
		return $data;
	}
	
	/**
	 * CSVフォーマットを取得する
	 * 
	 * @param $div CSVフォーマット区分
	 */
	private function get_csv_format($div) {
		return Model_Csv_Format::query()
			->where('div', '=', $div)
			->order_by('sort', 'asc')
			->get();
	}
}