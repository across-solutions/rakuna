<?php echo View::forge('setting/user/menu', array('selected' => 'order')); ?>

<?php echo $message(); ?>

<!--#config start -->
<div class="config">
	<?php echo Form::open(array('action' => '/manage/setting/user/order/save','enctype' => 'multipart/form-data')); ?>
		<div id="orderLink" class="configWrap">
			<strong>
				発注管理設定
			</strong>
			<p>
				表示する商品数、消費税などの設定をおこないます。
			</p>

			<dl class="clearfix">
				<dt>
					<label for="dispNum">
						商品表示件数<span class="red">*</span>
					</label>
				</dt>
				<dd class="short">
					<?php echo Form::input('item_num', Arr::get($data, 'item_num'), array('placeholder' => '3～18の数字を入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>3～18の数字を入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('item_num'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="tax">
						消費税率<span class="red">*</span>
					</label>
				</dt>
				<dd class="short">
					<?php echo Form::input('tax_rate', Arr::get($data, 'tax_rate'), array('placeholder' => '消費税率を入力してください。')); ?>%
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>0～100の数字を入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('tax_rate'); ?>
				</dd>
			</dl>

			<!--#dig nav start -->
			<div class="digNav configN">
				<ul>
					<li>
						<a href="#" title="保存" class="submit">
							<span class="icon-save mr"></span>保存
						</a>
					</li>
				</ul>
			</div>
			<!--#dig nav end -->
		</div>
	<?php echo Form::close(); ?>
</div>
<!--#config end -->