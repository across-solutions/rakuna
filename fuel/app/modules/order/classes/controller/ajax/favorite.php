<?php
namespace Order;

use Auth\Auth;
/**
 * お気に入り非同期コントローラクラス
 */
class Controller_Ajax_Favorite extends Controller_Ajax_Base {

	/**
	 * お気に入りの状態を切り替える
	 *
	 * @param string $item_code 商品コード
	 */
	public function get_toggle($item_code = '') {
		if (!Auth::check()) {
			return $this->response_error_auth();
		}

		$item = \Model_Item::query()->where('code', '=', $item_code)->get_one();
		if (empty($item)) {
			return $this->response_error_not_found();
		}

		$member_id = Auth::instance()->get_user_id()[1];
		$favorite = $this->get_favorite($member_id, $item_code);
		if (empty($favorite)) {
			if (!$this->insert_favorite($member_id, $item_code)) {
				return $this->response_error_fatal();
			}

			return $this->response(array('is_favorite' => true));
		} else {
			if (!$favorite->delete()) {
				return $this->response_error_fatal();
			}

			return $this->response(array('is_favorite' => false));
		}
	}

	/**
	 * お気に入りに追加する
	 *
	 * @param string $item_code 商品コード
	 */
	public function get_add($item_code = '') {
		if (!Auth::check()) {
			return $this->response_error_auth();
		}

		$item = \Model_Item::query()->where('code', '=', $item_code)->get_one();
		if (empty($item)) {
			return $this->response_error_not_found();
		}

		$member_id = Auth::instance()->get_user_id()[1];
		$favorite = $this->get_favorite($member_id, $item_code);
		if (empty($favorite)) {
			if (!$this->insert_favorite($member_id, $item_code)) {
				return $this->response_error_fatal();
			}
		}

		return $this->response(array('is_favorite' => true));
	}

	/**
	 * お気に入りを解除する
	 *
	 * @param string $item_code 商品コード
	 */
	public function get_del($item_code = '') {
		if (!Auth::check()) {
			return $this->response_error_auth();
		}

		$item = \Model_Item::query()->where('code', '=', $item_code)->get_one();
		if (empty($item)) {
			return $this->response_error_not_found();
		}

		$member_id = Auth::instance()->get_user_id()[1];
		$favorite = $this->get_favorite($member_id, $item_code);
		if (!empty($favorite)) {
			if (!$favorite->delete()) {
				return $this->response_error_fatal();
			}
		}

		return $this->response(array('is_favorite' => false));
	}

	/**
	 * お気に入りを取得する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function get_favorite($member_id, $item_code) {
		return \Model_Favorite::query()
			->where('member_id', '=', $member_id)
			->where('item_code', '=', $item_code)
			->get_one();
	}

	/**
	 * お気に入りを登録する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param string $item_code 商品コード
	 */
	private function insert_favorite($member_id, $item_code) {
		$values = array();
		$values['member_id'] = $member_id;
		$values['item_code'] = $item_code;
		$values['sort_num']  = $this->count_favorite($member_id);

		$model = \Model_Favorite::forge($values);
		if ($model->save() === false) {
			return false;
		}

		return $model->id;
	}

	/**
	 * 登録されている数を取得する
	 *
	 * @param int $member_id 発注者ID
	 * @param int $count     お気に入り数
	 */
	private function count_favorite($member_id) {
		$count = \Model_Favorite::query()
			->where('member_id', '=', $member_id)
			->count();

		return intval($count) + 1;
	}
}