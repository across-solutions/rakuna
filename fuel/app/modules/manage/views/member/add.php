<!--#dig title start -->
<div class="digTitle">
	<strong>
	発注者を追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	発注者を追加します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	発注者情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/member/add_save'); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="catCode">
					発注者コード<span class="red">*</span>
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
					発注者名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'catName', 'placeholder' => 'サンプル発注者名')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catMemberGroup">
					グループ
					</label>
				</dt>
				<dd>
					<?php echo Form::select('member_group_id',Arr::get($data, 'member_group_id'), $member_groups, array('id' => 'catMemberGroup')); ?>
					<?php echo $validate_error_message('member_group_id'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catSalesPersonCode">
					営業担当者コード
					</label>
				</dt>
				<dd>
					<?php echo Form::input('sales_person_code',Arr::get($data, 'sales_person_code'), array('id' => 'catSalesPersonCode', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('sales_person_code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catCorporation">
					企業名
					</label>
				</dt>
				<dd>
					<?php echo Form::input('corporation', Arr::get($data, 'corporation'), array('id' => 'catCorporation', 'placeholder' => 'サンプル企業名')); ?>
					<a class="tooltip" rel="tooltip" title="40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('corporation'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catStore">
					店舗名
					</label>
				</dt>
				<dd>
					<?php echo Form::input('store', Arr::get($data, 'store'), array('id' => 'catStore', 'placeholder' => 'サンプル店舗名')); ?>
					<a class="tooltip" rel="tooltip" title="40文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('store'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="zip">
						郵便番号
					</label>
				</dt>
				<dd>
					<?php echo Form::input('zip', Arr::get($data, 'zip'), array('id' => 'zip', 'placeholder' => '000-0000')); ?>
					<a class="tooltip" rel="tooltip" title="半角数字、または、ハイフンで入力してください。<br/>8文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('zip'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="address1">
						住所1
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address1', Arr::get($data, 'address1'), array('id' => 'address1', 'placeholder' => 'サンプル住所1')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address1'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="address2">
						住所2
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address2', Arr::get($data, 'address2'), array('id' => 'address2', 'placeholder' => 'サンプル住所2')); ?>
					<a class="tooltip" rel="tooltip" title="50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address2'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="address3">
						住所3
					</label>
				</dt>
				<dd>
					<?php echo Form::input('address3', Arr::get($data, 'address3'), array('id' => 'address3', 'placeholder' => 'サンプル住所3')); ?>
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
					<label for="deliveryFlg">
					納品可能曜日
					</label>
				</dt>
				<dd>
					<?php echo Form::checkbox('delivery_flg_mon', '1', Arr::get($data, 'delivery_flg_mon'), array('id' => 'deliveryFlgMon')); ?>
					<label for="deliveryFlgMon">月</label>
					<?php echo Form::checkbox('delivery_flg_tue', '1', Arr::get($data, 'delivery_flg_tue'), array('id' => 'deliveryFlgTue')); ?>
					<label for="deliveryFlgTue">火</label>
					<?php echo Form::checkbox('delivery_flg_wed', '1', Arr::get($data, 'delivery_flg_wed'), array('id' => 'deliveryFlgWed')); ?>
					<label for="deliveryFlgWed">水</label>
					<?php echo Form::checkbox('delivery_flg_thu', '1', Arr::get($data, 'delivery_flg_thu'), array('id' => 'deliveryFlgThu')); ?>
					<label for="deliveryFlgThu">木</label>
					<?php echo Form::checkbox('delivery_flg_fri', '1', Arr::get($data, 'delivery_flg_fri'), array('id' => 'deliveryFlgFri')); ?>
					<label for="deliveryFlgFri">金</label>
					<?php echo Form::checkbox('delivery_flg_sat', '1', Arr::get($data, 'delivery_flg_sat'), array('id' => 'deliveryFlgSat')); ?>
					<label for="deliveryFlgSat">土</label>
					<?php echo Form::checkbox('delivery_flg_sun', '1', Arr::get($data, 'delivery_flg_sun'), array('id' => 'deliveryFlgSun')); ?>
					<label for="deliveryFlgSun">日</label>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catUsername">
					ログインID
					</label>
				</dt>
				<dd>
					<?php echo Form::input('username', Arr::get($data, 'username'), array('id' => 'catUsername', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="半角英数字で入力してください。<br/>5文字以上、10文字以内で入力してください。<br/>未入力の場合、自動で設定されます。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('username'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catPassword">
					パスワード
					</label>
				</dt>
				<dd>
					<?php echo Form::input('password', Arr::get($data, 'password'), array('id' => 'catPassword', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="半角英数字で入力してください。<br/>5文字以上、15文字以内で入力してください。<br/>未入力の場合、自動で設定されます。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('password'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catEmail">
					メールアドレス
					</label>
				</dt>
				<dd>
					<?php echo Form::input('email', Arr::get($data, 'email'), array('id' => 'catEmail', 'placeholder' => 'sample@example.co.jp')); ?>
					<a class="tooltip" rel="tooltip" title="255文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('email'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catSubEmail">
					サブアドレス
					</label>
				</dt>
				<dd>
					<?php echo Form::input('sub_email[]', Arr::get($data, 'sub_email.0'), array('id' => 'catSubEmail', 'placeholder' => 'sample@example.co.jp')); ?>
					<a class="tooltip" rel="tooltip" title="255文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('sub_email.0'); ?>
				</dd>
			</dl>
			<dl class="clearfix">
				<dt>&nbsp;</dt>
				<dd>
					<?php echo Form::input('sub_email[]', Arr::get($data, 'sub_email.1'), array('placeholder' => 'sample@example.co.jp')); ?>
					<?php echo $validate_error_message('sub_email.1'); ?>
				</dd>
			</dl>
			<dl class="clearfix">
				<dt>&nbsp;</dt>
				<dd>
					<?php echo Form::input('sub_email[]', Arr::get($data, 'sub_email.2'), array('placeholder' => 'sample@example.co.jp')); ?>
					<?php echo $validate_error_message('sub_email.2'); ?>
				</dd>
			</dl>
			<dl class="clearfix">
				<dt>&nbsp;</dt>
				<dd>
					<?php echo Form::input('sub_email[]', Arr::get($data, 'sub_email.3'), array('placeholder' => 'sample@example.co.jp')); ?>
					<?php echo $validate_error_message('sub_email.3'); ?>
				</dd>
			</dl>
			<dl class="clearfix">
				<dt>&nbsp;</dt>
				<dd>
					<?php echo Form::input('sub_email[]', Arr::get($data, 'sub_email.4'), array('placeholder' => 'sample@example.co.jp')); ?>
					<?php echo $validate_error_message('sub_email.4'); ?>
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
