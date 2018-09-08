<?php
namespace Order;

use Fuel\Core\Response;
use Fuel\Core\DB;
use Auth\Auth;
use Fuel\Core\Arr;
/**
 * カート処理クラス
 */
class Common_Cart_Util {

	/**
	 * カート情報を取得する
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param bool $check_renewal 商品更新チェック
	 */
	public static function gets($member_id, $check_renewal = true) {
		$query = DB::select('items.id', 'items.code', 'items.name', 'items.size', 'items.comment',
				'items.unit_name_case', 'items.unit_name', 'items.size_case', 'items.size',
				array('items.renewal_datetime', 'item_renewal_datetime'), 'carts.amount', 'carts.amount_case',
				array('carts.item_renewal_datetime', 'cart_item_renewal_datetime'),
				array('carts.item_assign_renewal_datetime', 'cart_assign_renewal_datetime'), 'carts.updated')
			->from('carts')
			->join('items', 'LEFT')
				->on('carts.item_id', '=', 'items.id')
				->on('items.del_flg', '=', DB::escape(UNDELETED))
			->where('carts.member_id', '=', $member_id);

			//if (Common_Assign::has_assign($member_id)) {
				$query->select(array(DB::expr('IFNULL(item_assigns.price_case, items.price_case)'), 'price_case'),
								array(DB::expr('IFNULL(item_assigns.price, items.price)'), 'price'),
								array('item_assigns.renewal_datetime', 'assign_renewal_datetime'));
				$query->join('item_assigns', 'LEFT')
					->on('carts.member_id', '=', 'item_assigns.member_id')
					->on('items.code', '=', 'item_assigns.item_code')
					->on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
			//}

		$carts = $query->execute();

		if ($check_renewal) {
			if (!self::check_item_renewal($carts)) {
				throw new \Exception_renewal();
			}
		}

		return $carts;
	}

	/**
	 * 更新
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param int $amount 数量
	 */
	public static function update($member_id, $item_id, $amount) {
		if ($amount < 0) {
			return false;
		}

		$cart = self::get_cart($member_id, $item_id);
		if (!empty($cart)) {
			if ($amount == 0 && $cart['amount_case'] == 0) {
				self::delete_cart($member_id, $item_id);
				return null;
			}
		}

		$item = self::get_item($item_id, $member_id);
		if (empty($item)) {
			throw new \Exception_renewal();
		}

		$add_amount = empty($cart) ? $amount : $amount - $cart['amount'];

		self::insert_update_cart($member_id, $item_id, $item['item_renewal_datetime'], Arr::get($item, 'item_assign_renewal_datetime'),
				$add_amount, 0);

		$cart = self::get_cart($member_id, $item_id);

		return $cart;
	}

	/**
	 * 更新(ケース)
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param int $amount 数量
	 */
	public static function update_case($member_id, $item_id, $amount) {
		if ($amount < 0) {
			return false;
		}

		$cart = self::get_cart($member_id, $item_id);
		if (!empty($cart)) {
			if ($amount == 0 && $cart['amount'] == 0) {
				self::delete_cart($member_id, $item_id);
				return null;
			}
		}

		$item = self::get_item($item_id, $member_id);
		if (empty($item)) {
			throw new \Exception_renewal();
		}

		$add_amount = empty($cart) ? $amount : $amount - $cart['amount_case'];

		self::insert_update_cart($member_id, $item_id, $item['item_renewal_datetime'], Arr::get($item, 'item_assign_renewal_datetime'),
				0, $add_amount);

		$cart = self::get_cart($member_id, $item_id);

		return $cart;
	}

	/**
	 * 加算
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param int $add_amount 加算数量
	 * @param int $add_amount_case 加算数量(ケース)
	 */
	public static function plus($member_id, $item_id, $add_amount = 1, $add_amount_case = 0) {
		$item = self::get_item($item_id, $member_id);
		if (empty($item)) {
			throw new \Exception_renewal();
		}

		self::insert_update_cart($member_id, $item_id, $item['item_renewal_datetime'], Arr::get($item, 'item_assign_renewal_datetime'),
				$add_amount, $add_amount_case);

		$cart = self::get_cart($member_id, $item_id);

		return $cart;
	}

	/**
	 * 加算(ケース)
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param int $add_amount 加算数量(ケース)
	 */
	public static function plus_case($member_id, $item_id, $add_amount = 1) {
		return self::plus($member_id, $item_id, 0, $add_amount);
	}

	/**
	 * 減算
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param $sub_amount 減算数量
	 * @param $sub_amount_case 減算数量(ケース)
	 */
	public static function minus($member_id, $item_id, $sub_amount = 1, $sub_amount_case = 0) {
		$cart = self::get_cart($member_id, $item_id);
		if (empty($cart)) {
			return null;
		}

		$amount = $cart['amount'] - $sub_amount;
		$amount_case = $cart['amount_case'] - $sub_amount_case;
		if ($amount < 0 || $amount_case < 0) {
			return $cart;
		}
		if ($amount <= 0 && $amount_case <= 0) {
			self::delete_cart($member_id, $item_id);
			return null;
		}

		if (!self::update_cart($member_id, $item_id, $amount, $amount_case)) {
			return false;
		}

		$cart = self::get_cart($member_id, $item_id);

		return $cart;
	}

	/**
	 * 減算(ケース)
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 * @param $sub_amount 減算数量(ケース)
	 */
	public static function minus_case($member_id, $item_id, $sub_amount = 1) {
		return self::minus($member_id, $item_id, 0, $sub_amount);
	}

	/**
	 * 削除
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 */
	public static function delete($member_id, $item_id) {
		$cart = self::get_cart($member_id, $item_id);
		if (empty($cart)) {
			return null;
		}

		return self::minus($member_id, $item_id, $cart['amount']);
	}

	/**
	 * 削除(ケース)
	 *
	 * @param int $member_id 発注者アカウントID
	 * @param int $item_id 商品ID
	 */
	public static function delete_case($member_id, $item_id) {
		$cart = self::get_cart($member_id, $item_id);
		if (empty($cart)) {
			return null;
		}

		return self::minus_case($member_id, $item_id, $cart['amount_case']);
	}

	/**
	 * 商品更新チェック
	 *
	 * @param array $carts カート情報
	 */
	private static function check_item_renewal($carts) {
		if (empty($carts)) {
			return true;
		}

		foreach ($carts as $cart) {
			$cart_item_time = is_null($cart['cart_item_renewal_datetime']) ? null : strtotime($cart['cart_item_renewal_datetime']);
			$cart_assign_time = is_null($cart['cart_assign_renewal_datetime']) ? null : strtotime($cart['cart_assign_renewal_datetime']);
			$item_time = is_null($cart['item_renewal_datetime']) ? null : strtotime($cart['item_renewal_datetime']);
			$assign_renewal_datetime = Arr::get($cart, 'assign_renewal_datetime');
			$assign_time = is_null($assign_renewal_datetime) ? null : strtotime($assign_renewal_datetime);

			if (is_null($item_time) || is_null($cart_item_time) || $cart_item_time < $item_time) {
				return false;
			}

			if (is_null($cart_assign_time) xor is_null($assign_time)) {
				return false;
			}

			if (!is_null($cart_assign_time) && !is_null($assign_time) && $cart_assign_time < $assign_time) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 商品情報を取得する
	 *
	 * @param int $item_id 商品ID
	 * @param int $member_id 発注者ID
	 */
	private static function get_item($item_id, $member_id) {
		$query = DB::select('items.id', array('items.renewal_datetime', 'item_renewal_datetime'))
			->from('items')
			->where('items.id', '=', $item_id)
			->where('items.del_flg', '=', UNDELETED);

		//if (Common_Assign::has_assign($member_id)) {
			$query->select(array('item_assigns.renewal_datetime', 'item_assign_renewal_datetime'));
			$query->join('item_assigns', 'LEFT')
				->on('item_assigns.item_code', '=', 'items.code')
				->and_on('item_assigns.member_id', '=', DB::escape($member_id))
				->and_on('item_assigns.del_flg', '=', DB::escape(UNDELETED));
		//}

		return $query->execute()->current();
	}

	/**
	 * カートを取得する
	 *
	 * @param int $member_id 発注者ID
	 * @param int $item_id 商品ID
	 */
	private static function get_cart($member_id, $item_id) {
		return DB::select('carts.*')
			->from('carts')
			->where('member_id', '=', $member_id)
			->where('item_id', '=', $item_id)
			->execute()
			->current();
	}

	/**
	 * カートを更新する
	 *
	 * @param int $member_id 発注者ID
	 * @param int $item_id 商品ID
	 * @param int $amount 数量
	 * @param int $amount_case 数量(ケース)
	 */
	private static function update_cart($member_id, $item_id, $amount, $amount_case) {
		return DB::update('carts')
			->value('amount', $amount)
			->value('amount_case', $amount_case)
			->value('update_user_id', Auth::get_user_id()[1])
			->value('updated', date('Y-m-d H:i:s'))
			->where('member_id', '=', $member_id)
			->where('item_id', '=', $item_id)
			->execute() !== false;
	}

	/**
	 * カートを登録、または、更新する
	 *
	 * @param string $member_id 発注者ID
	 * @param int $item_id 商品ID
	 * @param string $item_renewal_datetime 商品更新日時
	 * @param string $item_assign_renewal_datetime 割当商品更新日時
	 * @param int $amount バラ数量
	 * @param int $amount_case ケース数量
	 */
	private static function insert_update_cart($member_id, $item_id, $item_renewal_datetime, $item_assign_renewal_datetime, $amount, $amount_case) {
		$query = 'INSERT INTO carts (member_id, item_id, item_renewal_datetime, item_assign_renewal_datetime, amount, amount_case, update_user_id, created, updated) '
				. 'VALUES (:member_id, :item_id, :item_renewal_datetime, :item_assign_renewal_datetime, :amount, :amount_case, :update_user_id, now(), now()) '
						. 'ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount), amount_case = amount_case + VALUES(amount_case), update_user_id = VALUES(update_user_id), updated = now()';
		$params = array();
		$params['member_id'] = $member_id;
		$params['item_id'] = $item_id;
		$params['item_renewal_datetime'] = $item_renewal_datetime;
		$params['item_assign_renewal_datetime'] = $item_assign_renewal_datetime;
		$params['amount'] = $amount;
		$params['amount_case'] = $amount_case;
		$params['update_user_id'] = Auth::get_user_id()[1];

		$result = DB::query($query)->parameters($params)->execute();

		return !empty($result);
	}

	/**
	 * カートを削除する
	 *
	 * @param int $member_id 発注者ID
	 * @param int $item_id 商品ID
	 */
	private static function delete_cart($member_id, $item_id) {
		return DB::delete('carts')
			->where('member_id', '=', $member_id)
			->where('item_id', '=', $item_id)
			->execute() !== false;
	}
}