<!--#dig title start -->
<div class="digTitle">
	<strong>
		発注タイプを編集
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		発注タイプ情報を編集します
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

<?php echo Form::open('/manage/setting/order/type/edit_save', array('id' => Arr::get($data, 'id'))); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="orderTypeName">
					発注タイプ名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'orderTypeName', 'placeholder' => 'サンプル商品')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="orderTypeCode">
					出荷区分コード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('code', Arr::get($data, 'code'), array('id' => 'orderTypeCode', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="orderTypeWarehouseCode">
					倉庫コード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('warehouse_code', Arr::get($data, 'warehouse_code'), array('id' => 'orderTypeWarehouseCode', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('warehouse_code'); ?>
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
			<?php if ($data['id'] > 4) : ?>
				<li>
					<a href="/manage/setting/order/type/delete_save" title="削除" class="submit_delete">
						<span class="icon-trash mr"></span>削除
					</a>
				</li>
			<?php endif ; ?>
			<li>
				<a href="#" title="キャンセル" class="close">
					<span class="icon-remove mr"></span>キャンセル
				</a>
			</li>
		</ul>
	</div>
	<!--#dig nav end -->
<?php echo Form::close(); ?>
