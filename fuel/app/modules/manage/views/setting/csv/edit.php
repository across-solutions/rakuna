<!--#title start -->
<div class="title">
	<strong>
		CSV設定
	</strong>
</div>
<!--#title end -->

<!--#configMenuWrap start -->
<div class="configMenuWrap">
	<!--#configMenu start -->
	<div class="configMenu">
		<ul>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.order'); ?>" title="受注CSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.order') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>受注CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.item'); ?>" title="商品CSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.item') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>商品CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.group_assign'); ?>" title="グループ割当CSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.group_assign') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>グループ割当CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.assign'); ?>" title="割当CSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.assign') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>割当CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.item_order_type'); ?>" title="商品発注タイプCSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.item_order_type') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>商品発注タイプCSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.recommended_item'); ?>" title="いつもの商品CSV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.recommended_item') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>いつもの商品CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.recommended_group_assign'); ?>" title="いつものグループ割当CSV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.recommended_group_assign') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>いつものグループ割当CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.member'); ?>" title="発注者CSV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.member') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>発注者CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.delivery'); ?>" title="納品先SV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.delivery') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>納品先CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.sales_representative'); ?>" title="営業担当者CSV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.sales_representative') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>営業担当者CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.delivery_week'); ?>" title="配達曜日CSV設定"
				   <?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.delivery_week') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>配達曜日CSV設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/csv/edit/<?php echo Config::get('define.csv_format_div.holiday'); ?>" title="非営業日CSV設定"
					<?php echo Arr::get($data, 'div') == Config::get('define.csv_format_div.holiday') ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>非営業日CSV設定
				</a>
			</li>
		</ul>
	</div>
	<!--#configMenu end -->
</div>
<!--#configMenuWrap end -->

<div class="sort">

		<?php echo $message(); ?>

		<div class="sortWrap csv">

			<div class="sortItem">
				<strong>CSV</strong>
				<ul class="sortable from">
					<?php foreach($data['templates'] as $row) : ?>
						<li>
							<span class="icon-angle-right mr"></span>
							<input type="text" name="column[]" value="<?php echo Arr::get($row, 'name'); ?>">
							<input type="hidden" name="sort[]" value="<?php echo Arr::get($row, 'key'); ?>">
						</li>
					<?php endforeach; ?>
				</ul>
				<ul class="sortable empty">
					<li class="empty_field">
						<span class="icon-angle-right mr"></span>
						<input type="text" name="column[]" value="">
						<input type="hidden" name="sort[]" value="empty">
					</li>
				</ul>
			</div>

			<div class="sortCursor">
				<span class="icon-chevron-right"></span>
			</div>

			<?php echo Form::open('/manage/setting/csv/edit_save/', array('div' => Arr::get($data, 'div'))); ?>
				<div class="sortComp">
					<strong>出力用CSV</strong>
					<ul class="sortable to">
						<?php foreach($data['formats'] as $row) : ?>
							<li class="<?php echo $get_class($row); ?>">
								<span class="icon-angle-right mr"></span>
								<input type="text" name="column[]" value="<?php echo Arr::get($row, 'name'); ?>">
								<input type="hidden" name="sort[]" value="<?php echo Arr::get($row, 'key'); ?>">
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!--#dig nav start -->
				<div class="digNav configN csv">
					<ul>
						<li>
							<a href="#" title="保存" class="submit">
								<span class="icon-save mr"></span>保存
							</a>
						</li>
						<li>
							<a href="/manage/setting/csv/default" title="MOS標準フォーマットに戻す" class="submit_csv_confirm">
								<span class="icon-save mr"></span>MOS標準フォーマットに戻す
							</a>
						</li>
					</ul>
				</div>
				<!--#dig nav end -->
			<?php echo Form::close(); ?>

		</div>

</div>
