<!--#title start -->
<div class="title">
	<strong>
		ダウンロード履歴
	</strong>
</div>
<!--#title end -->

<?php echo Form::open(array('action' => '/manage/history/download', 'method' => 'get')); ?>
	<!--#search start -->
	<div class="search">
		<p>
			検索条件を指定して検索してください
		</p>

		<table class="searchBox">
			<tbody>
				<tr>
					<td class="searchTitle">
						日付範囲
					</td>
					<td>
						<span class="date_range_picker">
							<span class="range_start_date">
								<?php echo Form::select('start_year', Input::get('start_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('start_month', Input::get('start_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('start_day', Input::get('start_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="order_start_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							～
							<span class="range_end_date">
								<?php echo Form::select('end_year', Input::get('end_year'), $years, array('class' => 'dateSelect year')); ?>
								<?php echo Form::select('end_month', Input::get('end_month'), $months, array('class' => 'dateSelect month')); ?>
								<?php echo Form::select('end_day', Input::get('end_day'), $days, array('class' => 'dateSelect day')); ?>
								<input type="text" class="datepicker" style="display:none;">
								<span class="icon-calendar openDatePicker"></span>
								<span class="clear_button_outside">
									<a class="order_end_clear clear_button" href="#"><span class="icon-remove"></span></a>
								</span>
							</span>
							<?php echo $validate_search_error_message('start_date'); ?>
							<?php echo $validate_search_error_message('end_date'); ?>
							<?php echo $validate_search_error_message('start_end_date'); ?>
						</span>
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

<!--#resultTop start -->
<div class="resultTop clearfix">
	<div class="resultText">
		<?php if ($data_count > 0) : ?>
			<strong>
			検索結果一覧
			</strong>
			<p>
				<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		<?php else : ?>
			データが見つかりませんでした。
		<?php endif ?>
	</div>

	<div class="paging">
		<?php echo $pager; ?>
	</div>
</div>
<!--#resultTop end -->

<?php if ($data_count > 0) : ?>

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>

		<table class="resultList stripe">
			<thead>
				<tr>
					<th class="w15">ダウンロード日時</th>
					<th class="w24">件数</th>
					<th class="w10">ダウンロード</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr>
						<td class="center">
							<a href="/manage/history/download/download/<?php echo Arr::get($row, 'id'); ?>" title="ダウンロード" class="nodeco">
								<?php echo Common_Util::format_datetime(Arr::get($row, 'download_datetime')); ?>
							</a>
						</td>
						<td class="right">
							<?php echo Common_Util::format_number(Arr::get($row, 'record_count')); ?>
						</td>
						<td class="center">
							<a href="/manage/history/download/download/<?php echo Arr::get($row, 'id'); ?>" title="ダウンロード" class="nodeco">
								<span class="icon-download decEdit"></span>
							</a>
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
<?php endif; ?>
