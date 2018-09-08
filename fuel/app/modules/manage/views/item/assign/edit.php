<div class="narrow">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
		割当を編集
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
		割当を編集します
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

	<?php echo Form::open('/manage/item/assign/edit_save', array('id' => Arr::get($data, 'id'))); ?>
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
						<label for="itemHiddenCase">
							ケース表示<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::radio('hidden_flg_case', 0, Arr::get($data, 'hidden_flg_case', 0), array('id' => 'form_hidden_flg_case_0')); ?>
						<?php echo Form::label('表示', 'hidden_flg_case_0'); ?>
						<?php echo Form::radio('hidden_flg_case', 1, Arr::get($data, 'hidden_flg_case', 0), array('id' => 'form_hidden_flg_case_1')); ?>
						<?php echo Form::label('非表示', 'hidden_flg_case_1'); ?>
						<?php echo $validate_error_message('hidden_flg_case'); ?>
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

				<dl class="clearfix">
					<dt>
						<label for="itemHiddenSingle">
							バラ表示<span class="red">*</span>
						</label>
					</dt>
					<dd>
						<?php echo Form::radio('hidden_flg_single', 0, Arr::get($data, 'hidden_flg_single', 0), array('id' => 'form_hidden_flg_single_0')); ?>
						<?php echo Form::label('表示', 'hidden_flg_single_0'); ?>
						<?php echo Form::radio('hidden_flg_single', 1, Arr::get($data, 'hidden_flg_single', 0), array('id' => 'form_hidden_flg_single_1')); ?>
						<?php echo Form::label('非表示', 'hidden_flg_single_1'); ?>
						<?php echo $validate_error_message('hidden_flg_single'); ?>
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
					<a href="/manage/item/assign/delete_save" title="削除" class="submit_delete">
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