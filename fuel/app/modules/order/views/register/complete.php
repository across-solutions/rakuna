<!--#boxPadding start -->
<div class="boxPadding">
	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			発注が完了しました
		</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<p>
			発注内容は自動返信メールでお知らせしております。
			また詳細を知りたい場合は「発注履歴を見る」から
			ご覧ください。
		</p>
	</div>
	<!--#page dec end -->

	<!--#buy now start -->
	<div class="buyNow done">
		<a href="/order/history" title="発注履歴を見る">
			<span class="icon-chevron-right mr"></span>発注履歴を見る
		</a>
	</div>
	<!--#buy now end -->
</div>
<!--#boxPadding end -->


<!--#guide start -->
<div class="guide">
	<ul>
		<li>
			<a href="/order/home" title="ホーム">
				<span class="icon-angle-right mr"></span>ホーム
			</a>
		</li>
		<?php if(false) : ?>
		<li>
			<a href="/order/pr" title="<?php echo Common_Setting::get('pr_title'); ?>">
				<span class="icon-angle-right mr"></span><?php echo Common_Setting::get('pr_title'); ?>
			</a>
		</li>
		<?php endif; ?>
		<li>
			<a href="/order/favorite" title="お気に入り">
				<span class="icon-angle-right mr"></span>お気に入り
			</a>
		</li>
	</ul>
</div>
<!--#guide end -->

<!--#search start -->
<div class="search guideNav clearfix">
	<strong>
		商品検索
	</strong>

	<?php echo Form::open(array('action' => '/order/item', 'method' => 'get')); ?>
		<div class="formBox">
			<?php echo Form::input('freeword', null, array('placeholder' => 'キーワードを入力してください')); ?>
		</div>

		<div class="searchSubmit">
			<a href="#" title="検索" class="submit">
			検索
			</a>
		</div>
	<?php echo Form::close(); ?>

</div>
<!--#search end -->

