<div class="narrow">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
		商品発注タイプを編集
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
		商品発注タイプを編集します
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

	<?php echo Form::open('/manage/item/order/type/edit_save', array('id' => Arr::get($data, 'id'))); ?>
		<?php echo $message(); ?>
		<!--#dig edit form start -->
		<div class="digEditForm clearfix">

			<div class="digForm">

				<dl class="clearfix">
					<dt>
						発注者コード
					</dt>
					<dd>
						<?php echo Arr::get($member, 'code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						発注者名
					</dt>
					<dd>
						<?php echo Arr::get($member, 'name'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						商品コード
					</dt>
					<dd>
						<?php echo Arr::get($data, 'item_code'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						商品名
					</dt>
					<dd>
						<?php echo Arr::get($data, 'items.name'); ?>
					</dd>
				</dl>

				<dl class="clearfix">
					<dt>
						<label for="orderType">
						発注タイプ<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::select('order_type', Arr::get($data, 'order_type', 3), $order_types, array('id' => 'orderType')); ?>
						<?php echo $validate_error_message('order_type'); ?>
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
					<a href="/manage/item/order/type/delete_save" title="削除" class="submit_delete">
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