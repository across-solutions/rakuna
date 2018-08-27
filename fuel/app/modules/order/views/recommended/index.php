<!--#boxPadding start -->
<div class="boxPadding">
	<!--#page title start -->
	<div class="pageTitle">
		<strong>
		いつものグループ一覧</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<p>いつものグループを選択して閲覧ができます。</p>
	</div>
	<!--#page dec end -->

	<!--#result start -->
	<div class="result">
		<p>
			検索結果:<?php echo count($rows); ?>
		</p>
	</div>
	<!--#result end -->

	<!--#page dec start -->
	<div class="pageDec">
		<?php if ( count($rows) <= 0) : ?>
			<p class="info">いつものグループがみつかりませんでした</p>
		<?php endif; ?>
	</div>
	<!--#page dec end -->
</div>
<!--#boxPadding end -->

<?php echo $message(); ?>

<!--#recomPro start -->
<div class="recomPro single">
	<ul>
		<?php foreach($rows as $row) : ?>
			<li>
				<a href="/order/recommended/group/<?php echo Arr::get($row, 'id'); ?>">
					<div class="recomBoxWrap">
						<div class="recomBoxCnt">
							<span class="digMr recomNum">
								<i>
									<?php echo Arr::get($row, 'count')?>
								</i>
							</span>
						</div>
						<p><?php echo Arr::get($row, 'name')?></p>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<!--#recomPro end -->
