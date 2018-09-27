<?php
namespace Order;

use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
/**
 * 発注者非同期コントローラクラス
 */
class Controller_Ajax_Member extends Controller_Ajax_Base {

	/**
	 * @see \Order\Controller_Ajax_Base::before()
	 */
	public function before() {
		parent::before();

		if (!Auth::check()) {
			return $this->response_error_auth();
		}
	}

	/**
	 * 発注者取得処理
	 */
	public function get_data() {
		try {
			$member = $this->get_member($this->get_member_id());
			if (empty($member)) {
				return $this->response_error_fatal();
			}
			return $this->create_response($member);
		} catch(\Exception_renewal $e) {
			return $this->response_item_renewal();
		} catch (\Exception $e) {
			return $this->response_error_fatal();
		}
	}

	/**
	 * 発注者情報を取得する
	 *
	 * @param int $member_id 発注者アカウントID
	 */
	private function get_member($member_id) {
		return \Model_Member::query()
			->where('id', '=', $member_id)
			->where('status', '=', Config::get('define.member_status.enable'))
			->get_one();
	}

	/**
	 * レスポンスを生成する
	 *
	 * @param $member 発注者
	 */
	private function create_response($member) {
		$result = array();
		$result['name'] = Arr::get($member, 'name');
		$result['zip'] = Arr::get($member, 'zip');
		$result['address'] = Arr::get($member, 'address');
		$result['address2'] = Arr::get($member, 'address2');
		$result['address3'] = Arr::get($member, 'address3');
		$result['tel'] = Arr::get($member, 'tel');
		$result['fax'] = Arr::get($member, 'fax');
/*
		$dates = \Common_Util::get_delivery_dates('members', $member->code);
		foreach ($dates as $key => $value) {
			$result['dates'][] = array($key => $value);
		}
*/
		return $this->response($result);
	}
}