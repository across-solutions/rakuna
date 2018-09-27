<!--#dig title start -->
<div class="digTitle">
	<strong>
	納品先を追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	納品先を追加します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	納品先情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/delivery/add_save'); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="parentMemberCode">
					発注者コード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('member_code', Arr::get($data, 'member_code'), array('id' => 'parentMemberCode', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('member_code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catCode">
					納品先コード<span class="red">*</span>
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
					納品先名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'catName', 'placeholder' => 'サンプル納品先名')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catNameKana">
					納品先カナ名
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name_kana', Arr::get($data, 'name_kana'), array('id' => 'name_kana', 'placeholder' => 'サンプルカナメイ')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name_kana'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catReceiverName1">
					荷受け人名1
					</label>
				</dt>
				<dd>
					<?php echo Form::input('receiver_name1', Arr::get($data, 'receiver_name1'), array('id' => 'catReceiverName1', 'placeholder' => 'サンプル荷受け人名1')); ?>
					<a class="tooltip" rel="tooltip" title="40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('receiver_name1'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catReceiverName2">
					荷受け人名2
					</label>
				</dt>
				<dd>
					<?php echo Form::input('receiver_name2', Arr::get($data, 'receiver_name2'), array('id' => 'catReceiverName2', 'placeholder' => 'サンプル荷受け人名2')); ?>
					<a class="tooltip" rel="tooltip" title="40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('receiver_name2'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="warehouseZip">
						郵便番号
					</label>
				</dt>
				<dd>
					<?php echo Form::input('zip', Arr::get($data, 'zip'), array('id' => 'warehouseZip', 'placeholder' => '000-0000')); ?>
					<a class="tooltip" rel="tooltip" title="半角数字、または、ハイフンで入力してください。<br/>8文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('zip'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="warehouseAddress1">
						住所1
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address1', Arr::get($data, 'address1'), array('id' => 'warehouseAddress1', 'placeholder' => 'サンプル住所1')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address1'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="warehouseAddress2">
						住所2
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address2', Arr::get($data, 'address2'), array('id' => 'warehouseAddress2', 'placeholder' => 'サンプル住所2')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address2'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="warehouseAddress3">
						住所3
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address3', Arr::get($data, 'address3'), array('id' => 'warehouseAddress3', 'placeholder' => 'サンプル住所3')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address3'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catTel">
					電話番号
					</label>
				</dt>
				<dd>
					<?php echo Form::input('tel', Arr::get($data, 'tel'), array('id' => 'catTel', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="半角数字、または、ハイフンで入力してください。<br/>14文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('tel'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catFax">
					FAX
					</label>
				</dt>
				<dd>
					<?php echo Form::input('fax', Arr::get($data, 'fax'), array('id' => 'catFax', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="半角数字、または、ハイフンで入力してください。<br/>14文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('fax'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="deliveryWeekCode">
						配達曜日コード
					</label>
				</dt>
				<dd>
					<?php echo Form::input('delivery_week_code', Arr::get($data, 'delivery_week_code'), array('id' => 'deliveryWeekCode', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="半角英数字で入力してください。<br/>10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('delivery_week_code'); ?>
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
