<!--#title start -->
<div class="title">
	<strong>
		受注集計
	</strong>
</div>
<!--#title end -->

<?php echo Form::open(array('action' => '/manage/summary/item', 'method' => 'get')); ?>
	<!--#search start -->
	<div class="search">
		<p>
			集計条件を指定して集計してください。
		</p>

		<table class="searchBox">
			<tbody>
				<tr>
					<td class="searchTitle">
						<label for="freeword">
							商品名または<br>商品コード
						</label>
					</td>
					<td>
						<?php echo Form::input('item_field', Input::get('item_field'), array('id' => 'freeword')); ?>
						<?php echo $validate_error_message('search_field'); ?>
					</td>
				</tr>
				<tr>
					<td class="searchTitle">
						受注日付範囲<br>(1年間以下の範囲)
					</td>
					<td>
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
							<?php echo $validate_search_error_message('order_start_end_date_interval'); ?>
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

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th class="w5">
						<div>商品コード</div>
						<div class="resultSort">
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.item_code_desc')); ?>" class="<?php echo $sort_class('item_code_desc'); ?>"><span class="icon-chevron-down"></span></a>
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.item_code_asc')); ?>" class="<?php echo $sort_class('item_code_asc'); ?>"><span class="icon-chevron-up"></span></a>
						</div>
					</th>
					<th class="w12">
						<div>商品名</div>
						<div class="resultSort">
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.item_name_desc')); ?>" class="<?php echo $sort_class('item_name_desc'); ?>"><span class="icon-chevron-down"></span></a>
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.item_name_asc')); ?>" class="<?php echo $sort_class('item_name_asc'); ?>"><span class="icon-chevron-up"></span></a>
						</div>
					</th>
					<th class="w5">
						<div><?php echo Common_Setting::is_case() ? '数量(バラ)' : '数量'; ?></div>
						<div class="resultSort">
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.amount_desc')); ?>" class="<?php echo $sort_class('amount_desc'); ?>"><span class="icon-chevron-down"></span></a>
							<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.amount_asc')); ?>" class="<?php echo $sort_class('amount_asc'); ?>"><span class="icon-chevron-up"></span></a>
						</div>
					</th>
					<?php if (Common_Setting::is_case()) : ?>
						<th class="w5">
							<div>数量(ケース)</div>
							<div class="resultSort">
								<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.amount_case_desc')); ?>" class="<?php echo $sort_class('amount_case_desc'); ?>"><span class="icon-chevron-down"></span></a>
								<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort_summary_item.amount_case_asc')); ?>" class="<?php echo $sort_class('amount_case_asc'); ?>"><span class="icon-chevron-up"></span></a>
							</div>
						</th>
					<?php endif; ?>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr <?php if (Arr::get($row, 'cancel_flg')) : ?>class="delFlg"<?php endif; ?>>
						<td class="center">
							<?php echo Arr::get($row, 'item_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'item_name'); ?>
						</td>
						<td class="right">
							<?php echo $format_number($row, 'amount', 0); ?>
						</td>
						<?php if (Common_Setting::is_case()) : ?>
							<td class="right">
								<?php echo $format_number($row, 'amount_case', 0); ?>
							</td>
						<?php endif; ?>
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
