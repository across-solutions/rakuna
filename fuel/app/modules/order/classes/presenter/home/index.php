<?php
namespace Order;

use Fuel\Core\Input;
use Fuel\Core\Arr;
use Fuel\Core\DB;

/**
 * ホームプレゼンタクラス
 */
class Presenter_Home_Index extends \Presenter_Base {

	/**
	 * @see \Fuel\Core\Presenter::view()
	 */
	public function view() {
		parent::view();

		$this->notices = $this->get_notices();

		$this->categories = Common_Category::list_category($this->get_member_id());
	}

	/**
	 * お知らせを取得する
	 */
	private function get_notices() {

		$member = \Model_Member::query()->where('id', '=', $this->get_member_id())->get_one();

		$query = DB::select('notices.id', 'notices.title', 'notices.entry_datetime', array('notice_reads.id', 'read_id'))
			->from('notices')
			->join('notice_reads', 'LEFT')
				->on('notice_reads.notice_id', '=', 'notices.id')
				->and_on('notice_reads.member_id', '=', DB::expr($this->get_member_id()))
				->and_on('notice_reads.del_flg', '=', DB::expr(UNDELETED));

		Common_Notice::add_condition($member, $query);

		$query->order_by('entry_datetime', 'desc')
			->order_by('notices.id', 'desc')
			->limit(HOME_NOTICE_NUM);

		return $query->execute()->as_array();
	}
}