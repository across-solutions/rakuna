<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			お気に入り
		</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<p>
			希望商品に数量を入れ、カートに入れて発注してください。
			商品の星印を押すことで「お気に入り」の解除がおこなえます。
		</p>
	</div>
	<!--#page dec end -->

	<!--#search start -->
	<div class="search sub clearfix">

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
		<p class="info">商品がみつかりませんでした</p>
	<?php endif; ?>

	<?php echo $validate_error_all(); ?>
	<?php echo $message(); ?>

</div>
<!--#boxPadding end -->

<?php if ($data_count > 0) : ?>
	<!--#sort nav start -->
	<div class="sortNav">
		<ul>

			<li>
				<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort.favorite_sort')); ?>" title="お気に入り" class="<?php echo $sort_class('favorite_sort'); ?>">
					お気に入り<span class="<?php echo $sort_icon('favorite_sort'); ?> ml"></span>
				</a>
			</li>

			<li>
				<a href="<?php echo Uri::update_query_string('sort', Config::get('define.search_sort.frequency')); ?>" title="発注頻度" class="<?php echo $sort_class('frequency'); ?>">
					発注頻度
				</a>
			</li>

		</ul>
	</div>
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
		<?php echo View::forge('item/view/list', array('rows' => $rows, 'sort' => $sortable, 'sort_num' => $sort_num, 'validate_error_class' => $validate_error_class, 'page_index' => $page_index)); ?>
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
