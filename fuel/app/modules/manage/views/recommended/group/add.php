<!--#dig title start -->
<div class="digTitle">
	<strong>
	いつものグループを追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	いつものグループを追加します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	いつものグループ情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/recommended/group/add_save'); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">
	
		<div class="digForm">
		
			<dl class="clearfix">
				<dt>
					<label for="catCode">
					いつものグループコード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('code', Arr::get($data, 'code'), array('id' => 'catCode', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('code'); ?>
				</dd>
			</dl>
		
			<dl class="clearfix">
				<dt>
					<label for="catName">
					いつものグループ名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'catName', 'placeholder' => 'サンプルグループ名')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>
			
		</div>
		
	</div>
	<!--#dig edit form end -->
	
	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<li>
				<a href="#" title="追加" class="submit">
					<span class="icon-save mr"></span>追加
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