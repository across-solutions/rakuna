<?php
namespace Manage;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * 発注者アカウント管理一覧プレゼンタクラス
 */
class Presenter_Member_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::view()
	 */
	public function view() {
		parent::view();

		$this->is_id_mail_sent = function($row) {
			return $this->is_id_mail_sent($row);
		};

	}

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {
		$query = \Model_Member::query();
		$this->add_condition($query, $data);

		return $query->count();
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {
		$query = \Model_Member::query();
		$this->add_condition($query, $data);
		$query->order_by('code', 'asc');

		return $query->limit($limit)->offset($offset)->get();
	}

	/**
	 * 検索条件を付与する
	 * @param $query Query
	 * @param $data 検索条件
	 */
	private function add_condition(&$query, $data) {

		// フリーワード
		$search_field = Arr::get($data, 'search_field');
		if (!is_null($search_field) && trim($search_field) != '') {
			$search_field = \Common_Util::mb_convert($search_field);
			$values = \Common_Util::split_space($search_field);
			foreach ($values as $value) {
				$query->where('search_field', 'LIKE', '%' . trim($value) . '%');
			}
		}

		// ID・パスワード通知メール未送信ユーザを抽出
		$not_send_mail = Arr::get($data, 'not_send_mail');
		if ( !is_null($not_send_mail) && trim($not_send_mail) == '1' ) {
			$query->where_open();
			$query->where('id_mail_sent_flg', '=', DB::expr(0) );
			$query->or_where('id_mail_sent_flg', '=', NULL );
			$query->or_where('id_mail_sent_flg', '=', "" );
			$query->where_close();
		}

	}

	/**
	 * IDパスワードメール送信済みかどうか
	 *
	 * @param array $row 行データ
	 */
	private function is_id_mail_sent($row) {
		$id_mail_sent_flg = Arr::get($row, 'id_mail_sent_flg');
		return !is_null($id_mail_sent_flg) && $id_mail_sent_flg == '1';
	}

}