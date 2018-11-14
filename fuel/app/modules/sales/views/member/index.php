<!--#header start -->
<header>

	<!--#header wrapper start -->
	<div class="headerWrapper clearfix">
		<div class="logo clearfix">
			<a href="/sales/member" title="ホーム" data-tor-smoothScroll="noSmooth">
				<?php echo Image_Logo::img_order_base_logo(); ?>
			</a>

			<p class="custName">
				ようこそ<br />
			<span><?php echo Session::get('login_info.sales_person_name'); ?></span>様
			</p>
		</div>
	</div>
	<!--#header wrapper end -->

</header>
<!--#header end -->

<!--#content start -->
<div class="content">

	<!--#content Wrapper start -->
	<div class="contentWrapper">

		<div class="clearfix">
			<!--#cat start -->
			<div class="catall">

				<strong>
					発注者選択
				</strong>
				<?php echo $message(); ?>

				<!--#boxPadding start -->
				<div class="boxPadding">

					<?php echo Form::open(array('action' => '/sales/member', 'method' => 'get')); ?>
						<!--#search boxs wrap start -->
						<div class="searchBoxsWrap">
							<!--#search start -->
							<div class="search sub clearfix chs">
								<div class="formBox">
									<?php echo Form::input('freeword', Input::get('freeword'), array('id' => 'freeword', 'placeholder' => '')); ?>
								</div>

								<div class="searchSubmit">
									<a href="#" title="検索" class="submit">
										<span class="ssInner"></span>検索
									</a>
								</div>

							</div>
							<!--#search end -->
						</div>
						<!--#search boxs wrap end -->
					<?php echo Form::close(); ?>

						<!--#result start -->
						<div class="result">
							<p>
								検索結果:<?php echo $data_count; ?>
							</p>
						</div>
						<!--#result end -->

				</div>
				<!--#boxPadding end -->

				<?php if (count($rows) > 0) : ?>

					<!--#paging start -->
					<div class="paging">
						<?php echo $pager; ?>
					</div>
					<!--#paging end -->

					<div class="catallList">
						<ul>
							<?php foreach($rows as $member_code => $member_name) : ?>
								<li>
									<a href="/sales/member/select/<?php echo $member_code; ?>" data-tor-smoothScroll="noSmooth">
										<p>
											<?php echo $member_code; ?>:<?php echo $member_name; ?>
										</p>
										<span class="icon-chevron-right"></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

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
					<div class="cateallMessage">
						発注者がみつかりませんでした
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

</div>

<!--#footer start -->
<footer>

	<!--#footer wrapper start -->
	<div class="footerWrapper">

		<!--#footer nav start -->
		<nav class="footerNav clearfix">
			<ul></ul>
			<div class="logout">
				<a href="/sales/login/logout" title="ログアウト">
					ログアウト
				</a>
			</div>

		</nav>
		<!--#footer nav end -->

		<!--#copyright start -->
		<div class="copyright">
			<p>
				powerd by <a href="http://www.mosjapan.jp/" title="MOS" target="_blank">MOS</a>
			</p>
			<p>
				&copy;ACROSS Solutions,inc. All Rights Reserved.
			</p>
		</div>
		<!--#copyright end -->

	</div>
	<!--#footer wrapper end -->

</footer>
<!--#footer end -->