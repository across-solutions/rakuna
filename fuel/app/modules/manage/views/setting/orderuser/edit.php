<!--#dig title start -->
<div class="digTitle">
	<strong>
	受注担当者を編集
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	受注担当者を編集します
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

<?php echo Form::open('/manage/setting/orderuser/edit_save', array('id' => Arr::get($data, 'id'))); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="catName">
					受注担当者名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'catName', 'placeholder' => 'サンプル受注担当者名')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catUsername">
					ログインID<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('username', Arr::get($data, 'username'), array('id' => 'catUsername', 'placeholder' => '0000000000')); ?>
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
					<?php echo Form::input('password', Arr::get($data, 'password'), array('id' => 'catPassword', 'placeholder' => '0000000000')); ?>
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
				<a href="/manage/setting/orderuser/delete_save" title="削除" class="submit_delete">
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
