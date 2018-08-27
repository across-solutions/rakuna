<?php
namespace Order;
use Auth\Auth;
/**
 * [発注]お知らせコントローラクラス
 */
class Controller_Notice extends Controller_Base {
	
	/**
	 * ページタイトル
	 */
	protected $title = 'お知らせ';
	
	/**
	 * 一覧画面-初期表示
	 */
	public function action_index() {
		$this->render();
	}
	
	/**
	 * 詳細画面-初期表示
	 * 
	 * @param int $id お知らせID
	 */
	public function action_detail($id) {
		$notice = \Model_Notice::find($id);
		if (empty($notice)) {
			throw new \HttpNotFoundException();
		}

		$member_id = $this->get_member_id();
		if (!$this->readed($member_id, $id)) {
			if (!$this->insert_notice_read($member_id, $id)) {
				throw new \HttpServerErrorException();
			}
		}
		
		$this->render($notice);
	}
	
	/**
	 * 既読か否か返す
	 * 
	 * @param int $member_id 発注者アカウントID
	 * @param int $notice_id お知らせID
	 */
	private function readed($member_id, $notice_id) {
		$count = \Model_Notice_Read::query()
			->where('member_id', '=', $member_id)
			->where('notice_id', '=', $notice_id)
			->count();
		
		return $count > 0;
	}
	
	/**
	 * お知らせ既読を登録する
	 * 
	 * @param int $member_id 発注者アカウントID
	 * @param int $notice_id お知らせID
	 */
	private function insert_notice_read($member_id, $notice_id) {
		$values = array();
		$values['member_id'] = $member_id;
		$values['notice_id'] = $notice_id;
		
		$model = \Model_Notice_Read::forge($values);
		
		return $model->save() !== false;
	}
}