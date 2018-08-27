<?php echo View::forge('setting/user/menu', array('selected' => 'account')); ?>

<?php echo $message(); ?>

<!--#config start -->
<div class="config">
	<?php echo Form::open('/manage/setting/user/account/save'); ?>
		<div id="logLink" class="configWrap">
			<strong>
				管理者アカウント設定
			</strong>
			<p>
				受注画面へログインする際のID、パスワードの設定をおこないます。
			</p>
		
			<dl class="clearfix">
				<dt>
					<label for="loginId">
						ログインID<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('username', Arr::get($data, 'username'), array('placeholder' => '再設定したいログインIDを入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>5文字以上、20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('username'); ?>
				</dd>
			</dl>
		
			<dl class="clearfix">
				<dt>
					<label for="passwd">
						パスワード
					</label>
				</dt>
				<dd>
					<?php echo Form::password('password', null, array('placeholder' => '再設定したいパスワードを入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="変更する場合は入力してください。<br/>半角英数字で入力してください。<br/>5文字以上、15文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('password'); ?>
				</dd>
			</dl>
		
			<dl class="clearfix">
				<dt>
					<label for="repass">
						パスワード(再)
					</label>
				</dt>
				<dd>
					<?php echo Form::password('password_confirm', null, array('placeholder' => '再設定したいログインIDを再入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="変更する場合は入力してください。<br/>パスワードを再入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('password_confirm'); ?>
				</dd>
			</dl>
		
			<!--#dig nav start -->
			<div class="digNav configN">
				<ul>
					<li>
						<a href="#" title="保存" class="submit">
							<span class="icon-save mr"></span>保存
						</a>
					</li>
				</ul>
			</div>
			<!--#dig nav end -->
		</div>
	<?php echo Form::close(); ?>
</div>
<!--#config end -->