<!--#header start -->
<header class="log">
	<!--#header wrapper start -->
	<div class="headerWrapper">
		<?php echo Image_Logo::img_order_login_logo(); ?>
		<div class="logoSubTitle">for Sales Department</div>
	</div>
	<!--#header wrapper end -->
</header>
<!--#header end -->

<!--#content start -->
<div class="content">

	<?php echo Form::open(array('action' => '/sales/login/login', 'id' => 'login_form')); ?>
		<!--#login box start -->
		<div class="loginBox">
			<div class="logFormBox">
				<span class="icon-user"></span>
				<?php echo Form::input('username', Input::post('username'), array('placeholder' => 'ログインIDを入力')); ?>
				<?php echo $validate_error_message('username'); ?>
			</div>

			<div class="logFormBox">
				<span class="icon-lock"></span>
				<?php echo Form::password('password', '', array('placeholder' => 'パスワードを入力')); ?>
				<?php echo $validate_error_message('password'); ?>
				<?php echo $message(); ?>
			</div>

			<div class="logFormCheck">
				<label>
					<?php echo Form::checkbox('auto_login', '1', Arr::get($data, 'auto_login')); ?>次回からID・パスワードの入力を省略する
				</label>
			</div>

			<div class="login">
				<a href="#" title="ログイン" class="submit">
					<span class="icon-off mr"></span>ログイン
				</a>
			</div>

		</div>
		<!--#login box end -->
	<?php echo Form::close(); ?>

</div>
<!--#contentend -->

<!--#footer start -->
<footer class="log">

	<!--#footer wrapper start -->
	<div class="footerWrapper">

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


