<!--#header start -->
<header>

	<!--#header wrapper start -->
	<div class="headerWrapper clearfix">
		<div class="logo clearfix">
			<a href="/order/home" title="ホーム" data-tor-smoothScroll="noSmooth">
				<?php echo Image_Logo::img_order_base_logo(); ?>
			</a>

			<p class="custName">
			ようこそ<br />
			<span><?php echo Session::get('login_info.name'); ?></span>様
			</p>
		</div>

	</div>
	<!--#header wrapper end -->

</header>
<!--#header end -->
