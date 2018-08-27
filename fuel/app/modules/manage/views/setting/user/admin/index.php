<?php echo View::forge('setting/user/menu', array('selected' => 'admin')); ?>

<?php echo $message(); ?>

<!--#config start -->
	<div class="config">
	<?php echo Form::open('/manage/setting/user/admin/save'); ?>
		<div id="accLink" class="configWrap">
			<strong>
				管理者設定
			</strong>
			<p>
				管理者名、管理企業名の設定をおこないます。管理者名、管理企業名は○○○に表示されます。
			</p>
			
			<dl class="clearfix">
				<dt>
					<label for="masterName">
						管理者名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('administrator_name', Arr::get($data, 'administrator_name'), array('placeholder' => '表示したい管理者名を入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('administrator_name'); ?>
				</dd>
			</dl>
			
			<dl class="clearfix">
				<dt>
					<label for="masterCo">
						管理企業名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('corporation_name', Arr::get($data, 'corporation_name'), array('placeholder' => '表示したい管理企業名を入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('corporation_name'); ?>
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