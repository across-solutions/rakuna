<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			発注履歴詳細
		</strong>
	</div>
	<!--#page title end -->

</div>
<!--#boxPadding end -->

<!--#news arc box start -->
<div class="newsArcBox">

	<div class="newsArchive">
		<?php if (!empty($before_latest)) : ?>
			<a href="/order/history/detail/<?php echo $before_latest->id; ?>" title="前へ">
				<span class="icon-angle-left mr"></span>前へ
			</a>
		<?php endif; ?>
		<?php if (!empty($after_latest)) : ?>
			<a href="/order/history/detail/<?php echo $after_latest->id; ?>" title="次へ">
				次へ<span class="icon-angle-right ml"></span>
			</a>
		<?php endif; ?>
		<a href="<?php echo $history_url($data); ?>" title="一覧へ">
			<span class="icon-chevron-sign-right mr"></span>一覧へ
		</a>
	</div>

</div>
<!--#news arc box end -->

<!--#estimate start -->
	<div class="estimate">

		<div class="estimateWrap clearfix">

			<div class="estimateTitle">
				<strong>
					発注履歴
				</strong>
				<p>
					<?php echo Common_Util::format_datetime(Arr::get($data, 'order_datetime'), 'Y年m月d日H時i分'); ?>
				</p>
			</div>

			<?php if (Common_Setting::is_price()) : ?>
				<div class="amount">
					<ul>
						<li>
							<strong>
								<span>合計</span><?php echo Common_Util::format_number(Arr::get($data, 'payment_tax')); ?>円
							</strong>
						</li>
						<li>
							<p>
								<span>小計</span><?php echo Common_Util::format_number(Arr::get($data, 'payment')); ?>円
							</p>
						</li>
						<li>
							<p>
								<span>消費税</span><?php echo Common_Util::format_number(Arr::get($data, 'tax')); ?>円
							</p>
						</li>
					</ul>
				</div>
			<?php endif; ?>

			<div class="totalItem">
				<ul>
					<li>
						<strong>
							商品注文合計数
						</strong>
					</li>
					<?php if (Common_Setting::is_case()) : ?>
						<li>
							<p>
								<span>ケース合計</span><?php echo Common_Util::format_number(Arr::get($data, 'amount_case')); ?>
							</p>
						</li>
					<?php endif; ?>
					<li>
						<p>
							<?php if (Common_Setting::is_case()) : ?>
								<span>バラ合計</span>
							<?php endif; ?>
							<?php echo Common_Util::format_number(Arr::get($data, 'amount')); ?>
						</p>
					</li>
				</ul>
			</div>

		</div>

	</div>
	<!--#estimate end -->



<!--#item list start -->
<div class="itemList onlyTxt">

<!--#repeat start -->
<div class="repeat">
	<a href="/order/register/into_cart/<?php echo $data->id ?>" title="履歴と同じ商品をカートへ" class="submit_history_into_cart">
		<span class="icon-chevron-right mr"></span>履歴と同じ商品をカートへ
	</a>
</div>
<!--#repeat end -->

<!--#item box start -->
	<div class="itemBox history">

		<div class="itemBoxWrap">
			<table>
				<thead>
					<tr>
						<th class="w20">商品名</th>
						<th class="w12">入数</th>
						<?php if (Common_Setting::is_price()) : ?>
							<th class="w12">価格</th>
						<?php endif; ?>
						<th class="w12">数量</th>
						<?php if (Common_Setting::is_price()) : ?>
							<th class="w12">小計</th>
						<?php endif; ?>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($data->order_details as $row) : ?>
						<tr>
							<td class="left">
								<em>
									<?php echo Arr::get($row, 'category_name'); ?>
								</em>
								<strong>
									<?php echo Arr::get($row, 'item_name'); ?>
								</strong>
							</td>

							<td class="right">
								<?php if (Common_Setting::is_case()) : ?>
									<p class="hun">
										<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name_case'); ?></i></span>
										<span class="histNums"><?php echo Arr::get($row, 'item_size_case'); ?></span>
									</p>
								<?php endif; ?>
								<p class="hun">
									<?php if (Common_Setting::is_case()) : ?>
										<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name'); ?></i></span>
									<?php endif; ?>
									<span class="histNums"><?php echo Arr::get($row, 'item_size'); ?></span>
								</p>
							</td>

							<?php if (Common_Setting::is_price()) : ?>
								<td class="right">
									<?php if (Common_Setting::is_case()) : ?>
										<p class="hun">
											<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name_case'); ?></i></span>
											<span class="histNums"><?php echo Common_Util::format_number(\Common_Util::add_tax($row['price_case'] * $row['item_size_case'])); ?>円</span>
										</p>
									<?php endif; ?>
									<p class="hun">
										<?php if (Common_Setting::is_case()) : ?>
											<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name'); ?></i></span>
										<?php endif; ?>
										<span class="histNums"><?php echo Common_Util::format_number(\Common_Util::add_tax($row['price'] * $row['item_size'])); ?>円</span>
									</p>
								</td>
							<?php endif; ?>


							<td class="right">
								<?php if (Common_Setting::is_case()) : ?>
									<p class="hun">
										<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name_case'); ?></i></span>
										<span class="histNums"><?php echo Common_Util::format_number(Arr::get($row, 'amount_case')); ?></span>
									</p>
								<?php endif; ?>
								<p class="hun">
									<?php if (Common_Setting::is_case()) : ?>
										<span class="histUnit"><i><?php echo Arr::get($row, 'item_unit_name'); ?></i></span>
									<?php endif; ?>
									<span class="histNums"><?php echo Common_Util::format_number(Arr::get($row, 'amount')); ?></span>
								</p>
							</td>

							<?php if (Common_Setting::is_price()) : ?>
								<td class="right">
									<p>
										<?php echo Common_Util::format_number(Arr::get($row, 'total_tax')); ?>円
									</p>
								</td>
	 						<?php endif; ?>

						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	</div>
	<!--#item box end -->

	<!--#repeat start -->
	<div class="repeat">
		<a href="/order/register/into_cart/<?php echo $data->id ?>" title="履歴と同じ商品をカートへ" class="submit_history_into_cart">
			<span class="icon-chevron-right mr"></span>履歴と同じ商品をカートへ
		</a>
	</div>
	<!--#repeat end -->

</div>
<!--#item list end -->

<!--#ship addr start -->
<div class="deliveryAddr hAddr">
	<strong>
		納品先
	</strong>

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
<!--#ship addr end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		発注タイプ
	</strong>

	<ul>
		<li>
			<p>
				<?php echo Arr::get($data, 'order_type_name'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		出荷予定日
	</strong>

	<ul>
		<li>
			<p>
				<?php echo $shipping_date($data, 'shipping_date'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		納期
	</strong>

	<ul>
		<li>
			<p>
				<?php echo $delivery_date($data, 'delivery_date'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		出荷区分
	</strong>

	<ul>
		<li>
			<p>
				<?php echo Arr::get($data, 'shipping_div_name'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		倉庫
	</strong>

	<ul>
		<li>
			<p>
				<?php echo Arr::get($data, 'warehouse_div_name'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#ship date start -->
<div class="shipDate hDate">
	<strong>
		オーダーNo.
	</strong>

	<ul>
		<li>
			<p>
				<?php echo Arr::get($data, 'order_no'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->

<!--#remarks start -->
<div class="remarks hRemark">
	<strong>
		備考
	</strong>

	<ul>
		<li>
			<p>
				<?php echo $comment($data, 'comment'); ?>
			</p>
		</li>
	</ul>
</div>
<!--#ship date end -->
