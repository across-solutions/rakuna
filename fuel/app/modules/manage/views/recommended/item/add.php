<!--#dig title start -->
<div class="digTitle">
	<strong>
	いつもの商品を追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	いつもの商品を追加します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	いつもの商品情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/recommended/item/add_save'); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="recommendedGroupCode">
					いつものグループ
					</label>
				</dt>
				<dd>
					<?php echo Form::select('recommended_group_code', Arr::get($data, 'recommended_group_code'), $recommended_groups, array('id' => 'recommendedGroupCode')); ?>
					<?php echo $validate_error_message('recommended_group_code'); ?>
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
					<label for="sortNum">
						順番
					</label>
				</dt>
				<dd>
					<?php echo Form::input('sort_num', Arr::get($data, 'sort_num'), array('id' => 'sortNum', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。数字で入力してください。<br/>10,000,000未満で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('sort_num'); ?>
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
