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

<!--#news archive box start -->
<div class="newsArcBox">

	<!--#news dec start -->
	<div class="newsDec clearfix">

		<div class="newsArchive">
			<?php if (!empty($before_latest)) : ?>
				<a href="/order/notice/detail/<?php echo Arr::get($before_latest,'id') ?>" title="<?php echo Arr::get($before_latest,'title'); ?>">
					<span class="icon-angle-left mr"></span>前へ
				</a>
			<?php endif; ?>
			<?php if (!empty($after_latest)) : ?>
				<a href="/order/notice/detail/<?php echo Arr::get($after_latest, 'id'); ?>" title="<?php echo Arr::get($after_latest, 'title'); ?>">
					次へ<span class="icon-angle-right ml"></span>
				</a>
			<?php endif; ?>
			<a href="/order/notice" title="一覧へ">
				<span class="icon-chevron-sign-right mr"></span>一覧へ
			</a>
		</div>

		<div class="newsDate">
			<p>
				<?php echo Common_Util::format_date(Arr::get($data, 'entry_datetime'), 'Y年m月d日'); ?>
			</p>
		</div>

		<div class="newsTitle">
			<strong>
				<?php echo Arr::get($data, 'title'); ?>
			</strong>
		</div>

		<div class="newsTxt">
			<p>
				<?php echo nl2br(Arr::get($data, 'message')); ?>
			</p>
		</div>
		<?php echo View::forge('item/view/normal', array('rows' => $rows, 'single' => true)); ?>

		<div class="newsImg">
			<?php echo Image_Notice::img(Arr::get($data, 'id')); ?>
			<?php if (Pdf_Notice::exist(Arr::get($data, 'id'))) : ?>
				<div class="itemPdfDownload">
					<a target="_blank" href="<?php echo Pdf_Notice::url( Arr::get($data, 'id') ); ?>">
						<span class="icon-file mr"></span> お知らせPDFを表示
					</a>
				</div>
			<?php endif; ?>
		</div>

	</div>
	<!--#news dec end -->

</div>
<!--#news archive box end -->

<!--#page top start -->
<div class="pageTop">
	<a href="#top" title="ページトップ">
		<span class="icon-chevron-up mr"></span>
	</a>
</div>
<!--#page top end -->
