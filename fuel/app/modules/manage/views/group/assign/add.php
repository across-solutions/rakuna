<div class="narrow">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
			グループ割当を追加
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
			グループ割当を追加します
		</p>
	</div>
	<!--#dig text end -->

	<!--#dig dec start -->
	<div class="digDec">
		<p>
			グループ割当情報を入力してください。
		</p>
	</div>
	<!--#dig dec end -->

	<?php echo Form::open('/manage/group/assign/add_save'); ?>
		<?php echo $message(); ?>
		<!--#dig edit form start -->
		<div class="digEditForm clearfix">

			<div class="digForm">

				<dl class="clearfix">
					<dt>
						<label for="memberGroupCode">
							グループコード<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('member_group_code', Arr::get($data, 'member_group_code'), array('id' => 'memberGroupCode', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。<br/>登録済みのグループコードを入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('member_group_code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="itemCode">
							商品コード<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::input('item_code', Arr::get($data, 'item_code'), array('id' => 'itemCode', 'placeholder' => '0000000000000')); ?>
						<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。<br/>登録済みの商品コードを入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('item_code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="priceCase">
							ケース単価
						</label>
					</dt>
					<dd>
						<?php echo Form::input('price_case', Arr::get($data, 'price_case'), array('id' => 'priceCase', 'placeholder' => '0000')); ?>
						<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>10,000,000円未満で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('price_case'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="price">
							バラ単価
						</label>
					</dt>
					<dd>
						<?php echo Form::input('price', Arr::get($data, 'price'), array('id' => 'price', 'placeholder' => '0000')); ?>
						<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>10,000,000円未満で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('price'); ?>
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
</div>