<div class="normal">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
		営業担当を編集
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
		営業担当を編集します
		</p>
	</div>
	<!--#dig text end -->

	<!--#dig dec start -->
	<div class="digDec">
		<p>
		フォームに更新する情報を入力してください。
		</p>
	</div>
	<!--#dig dec end -->

	<?php echo Form::open('/manage/sales/representative/edit_save', array('id' => Arr::get($data, 'id'))); ?>
		<?php echo $message(); ?>
		<!--#dig edit form start -->
		<div class="digEditForm clearfix">

			<div class="digForm">

				<dl class="clearfix">
					<dt>
						<label for="catPersonCode">
						営業担当コード<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('sales_person_code', Arr::get($data, 'sales_person_code'), array('id' => 'catPersonCode', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('sales_person_code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="catPersonName">
						営業担当名<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('sales_person_name', Arr::get($data, 'sales_person_name'), array('id' => 'catPersonName', 'placeholder' => 'サンプル営業担当名')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>40文字以内で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('sales_person_name'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="catDepartmentCode">
							部門コード<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('department_code', Arr::get($data, 'department_code'), array('id' => 'catDepartmentCode', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('department_code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="catUsername">
						ログインID<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('username', Arr::get($data, 'username'), array('id' => 'catUsername', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>5文字以上、10文字以内で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('username'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="catPassword">
						パスワード<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('password', Arr::get($data, 'password'), array('id' => 'catPassword', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>5文字以上、15文字以内で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('password'); ?>
					</dd>
				</dl>

			</div>

		</div>
		<!--#dig edit form end -->

		<!--#dig nav start -->
		<div class="digNav">
			<ul>
				<li>
					<a href="#" title="保存" class="submit">
						<span class="icon-save mr"></span>保存
					</a>
				</li>
				<li>
					<a href="/manage/sales/representative/delete_save" title="削除" class="submit_delete">
						<span class="icon-trash mr"></span>削除
					</a>
				</li>
				<li>
					<a href="#" title="キャンセル" class="close">
						<span class="icon-remove mr"></span>キャンセル
					</a>
				</li>
			</ul>
		</div>
		<!--#dig nav end -->
	<?php echo Form::close(); ?>
</div>