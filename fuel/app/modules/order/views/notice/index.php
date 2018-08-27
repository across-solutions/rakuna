<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			お知らせ
		</strong>
	</div>
	<!--#page title end -->

</div>
<!--#boxPadding end -->

<?php if ($data_count > 0) : ?>
	<!--#paging start -->
	<div class="paging">
		<?php echo $pager; ?>
	</div>
	<!--#paging end -->
	
	<!--#news archive box start -->
	<div class="newsArcBox">
		<div class="newsLoop">
			<ul>
				<?php foreach ($rows as $row) : ?>
					<li>
						<dl>
							<dt>
							<?php echo Common_Util::format_date(Arr::get($row, 'entry_datetime'), 'Y年m月d日'); ?>
							</dt>
							<dd>
								<a href="/order/notice/detail/<?php echo Arr::get($row, 'id'); ?>">
									<?php echo Arr::get($row, 'title'); ?>
								</a>
								<?php if (!Arr::get($row, 'read_id')) : ?>
									<span class="new">未読</span>
								<?php endif; ?>
							</dd>
						</dl>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<!--#news archive box end -->
	
	<!--#paging start -->
	<div class="paging">
		<?php echo $pager; ?>
	</div>
	<!--#paging end -->

	<!--#page top start -->
	<div class="pageTop noBorder">
		<a href="#top" title="ページトップ">
			<span class="icon-chevron-up mr"></span>
		</a>
	</div>
	<!--#page top end -->
<?php else : ?>
<div class="boxPadding">
	<div class="noNews">
		<p>
		お知らせはありません
		</p>
	</div>
</div>
<?php endif; ?>
