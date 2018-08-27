<?php
namespace Order;
use Fuel\Core\DB;
/**
 * お知らせ一覧プレゼンタクラス
 */
class Presenter_Notice_Index extends \Presenter_Pagination {

	/**
	 * @see Presenter_Pagination::get_count()
	 */
	protected function get_count($data) {

		$member = \Model_Member::query()->where('id', '=', $this->get_member_id())->get_one();

		$query = DB::select(DB::expr('COUNT(*) as count'))
			   ->from('notices');

		Common_Notice::add_condition($member, $query);

		$result = $query->execute()->current();

		return $result['count'];
	}

	/**
	 * @see Presenter_Pagination::get_rows()
	 */
	protected function get_rows($data, $limit, $offset) {

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
			->limit($limit)
			->offset($offset);

		return $query->execute()->as_array();
	}
}