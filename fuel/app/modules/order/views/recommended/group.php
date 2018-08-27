<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			<?php echo $this_recommended_group_name; ?>
		</strong>
	</div>
	<!--#page title end -->

<!--#search boxs wrap start -->
<div class="searchBoxsWrap">
	<!--#search start -->
	<div class="search sub recommended clearfix">

		<div class="formBox">
			<?php echo Form::input('freeword', Input::get('freeword'), array('id' => 'freeword', 'placeholder' => 'キーワードを入力してください')); ?>
		</div>

		<div class="searchSubmit">
			<a href="#" title="検索" id="freewordSubmit">
			検索
			</a>
		</div>

	</div>
	<!--#search end -->

	<!--#sub title start -->
	<div class="subTitle">
		<strong>
			検索カテゴリ
		</strong>
	</div>
	<!--#sub title end -->

	<!--#search cat start -->
	<div class="searchCat">
		<div class="formSelect">
			<?php echo Form::select('category', Input::get('category'), $categories, array('id' => 'select_category')); ?>
		</div>
	</div>
	<!--#search cat end -->
</div>
<!--#search boxs wrap end -->



	<!--#recommend wrap start -->
	<div class="recomWrap">
		<!--#sub title start -->
		<div class="subTitle">
			<strong>
				いつものグループ
			</strong>
		</div>
		<!--#sub title end -->

		<!--#search cat start -->
		<div class="searchCat">
			<div class="formSelect">
				<?php echo Form::select('recommended_group', Input::get('recommended_group'), $recommended_groups, array('id' => 'select_recommended_group')); ?>
			</div>
		</div>
		<!--#search cat end -->
	</div>
	<!--#recommend wrap end -->



	<!--#result start -->
	<div class="result">
		<p>
			登録数:<?php echo $data_count; ?>
		</p>
	</div>
	<!--#result end -->

	<?php if ($data_count > 0) : ?>
		<!--#display option start -->
		<div class="dispOpt">
			<ul>
				<li>
					<p>
						表示方法
					</p>
				</li>
				<li>
					<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.normal')); ?>" title="通常表示">
						<?php if ($mode == Config::get('define.search_mode.normal')) : ?>
							<span class="icon-sign-blank selected"></span>
						<?php else : ?>
							<span class="icon-sign-blank"></span>
						<?php endif ?>
					</a>
				</li>
				<li>
					<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.image')); ?>" title="画像表示">
						<?php if ($mode == Config::get('define.search_mode.image')) : ?>
							<span class="icon-th-large selected"></span>
						<?php else : ?>
							<span class="icon-th-large"></span>
						<?php endif ?>
					</a>
				</li>
				<li>
					<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.list')); ?>" title="リスト表示">
						<?php if ($mode == Config::get('define.search_mode.list')) : ?>
							<span class="icon-reorder selected"></span>
						<?php else : ?>
							<span class="icon-reorder"></span>
						<?php endif ?>
					</a>
				</li>
			</ul>
		</div>
		<!--#display option end -->
	<?php else : ?>
		いつもの商品がみつかりませんでした
	<?php endif; ?>

</div>
<!--#boxPadding end -->

<?php if ($data_count > 0) : ?>
	<!--#sort nav start -->
	<div class="sortNav noCtrl"></div>
	<!--#sort nav end -->


	<!--#paging start -->
	<div class="paging">
		<?php echo $pager; ?>
	</div>
	<!--#paging end -->

	<?php if ($mode == Config::get('define.search_mode.normal')) : ?>
		<?php echo View::forge('item/view/normal', array('rows' => $rows)); ?>
	<?php elseif ($mode == Config::get('define.search_mode.image')) : ?>
		<?php echo View::forge('item/view/image', array('rows' => $rows)); ?>
	<?php elseif ($mode == Config::get('define.search_mode.list')) : ?>
		<?php echo View::forge('item/view/list', array('rows' => $rows, 'sort' => false)); ?>
	<?php endif; ?>

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

<?php endif; ?>

