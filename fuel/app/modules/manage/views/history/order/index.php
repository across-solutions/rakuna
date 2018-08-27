<!--#title start -->
<div class="title">
	<strong>
		受注履歴
	</strong>
</div>
<!--#title end -->

<?php echo Form::open(array('action' => '/manage/history/order', 'method' => 'get')); ?>
	<!--#search start -->
	<div class="search">
		<p>
			検索条件を指定して検索してください
		</p>

		<table class="searchBox">
			<tbody>
				<tr>
					<td class="searchTitle">
						<label for="freeword">
						フリーワード
						</label>
					</td>
					<td colspan="3">
						<?php echo Form::input('search_field', Input::get('search_field'), array('id' => 'freeword')); ?>
						<?php echo $validate_error_message('search_field'); ?>
					</td>
				</tr>
				<tr>
					<td class="searchTitle">
						受注日付範囲
					</td>
					<td colspan="3">
						<span class="date_range_picker">
							<span class="range_start_date">
								<?php echo Form::select('order_start_year', Input::get('order_start_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('order_start_month', Input::get('order_start_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('order_start_day', Input::get('order_start_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" id="order_start_datepicker" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="order_start_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							～
							<span class="range_end_date">
								<?php echo Form::select('order_end_year', Input::get('order_end_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('order_end_month', Input::get('order_end_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('order_end_day', Input::get('order_end_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" id="order_end_datepicker" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="order_end_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							<?php echo $validate_search_error_message('order_start_date'); ?>
							<?php echo $validate_search_error_message('order_end_date'); ?>
							<?php echo $validate_search_error_message('order_start_end_date'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td class="searchTitle">
						納品希望日付範囲
					</td>
					<td colspan="3">
						<span class="date_range_picker">
							<span class="range_start_date">
								<?php echo Form::select('delivery_start_year', Input::get('delivery_start_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('delivery_start_month', Input::get('delivery_start_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('delivery_start_day', Input::get('delivery_start_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" id="delivery_start_datepicker" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="delivery_start_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							～
							<span class="range_end_date">
								<?php echo Form::select('delivery_end_year', Input::get('delivery_end_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('delivery_end_month', Input::get('delivery_end_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('delivery_end_day', Input::get('delivery_end_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" id="delivery_end_datepicker" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="delivery_end_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							<?php echo $validate_search_error_message('delivery_start_date'); ?>
							<?php echo $validate_search_error_message('delivery_end_date'); ?>
							<?php echo $validate_search_error_message('delivery_start_end_date'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td class="searchTitle">
						<label for="remark">
						備考
						</label>
					</td>
					<td>
						<?php echo Form::checkbox('comment', '1', Input::get('comment')); ?>
						<?php echo Form::label('備考あり', 'comment'); ?>
					</td>
					<td class="searchTitle">
						<label for="delData">
						削除データ
						</label>
					</td>
					<td>
						<?php echo Form::checkbox('del', '1', Input::get('del')); ?>
						<?php echo Form::label('削除データを含む', 'del'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="searchSubmit">
			<a href="#" class="submit" title="この条件で検索する">
				<span class="icon-search mr"></span>この条件で検索する
			</a>
		</div>
	</div>
	<!--#search end -->
<?php echo Form::close(); ?>

<?php if ($data_count > 0) : ?>
	<!--#resultTop start -->
	<div class="resultTop clearfix">
		<div class="resultText">
			<strong>
				検索結果一覧
			</strong>
			<p>
				<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		</div>

		<div class="paging">
			<?php echo $pager; ?>
		</div>
	</div>
	<!--#resultTop end -->

	<!--#subMenuWrap start -->
	<div class="subMenuWrap">
		<!--#subMenu start -->
		<div class="subMenu">
			<ul>
				<li>
					<a href="/manage/history/order/download<?php echo Common_Util::get_query_string(); ?>" title="CSVダウンロード" class="dialog w180 orderHistDl">
						<span class="icon-chevron-right mr"></span>CSVダウンロード<span class="icon-download abss"></span>
					</a>
				</li>
			</ul>
		</div>
		<!--#subMenu end -->
	</div>
	<!--#subMenuWrap end -->

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th class="w10">受注番号</th>
					<th class="w15">発注日時/納品希望日</th>
					<th class="w12">発注者名</th>
					<?php if (Common_Setting::is_case()) : ?>
						<th class="w8">ケース</th>
						<th class="w8">バラ</th>
					<?php else : ?>
						<th class="w8">数量</th>
					<?php endif; ?>
					<?php if (Common_Setting::is_price()) : ?>
						<th class="w8">合計金額</th>
					<?php endif; ?>
					<th class="w5">備考</th>
					<th class="w5">詳細</th>
					<th class="w5">削除</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr <?php if (Arr::get($row, 'cancel_flg')) : ?>class="delFlg"<?php endif; ?>>
						<td class="center">
							<a href="/manage/order/view/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
								<?php echo Arr::get($row, 'id'); ?>
							</a>
						</td>
						<td class="center">
							<span class="orderTime">
								<?php echo Common_Util::format_datetime(Arr::get($row, 'order_datetime'), 'Y-m-d H:i'); ?>
							</span>
							<span class="deliverTime">
								<?php echo Common_Util::format_date_with_week(Arr::get($row, 'delivery_date'), 'Y-m-d', '---'); ?>
							</span>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'member_name'); ?>
						</td>
						<?php if (Common_Setting::is_case()) : ?>
							<td class="right">
								<?php echo $format_number($row, 'amount_case'); ?>
							</td>
						<?php endif; ?>
						<td class="right">
							<?php echo $format_number($row, 'amount'); ?>
						</td>
						<?php if (Common_Setting::is_price()) : ?>
							<td class="right">
								<?php echo $format_price($row, 'payment_tax'); ?>
							</td>
						<?php endif; ?>
						<td class="center">
							<?php if ($has_comment($row)) : ?>
								<span class="icon-ok remark"></span>
							<?php endif ?>
						</td>
						<td class="center">
							<a href="/manage/order/view/<?php echo Arr::get($row, 'id'); ?>" class="dialog" title="詳細を表示">
								<span class="icon-file decEdit"></span>
							</a>
						</td>
						<td class="center">
							<?php if ($cancelled($row)) : ?>
								済
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
	<!--#list end -->

	<!--#resultBottom start -->
	<div class="resultBottom clearfix">
		<div class="resultText">
			<p>
				<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		</div>

		<div class="paging">
			<?php echo $pager; ?>
		</div>
	</div>
	<!--#resultBottom end -->
<?php else : ?>
	<!--#resultTop start -->
	<div class="resultTop clearfix">
		<div class="resultText">
			データが見つかりませんでした。
		</div>
	</div>
	<!--#resultTop end -->
<?php endif ?>
