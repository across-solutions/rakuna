<!--#dig title start -->
<div class="digTitle">
	<strong>
	発注者を編集
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	発注者を編集します
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

<?php echo Form::open('/manage/member/edit_save', array('id' => Arr::get($data, 'id'))); ?>
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
					<label for="catAddress">
					住所
					</label>
				</dt>
				<dd>
					<?php echo Form::textarea('address', Arr::get($data, 'address'), array('id' => 'catAddress', 'placeholder' => 'サンプル住所')); ?>
					<a class="tooltip" rel="tooltip" title="500文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('address'); ?>
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
				<a href="#" title="保存" class="submit">
					<span class="icon-save mr"></span>保存
				</a>
			</li>
			<li>
				<a href="/manage/member/delete_save" title="削除" class="submit_delete">
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
