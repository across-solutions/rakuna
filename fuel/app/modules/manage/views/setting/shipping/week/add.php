<!--#dig title start -->
<div class="digTitle">
	<strong>
		配達曜日を追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		配達曜日を追加します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		配達曜日情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/setting/shipping/week/add_save'); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="catCode">
						配達曜日コード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('code', Arr::get($data, 'code'), array('id' => 'catCode', 'placeholder' => '0000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="deliveryFlg">
						配達曜日
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
