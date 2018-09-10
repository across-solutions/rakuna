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
								<?php echo Common_Util::format_number(\Common_Util::add_tax($row['price_case'] * $row['item_size_case'], $data['tax_rate'], 1)); ?>
							</td>
						<?php endif; ?>
						<?php if (Common_Setting::is_case()) : ?>
							<td class="right">
								<?php echo Common_Util::format_number(Arr::get($row, 'amount_case')); ?>
							</td>
						<?php endif; ?>
						<?php if (Common_Setting::is_price()) : ?>
							<td class="right">
								<?php echo Common_Util::format_number(\Common_Util::add_tax($row['price'] * $row['item_size'], $data['tax_rate'], 1)); ?>
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

	<!--#dig shipAddr start -->
	<div class="digDeliveryAddr">
		<div class="title">
			<strong>
				納品先
			</strong>
		</div>

		<ul>
			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>納品先コード
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_code'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>納品先名
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_name'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<?php $delivery_receiver_name1 = Arr::get($data, 'delivery_receiver_name1'); ?>
			<?php if (!empty($delivery_receiver_name1)): ?>
				<li>
					<div class="deliveryWrap">
						<dl>
							<dt>
								<span class="icon-caret-right mr"></span>荷受け人名1
							</dt>
							<dd>
								<span class="deliveryAddrBox">
									<?php echo $delivery_receiver_name1; ?>
								</span>
							</dd>
						</dl>
					</div>
				</li>
			<?php endif; ?>

			<?php $delivery_receiver_name2 = Arr::get($data, 'delivery_receiver_name2'); ?>
			<?php if (!empty($delivery_receiver_name2)): ?>
				<li>
					<div class="deliveryWrap">
						<dl>
							<dt>
								<span class="icon-caret-right mr"></span>荷受け人名2
							</dt>
							<dd>
								<span class="deliveryAddrBox">
									<?php echo $delivery_receiver_name2; ?>
								</span>
							</dd>
						</dl>
					</div>
				</li>
			<?php endif; ?>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>郵便番号(〒)
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_zip'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>住所1
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_address1'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>住所2
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_address2'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>住所3
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_address3'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>電話番号
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_tel'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>

			<li>
				<div class="deliveryWrap">
					<dl>
						<dt>
							<span class="icon-caret-right mr"></span>FAX
						</dt>
						<dd>
							<span class="deliveryAddrBox">
								<?php echo Arr::get($data, 'delivery_fax'); ?>
							</span>
						</dd>
					</dl>
				</div>
			</li>
		</ul>
	</div>
	<!--#dig shipAddr end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				発注タイプ
			</dt>
			<dd>
				<?php echo Arr::get($data, 'order_type_name'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				出荷予定日
			</dt>
			<dd>
				<?php echo $shipping_date($data, 'shipping_date'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				納期
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
				出荷区分
			</dt>
			<dd>
				<?php echo Arr::get($data, 'shipping_div'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				倉庫
			</dt>
			<dd>
				<?php echo Arr::get($data, 'warehouse_div'); ?>
			</dd>
		</dl>
	</div>
	<!--#dig comment end -->

	<!--#dig comment start -->
	<div class="digComment">
		<dl>
			<dt>
				オーダーNo.
			</dt>
			<dd>
				<?php echo Arr::get($data, 'order_no'); ?>
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