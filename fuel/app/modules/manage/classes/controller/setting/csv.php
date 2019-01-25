<?php
namespace Manage;
use Fuel\Core\Response;
use Fuel\Core\Config;
use Fuel\Core\DB;
use Fuel\Core\Input;
/**
 * CSV設定コントロールクラス
 */
class Controller_Setting_Csv extends Controller_Base {

	/**
	 * ページタイトル
	 */
	protected $title = 'CSV設定';

	/**
	 * CSV設定画面-初期表示
	 */
	public function action_index() {
		Response::redirect('/manage/setting/csv/edit/' . Config::get('define.csv_format_div.order'));
	}

	/**
	 * CSV設定画面-編集初期表示
	 *
	 * @param int $div CSVフォーマット区分
	 */
	public function action_edit($div) {
		$formats = $this->get_csv_formats($div);
		if (empty($formats)) {
			throw new \Exception_403();
		}

		$templates = $this->get_csv_format_templates_diff($div);
		if (empty($formats)) {
			throw new \HttpServerErrorException();
		}

		$data = array();
		$data['div'] = $div;
		$data['formats'] = $formats;
		$data['templates'] = $templates;

		$this->render($data);
	}

	/**
	 * CSV設定画面-保存処理
	 */
	public function action_edit_save() {
		$data = Input::post();
		if (!isset($data['div']) || empty($data['div'])) {
			throw new \Exception_403();
		}
		if (!isset($data['sort']) || empty($data['sort'])) {
			throw new \Exception_403();
		}
		if (!isset($data['column']) || empty($data['column'])) {
			throw new \Exception_403();
		}
		$div = $data['div'];

		$templates = $this->get_csv_format_templates($div);
		if (empty($templates)) {
			throw new \HttpServerErrorException();
		}

		if (!$this->validate_edit($data, $templates)) {
			Response::redirect('/manage/setting/csv/edit/' . $div);
		}

		if (!$this->edit_format($div, $data, $templates)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('更新しました');
		Response::redirect('/manage/setting/csv/edit/' . $div);
	}

	/**
	 * CSV設定画面-MOS標準フォーマットに戻す処理
	 */
	public function action_default() {
		$data = Input::post();
		if (!isset($data['div']) || empty($data['div'])) {
			throw new \Exception_403();
		}
		$div = $data['div'];

		$templates = $this->get_csv_format_templates_array_by_sort($div);
		if (empty($templates)) {
			throw new \HttpServerErrorException();
		}

		if(!$this->default_format($div, $templates)) {
			throw new \HttpServerErrorException();
		}

		$this->set_info_message('MOS標準フォーマットに更新しました');
		Response::redirect('/manage/setting/csv/edit/' . $div);
	}

	/**
	 * 更新バリデート
	 *
	 * @param array $data フォームデータ
	 * @param array $templates テンプレートリスト
	 */
	private function validate_edit($data, $templates) {
		$sorts = $data['sort'];
		$columns = $data['column'];

		if (count($sorts) != count($columns)) {
			$this->set_error_message('項目数が一致しません');
			return false;
		}

		foreach ($templates as $template) {
			$pos = array_search($template->key, $sorts);
			if ($pos !== false) {
				unset($sorts[$pos]);
			} else {
				if ($template->required) {
					$this->set_error_message('必須項目[' . $template->key . ']が見つかりません');
					return false;
				}
			}
		}
		if (!empty($sorts)) {
			foreach ($sorts as $sort) {
				if ($sort != 'empty') {
					$this->set_error_message('不正な項目[' . $sort . ']が含まれています');
					return false;
				}
			}
		}

		$error = false;
		foreach ($columns as $column) {
			if (mb_strlen($column) > 20) {
				$this->set_error_message('20文字以内で入力してください[' . $column . ']');
				$error = true;
			}
		}
		if ($error) {
			return false;
		}

		return true;
	}

	/**
	 * 更新処理
	 *
	 * @param int $div CSVフォーマット区分
	 * @param array $data フォームデータ
	 * @param array $templates テンプレートリスト
	 */
	private function edit_format($div, $data, $templates) {
		DB::start_transaction();
		try {
			if (!$this->delete_csv_format($div)) {
				DB::rollback_transaction();
				return false;
			}

			$columns = $data['column'];
			foreach ($data['sort'] as $sort => $key) {
				if ($key == 'empty') {
					if (!$this->insert_empty_csv_format($sort + 1, $div, $columns[$sort])) {
						DB::rollback_transaction();
						return false;
					}
					continue;
				}

				if (!$this->insert_csv_format($sort + 1, $templates[$key], $columns[$sort])) {
					DB::rollback_transaction();
					return false;
				}
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * MOS標準フォーマットに戻す処理
	 *
	 * @param int $div CSVフォーマット区分
	 * @param array $templates テンプレートリスト
	 */
	private function default_format($div, $templates) {
		DB::start_transaction();
		try {
			if (!$this->delete_csv_format($div)) {
				DB::rollback_transaction();
				return false;
			}

			foreach ($templates as $sort => $template) {
				if (!$this->insert_csv_format($sort, $template, $template->name)) {
					DB::rollback_transaction();
					return false;
				}
			}

			DB::commit_transaction();
		} catch (\Exception $e) {
			DB::rollback_transaction();
			throw $e;
		}
		return true;
	}

	/**
	 * CSVフォーマットを取得する
	 *
	 * @param int $div CSVフォーマット区分
	 */
	private function get_csv_formats($div) {
		return \Model_Csv_Format::query()
			->where('div', $div)
			->order_by('sort', 'asc')
			->get();
	}

	/**
	 * CSVフォーマットテンプレートを取得する
	 *
	 * @param int $div CSVフォーマット区分
	 */
	private function get_csv_format_templates($div) {
		$templates = \Model_Csv_Format_Template::query()
			->where('div', '=', $div)
			->order_by('sort', 'asc')
			->get();

		$results = array();
		foreach ($templates as $template) {
			$results[$template->key] = $template;
		}
		return $results;
	}

	/**
	 * sortをキーにしたCSVフォーマットテンプレートを取得する
	 *
	 * @param int $div CSVフォーマット区分
	 */
	private function get_csv_format_templates_array_by_sort($div) {

		$templates = \Model_Csv_Format_Template::query()
			->where('div', '=', $div)
			->order_by('sort', 'asc')
			->get();

		$results = array();
		foreach ($templates as $template) {
			$results[$template->sort] = $template;
		}
		return $results;
	}

	/**
	 * CSVフォーマットに存在しないCSVフォーマットテンプレートを取得する
	 *
	 * @param int $div CSVフォーマット区分
	 */
	private function get_csv_format_templates_diff($div) {
		return DB::select()
			->from('csv_format_templates')
			->where(DB::expr('not exists (SELECT 1 FROM csv_formats WHERE csv_format_templates.div = csv_formats.div
					AND csv_format_templates.key = csv_formats.key AND csv_formats.del_flg = ' . DB::expr(UNDELETED) . ')'))
			->where('del_flg', '=', DB::expr(UNDELETED))
			->where('div', $div)
			->order_by('sort', 'asc')
			->execute()->as_array();
	}

	/**
	 * CSVフォーマットを登録する
	 *
	 * @param int $sort 並び順
	 * @param Model_Csv_Format_Template CSVフォーマットテンプレート
	 * @param string $name 項目名
	 */
	private function insert_csv_format($sort, $template, $name) {
		$fields = array('div', 'key', 'required');
		$values = \Common_Util::filter($template, $fields);

		$values['name'] = $name;
		$values['sort'] = $sort;
		$values['empty_flg'] = false;

		$model = \Model_Csv_Format::forge($values);

		return $model->save() !== false;
	}

	/**
	 * 空行のCSVフォーマットを登録する
	 *
	 * @param int $sort 並び順
	 * @param int $div CSVフォーマット区分
	 * @param string $name 項目名
	 */
	private function insert_empty_csv_format($sort, $div, $name) {
		$values = array();
		$values['div'] = $div;
		$values['key'] = 'empty';
		$values['name'] = $name;
		$values['required'] = false;
		$values['sort'] = $sort;
		$values['empty_flg'] = true;

		$model = \Model_Csv_Format::forge($values);

		return $model->save() !== false;
	}

	/**
	 * CSVフォーマットを削除する
	 *
	 * @param int $div CSVフォーマット区分
	 */
	private function delete_csv_format($div) {
		return DB::update('csv_formats')
			->value('del_flg', DELETED)
			->value('update_user_id', $this->get_user_id())
			->value('updated', date('Y-m-d H:i:s'))
			->where('div', '=', $div)
			->execute();
	}
}