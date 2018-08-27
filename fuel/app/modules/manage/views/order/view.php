<!--#dig title start -->
<div class="digTitle">
	<strong>
		詳細を表示
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p class="stamp">
		<?php echo $format_date($data, 'order_datetime', 'Y年m月d日H時i分'); ?>
	</p>
</div>
<!--#dig text end -->

<!--#dig user info start -->
<div class="digUserInfo">
	<strong>
		<?php echo Arr::get($data, 'member_name'); ?>
	</strong>
	<p>
		コード:<?php echo Arr::get($data, 'member_code'); ?>
	</p>
</div>
<!--#dig user info end -->

<!--#dig result start -->
<div class="digResult clearfix">

	<?php if (Common_Setting::is_price()) : ?>
		<div class="digAmount">
			<strong>
				合計<span><?php echo Common_Util::format_number(Arr::get($data, 'payment_tax')); ?>円</span>
			</strong>
		</div>
	<?php endif; ?>

	<div class="digTotal">
		<strong>商品注文合計数</strong>
		<?php if (Common_Setting::is_case()) : ?>
			<p>
				ケース合計<span><?php echo Common_Util::format_number(Arr::get($data, 'amount_case')); ?></span>
			</p>
		<?php endif; ?>
		<p>
			<?php if (Common_Setting::is_case()) : ?>
			バラ合計
			<?php endif; ?>
			<span><?php echo Common_Util::format_number(Arr::get($data, 'amount')); ?></span>
		</p>
	</div>

</div>
<!--#dig result end -->

<?php echo Form::open('/manage/order/delete_save', array('id' => Arr::get($data, 'id'))); ?>
	<?php echo $message(); ?>
	<!--#dig list start -->
	<div class="digList">

		<table class="digResultList">
			<thead>
				<tr>
					<th class="w20">カテゴリ</th>
					<th class="">商品</th>
					<?php if (Common_Setting::is_price() && Common_Setting::is_case()) : ?>
						<th class="w10">ケース単価</th>
					<?php endif; ?>
					<?php if (Common_Setting::is_case()) : ?>
						<th class="w10">ケース</th>
					<?php endif; ?>
					<?php if (Common_Setting::is_price()) : ?>
						<th class="w10"><?php echo Common_Setting::is_case() ? 'バラ単価' : '単価'; ?></th>
					<?php endif; ?>
					<th class="w10"><?php echo Common_Setting::is_case() ? 'バラ' : '数量'; ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($data->order_details as $row) : ?>
					<tr>
						<td class="left">
							<?php echo Arr::get($row, 'category_code'); ?><br>
							<?php echo Arr::get($row, 'category_name'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'item_code'); ?><br>
							<?php echo Arr::get($row, 'item_name'); ?>
						</td>
						<?php if (Common_Setting::is_price() && Common_Setting::is_case()) : ?>
							<td class="right">
								<?php echo Common_Util::format_number(Arr::get($row, 'price_case_tax')); ?>
							</td>
						<?php endif; ?>
						<?php if (Common_Setting::is_case()) : ?>
							<td class="right">
								<?php echo Common_Util::format_number(Arr::get($row, 'amount_case')); ?>
							</td>
						<?php endif; ?>
						<?php if (Common_Setting::is_price()) : ?>
							<td class="right">
								<?php echo Common_Util::format_number(Arr::get($row, 'price_tax')); ?>
							</td>
						<?php endif; ?>
						<td class="right">
							<?php echo Common_Util::format_number(Arr::get($row, 'amount')); ?>
						</td>
					</tr>
				<?php endforeach ?>

			</tbody>
		</table>

	</div>
	<!--#dig list end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				納品希望日
			</dt>
			<dd>
				<?php echo $delivery_date($data, 'delivery_date'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				備考
			</dt>
			<dd>
				<?php echo $comment($data, 'comment'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<?php if (!$data->cancel_flg) : ?>
				<li>
					<a href="#" title="受注データを削除" class="submit_delete">
						<span class="icon-trash mr"></span>受注データを削除
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="#" title="閉じる" class="close">
					<span class="icon-remove mr"></span>閉じる
				</a>
			</li>
		</ul>
	</div>
	<!--#dig nav end -->
<?php echo Form::close(); ?>