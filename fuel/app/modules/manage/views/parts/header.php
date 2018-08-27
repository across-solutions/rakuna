<!--#header start -->
<header class="clearfix">

	<!--#header wrapper start -->
	<div class="headerWrapper">

		<!--#logo start -->
		<div class="logo">
			<a href="/manage/order">
				<?php echo Asset::img('logo/manage_logo.png', array('alt' => 'mos')); ?>
			</a>
			<em><?php echo VERSION; ?></em>
		</div>
		<!--#logo end -->

		<span class="manageUser">
			ようこそ<br />
			<span class="manageUserName"><?php echo Session::get('login_info.name'); ?></span>様
		</span>

		<!--#logout button start -->
		<div class="logout">
			<a href="/manage/login/logout" title="ログアウト">
				<span class="icon-off mr"></span>ログアウト
			</a>
		</div>
		<!--#logout button end -->

		<?php
		$auth = Auth::instance();
		if( !$auth->has_access( Config::get('define.manage_page_label.1'), $auth->get_user_mosgroup() ) ){
			echo View::forge('parts/menu_no_admin');
		}
		else {
			echo View::forge('parts/menu');
		}
		?>
	</div>
	<!--#header wrapper end -->

</header>
<!--#header end -->
