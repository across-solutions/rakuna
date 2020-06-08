<?php
namespace Order;

use Auth\Auth;
use Fuel\Core\Arr;
use Fuel\Core\DB;
use Fuel\Core\Config;
/**
 * 納品先非同期コントローラクラス
 */
class Controller_Ajax_Delivery extends Controller_Ajax_Base {

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
	 * 納品先取得処理
	 *
	 * @param string $delivery_code 送付先コード
	 */
	public function get_data($delivery_code) {
		try {
			$member = $this->get_member($this->get_member_id());
			$delivery = $this->get_delivery($delivery_code, $member->code);
			if (empty($delivery)) {
				return $this->response_error_fatal();
			}
			return $this->create_response($delivery, $member->code);
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
	 * 納品先データを取得する
	 *
	 * @param string $delivery_code 納品先コード
	 * @param string $member_code 発注者コード
	 */
	private function get_delivery($delivery_code, $member_code) {
		$query = DB::select('deliveries.id', 'deliveries.code', 'deliveries.name', 'deliveries.receiver_name1',
							'deliveries.receiver_name2', 'deliveries.zip', 'deliveries.address1', 'deliveries.address2',
							'deliveries.address3', 'deliveries.tel', 'deliveries.fax')
				->from('deliveries')
				->where('deliveries.code', '=', $delivery_code)
				->where('deliveries.member_code', '=', $member_code)
				->where('deliveries.del_flg', '=', UNDELETED);

		return $query->execute()->current();
	}

	/**
	 * レスポンスを生成する
	 *
	 * @param $delivery 納品先
	 * @param $member_code 発注者コード
	 */
	private function create_response($delivery, $member_code) {
		$result = array();
		$result['name'] = Arr::get($delivery, 'name');
		$result['receiver_name1'] = Arr::get($delivery, 'receiver_name1');
		$result['receiver_name2'] = Arr::get($delivery, 'receiver_name2');
		$result['zip'] = Arr::get($delivery, 'zip');
		$result['address1'] = Arr::get($delivery, 'address1');
		$result['address2'] = Arr::get($delivery, 'address2');
		$result['address3'] = Arr::get($delivery, 'address3');
		$result['tel'] = Arr::get($delivery, 'tel');
		$result['fax'] = Arr::get($delivery, 'fax');
		$result['delivery_date'] = \Common_Util::get_nearest_delivery_week('deliveries', $member_code, Arr::get($delivery, 'code'));
/*
		$dates = \Common_Util::get_delivery_dates('deliveries', $member_code, Arr::get($delivery, 'code'));
		foreach ($dates as $key => $value) {
			$result['dates'][] = array($key => $value);
		}
*/
		return $this->response($result);
	}
}