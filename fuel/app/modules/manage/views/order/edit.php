<?php $order = $data["order"]; ?>
<?php $new_order_details = $data["new_order_details"]; ?>

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
		<?php echo $format_date($order, 'order_datetime', 'Y年m月d日H時i分'); ?>
	</p>
</div>
<!--#dig text end -->

<!--#dig user info start -->
<div class="digUserInfo">
	<strong>
		<?php echo Arr::get($order, 'member_name'); ?>
	</strong>
	<p>
		コード:<?php echo Arr::get($order, 'member_code'); ?>
	</p>
</div>
<!--#dig user info end -->

<!--#dig result start -->
<div class="digResult clearfix">

	<?php if (Common_Setting::is_price()) : ?>
		<div class="digAmount">
			<strong>
				合計<span><?php echo Common_Util::format_number(Arr::get($order, 'payment_tax')); ?>円</span>
			</strong>
		</div>
	<?php endif; ?>

	<div class="digTotal">
		<strong>商品注文合計数</strong>
		<?php if (Common_Setting::is_case()) : ?>
			<p>
				ケース合計<span><?php echo Common_Util::format_number(Arr::get($order, 'amount_case')); ?></span>
			</p>
		<?php endif; ?>
		<p>
			<?php if (Common_Setting::is_case()) : ?>
				バラ合計
			<?php endif; ?>
			<span><?php echo Common_Util::format_number(Arr::get($order, 'amount')); ?></span>
		</p>
	</div>

</div>
<!--#dig result end -->

<div class="digNav editOrderAddItem">
	<p class="new_order_add_item_error errorMessage">
	</p>
	<ul class="digNavAlignLeft">
		<li>
			商品コード：<?php echo Form::input('add_item_code', '', array('id' => 'order_add_item_code' )); ?>
		</li>
		<li>
			<a href="" title="" class="edit_order_add_item">
				<span class="icon-plus mr"></span>商品の追加
			</a>
		</li>
	</ul>
</div>

<?php echo Form::open('/manage/order/edit_save', array('id' => Arr::get($order, 'id'))); ?>
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
			<?php foreach($order->order_details as $row) : ?>
				<?php echo Presenter::forge('base', 'view', null,
											View::forge('order/edit_row', array('row' => $row))
				);?>
			<?php endforeach ?>

			<?php foreach($new_order_details as $row) : ?>
				<?php echo Presenter::forge('base', 'view', null,
											View::forge('order/edit_row', array('row' => $row))
				);?>
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
			<?php echo $delivery_date($order, 'delivery_date'); ?>
		</dd>
	</dl>
</div>
<!--#dig comment end -->

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
							<?php echo Arr::get($order, 'delivery_code'); ?>
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
							<?php echo Arr::get($order, 'delivery_name'); ?>
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
							<?php echo Arr::get($order, 'delivery_zip'); ?>
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
							<?php echo Arr::get($order, 'delivery_address1'); ?>
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
							<?php echo Arr::get($order, 'delivery_address2'); ?>
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
							<?php echo Arr::get($order, 'delivery_address3'); ?>
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
							<?php echo Arr::get($order, 'delivery_tel'); ?>
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
							<?php echo Arr::get($order, 'delivery_fax'); ?>
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
			備考
		</dt>
		<dd>
			<?php echo $comment($order, 'comment'); ?>
		</dd>
	</dl>
</div>
<!--#dig comment end -->

<!--#dig nav start -->
<div class="digNav">
	<ul>
		<li>
			<a href="#" title="変更して保存" class="submit">
				<span class="icon-save mr"></span>変更して保存
			</a>
		</li>
		<li>
			<a href="/manage/order/delete_save" title="受注データを削除" class="submit_delete">
				<span class="icon-trash mr"></span>受注データを削除
			</a>
		</li>
		<li>
			<a href="#" title="閉じる" class="close">
				<span class="icon-remove mr"></span>閉じる
			</a>
		</li>
	</ul>
</div>
<!--#dig nav end -->
<?php echo Form::close(); ?>
