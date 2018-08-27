<!--#content start -->
<div class="content">

	<?php echo Form::open(array('action' => '/manage/login/login', 'id' => 'login_form')); ?>
		<!--#login box start -->
		<div class="loginBox">
			<?php echo $message(); ?>
			<div class="logFormBox">
				<span class="icon-user"></span>
				<?php echo Form::input('username', Arr::get($data, 'username'), array('placeholder' => 'ログインIDを入力')); ?>
				<?php echo $validate_error_message('username'); ?>
			</div>

			<div class="logFormBox">
				<span class="icon-lock"></span>
				<?php echo Form::password('password', null, array('placeholder' => 'パスワードを入力')); ?>
				<?php echo $validate_error_message('password'); ?>
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
