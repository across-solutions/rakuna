<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			カート
		</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<strong>
			発注日付は「<span><?php echo date('Y年m月d日'); ?><?php echo Common_Util::add_week(date('Ymd')); ?></span>」です。
		</strong>
		<strong>
			カート内の商品：<label id="count_item"><?php echo number_format($data_count); ?></label>件
		</strong>
		<?php if ($data_count > 0) : ?>
			<p>
				まだ発注は完了していません。発注数、納品希望日、
				備考を確認し、発注するボタンを押してください。
			</p>
		<?php endif; ?>
	</div>
	<!--#page dec end -->

	<?php echo $message(); ?>

	<?php if ($data_count > 0) : ?>
		<!--#buy now start -->
		<div class="buyNow">
			<a href="#order_submit" title="すぐに発注する">
				<span class="icon-chevron-down mr"></span>すぐに発注する
			</a>
		</div>
		<!--#buy now end -->
	<?php endif; ?>

</div>
<!--#boxPadding end -->

<?php if ($data_count > 0) : ?>
	<!--#display option start -->
	<div class="dispOpt borders">
		<ul>
			<li>
				<p>
					表示方法
				</p>
			</li>
			<li>
				<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.normal')); ?>" title="通常表示">
					<?php if ($mode == Config::get('define.search_mode.normal')) : ?>
						<span class="icon-sign-blank selected"></span>
					<?php else : ?>
						<span class="icon-sign-blank"></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.image')); ?>" title="画像表示">
					<?php if ($mode == Config::get('define.search_mode.image')) : ?>
						<span class="icon-th-large selected"></span>
					<?php else : ?>
						<span class="icon-th-large"></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a href="<?php echo Uri::update_query_string('mode', Config::get('define.search_mode.list')); ?>" title="リスト表示">
					<?php if ($mode == Config::get('define.search_mode.list')) : ?>
						<span class="icon-reorder selected"></span>
					<?php else : ?>
						<span class="icon-reorder"></span>
					<?php endif ?>
				</a>
			</li>
		</ul>
	</div>
	<!--#display option end -->

	<?php echo Form::open(array('action' => '/order/register/save', 'id' => 'orderRegistForm')); ?>
		<!--#ship custom start -->
		<div class="shipCustom">
			<div class="shipCustWrap">
				<!--#ship cust item start -->
				<div class="scItem">
					<div class="scItemIn">
						<label class="scChks" for="form_delivery_kind_1">
							<?php echo Form::radio('delivery_kind', 1,
								is_null($data->get_delivery_kind()) || $data->get_delivery_kind() == 1, array('id' => 'form_delivery_kind_1', 'data-delivery-date' => $data->get_delivery_date())); ?>
							<span class="scChksName">自分へ送付</span>
						</label>

						<span class="scIcons">
							<span class="icon-chevron-right"></span>
						</span>
					</div>

					<div class="scItemDesc <?php echo (is_null($data->get_delivery_kind()) || $data->get_delivery_kind() == 1) ? 'disp' : ''; ?>">
						<div class="scidIn">
							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>発注先コード
								</dt>

								<dd>
									<?php echo Form::input('member_code', $data->get_member_code(), array('id' => 'member_code', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_code'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>発注先名
								</dt>

								<dd>
									<?php echo Form::input('member_name', $data->get_member_name(), array('id' => 'member_name', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_name'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>郵便番号(〒)
								</dt>

								<dd>
									<?php echo Form::input('member_zip', $data->get_member_zip(), array('id' => 'member_zip', 'placeholder' => '', 'class' => 'short', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_zip'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所1
								</dt>

								<dd>
									<?php echo Form::input('member_address1', $data->get_member_address1(), array('id' => 'member_address1', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_address1'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所2
								</dt>

								<dd>
									<?php echo Form::input('member_address2', $data->get_member_address2(), array('id' => 'member_address2', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_address2'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所3
								</dt>

								<dd>
									<?php echo Form::input('member_address3', $data->get_member_address3(), array('id' => 'member_address3', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_address3'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>電話番号
								</dt>

								<dd>
									<?php echo Form::input('member_tel', $data->get_member_tel(), array('id' => 'member_tel', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_tel'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>FAX
								</dt>

								<dd>
									<?php echo Form::input('member_fax', $data->get_member_fax(), array('id' => 'member_fax', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('member_fax'); ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
				<!--#ship cust item end -->

				<?php if (!empty($deliveries)) : ?>
				<!--#ship cust item start -->
				<div class="scItem">
					<div class="scItemIn">
						<label class="scChks" for="form_delivery_kind_2">
							<?php echo Form::radio('delivery_kind', 2,
								$data->get_delivery_kind() == 2, array('id' => 'form_delivery_kind_2')); ?>
							<span class="scChksName">納品先へ送付</span>
						</label>

						<span class="scIcons">
							<span class="icon-chevron-right"></span>
						</span>
					</div>

					<div class="scItemDesc  <?php echo ($data->get_delivery_kind() == 2) ? 'disp' : ''; ?>">
						<div class="scidIn">
							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>納品先コード
								</dt>

								<dd>
									<?php echo Form::select('delivery_code', $data->get_delivery_code(), $deliveries, array('id' => 'delivery_select', 'class' => 'select_search short')); ?>
									<?php echo $validate_error_message('delivery_code'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>納品先名
								</dt>

								<dd>
									<?php echo Form::input('delivery_name', $data->get_delivery_name(), array('id' => 'delivery_name', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_name'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>荷受け人名1
								</dt>

								<dd>
									<?php echo Form::input('delivery_receiver_name1', $data->get_delivery_receiver_name1(), array('id' => 'delivery_receiver_name1', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_receiver_name1'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>荷受け人名2
								</dt>

								<dd>
									<?php echo Form::input('delivery_receiver_name2', $data->get_delivery_receiver_name2(), array('id' => 'delivery_receiver_name2', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_receiver_name2'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>郵便番号(〒)
								</dt>

								<dd>
									<?php echo Form::input('delivery_zip', $data->get_delivery_zip(), array('id' => 'delivery_zip', 'placeholder' => '', 'class' => 'short', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_zip'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所1
								</dt>

								<dd>
									<?php echo Form::input('delivery_address1', $data->get_delivery_address1(), array('id' => 'delivery_address1', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_address1'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所2
								</dt>

								<dd>
									<?php echo Form::input('delivery_address2', $data->get_delivery_address2(), array('id' => 'delivery_address2', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_address2'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>住所3
								</dt>

								<dd>
									<?php echo Form::input('delivery_address3', $data->get_delivery_address3(), array('id' => 'delivery_address3', 'placeholder' => '', 'class' => 'long', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_address3'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>電話番号
								</dt>

								<dd>
									<?php echo Form::input('delivery_tel', $data->get_delivery_tel(), array('id' => 'delivery_tel', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_tel'); ?>
								</dd>
							</dl>

							<dl class="scfBox">
								<dt>
									<span class="icon-caret-right mr"></span>FAX
								</dt>

								<dd>
									<?php echo Form::input('delivery_fax', $data->get_delivery_fax(), array('id' => 'delivery_fax', 'placeholder' => '', 'class' => 'middle', 'readonly' => 'readonly')); ?>
									<?php echo $validate_error_message('delivery_fax'); ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
				<!--#ship cust item end -->
				<?php endif ; ?>

			</div>
		</div>
		<!--#ship custom end -->
		<?php foreach ($data->get_order_types() as $order_type) : ?>
			<?php if ($data->selectable_delivery_date()) : ?>
				<?php echo Form::hidden('delivery_date' . $order_type, $data->get_order_type_delivery_date($order_type), array('id' => 'hidden_form_delivery_date_select' . $order_type, 'class' => 'js-delivery-date-select')); ?>
			<?php endif; ?>
			<?php echo Form::hidden('order_no' . $order_type, $data->get_order_type_order_no($order_type), array('id' => 'hidden_form_order_no' . $order_type)); ?>
			<?php echo Form::hidden('comment' . $order_type, $data->get_order_type_comment($order_type), array('id' => 'hidden_form_comment' . $order_type)); ?>
		<?php endforeach; ?>
	<?php echo Form::close(); ?>

	<?php foreach ($rows as $order_type => $order_type_rows) : ?>
		<!--#estimate start -->
		<div class="estimate">

			<div class="estimateWrap clearfix">

				<div class="estimateTitle">
					<strong>
						発注内容：<?php echo $order_types[$order_type]; ?>
					</strong>
				</div>
				<?php if (Common_Setting::is_price()) : ?>
					<div class="amount">
						<ul>
							<li>
								<strong>
									<span>合計</span><label id="payment_tax<?=$order_type ?>"><?php echo number_format($data->get_order_type_payment_tax($order_type)); ?></label>円
								</strong>
							</li>
							<li>
								<p>
									<span>小計</span><label id="payment<?=$order_type ?>"><?php echo number_format($data->get_order_type_payment($order_type)); ?></label>円
								</p>
							</li>
							<li>
								<p>
									<span>消費税</span><label id="tax<?=$order_type ?>"><?php echo number_format($data->get_order_type_tax($order_type)); ?></label>円
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
									<span>ケース合計</span><label id="total_amount_case<?=$order_type ?>"><?php echo number_format($data->get_order_type_amount_case($order_type)); ?></label>
								</p>
							</li>
						<?php endif; ?>
						<li>
							<p>
								<?php if (Common_Setting::is_case()) : ?>
									<span>バラ合計</span>
								<?php endif; ?>
								<label id="total_amount<?=$order_type ?>"><?php echo number_format($data->get_order_type_amount($order_type)); ?></label>
							</p>
						</li>
					</ul>
				</div>

			</div>

		</div>
		<!--#estimate end -->

		<?php if ($mode == Config::get('define.search_mode.normal')) : ?>
			<?php echo View::forge('item/view/normal', array('rows' => $order_type_rows)); ?>
		<?php elseif ($mode == Config::get('define.search_mode.image')) : ?>
			<?php echo View::forge('item/view/image', array('rows' => $order_type_rows)); ?>
		<?php elseif ($mode == Config::get('define.search_mode.list')) : ?>
			<?php echo View::forge('item/view/list', array('rows' => $order_type_rows, 'sort' => false)); ?>
		<?php endif; ?>

		<?php if (false) : ?>
			<!--#ship date start -->
			<div class="shipDate">
				<strong>
					発注タイプ
				</strong>

				<ul>
					<li>
						<div class="deliveryWrap">
							<?php echo Form::select('order_type', $data->get_order_type(), $order_types, array('id' => 'order_type_select')); ?>
							<?php echo $validate_error_message('order_type'); ?>
						</div>
					</li>
				</ul>
			</div>
			<!--#ship date end -->

			<!--#ship date start -->
			<div class="shipDate">
				<strong>
					出荷区分
				</strong>

				<ul>
					<li>
						<div class="deliveryWrap">
							<?php echo Form::select('shipping_div', $data->get_shipping_div(), $shipping_div, array('id' => 'shipping_div_select')); ?>
							<?php echo $validate_error_message('shipping_div'); ?>
						</div>
					</li>
				</ul>
			</div>
			<!--#ship date end -->

			<!--#ship date start -->
			<div class="shipDate">
				<strong>
					倉庫
				</strong>

				<ul>
					<li>
						<div class="deliveryWrap">
							<?php echo Form::select('warehouse_div', $data->get_warehouse_div(), $warehouse_div, array('id' => 'warehouse_div_select')); ?>
							<?php echo $validate_error_message('warehouse_div'); ?>
						</div>
					</li>
				</ul>
			</div>
			<!--#ship date end -->

			<!--#ship date start -->
			<div class="shipDate">
				<strong>
					出荷予定日
				</strong>

				<ul>
					<li>
						<div class="deliveryWrap">
							<?php echo Form::select('shipping_date', $data->get_shipping_date(), $shipping_dates, array('id' => 'shipping_date_select')); ?>
							<?php echo $validate_error_message('shipping_date'); ?>
						</div>
					</li>
				</ul>
			</div>
			<!--#ship date end -->
		<?php endif; ?>

		<?php if ($data->selectable_delivery_date()) : ?>
		<!--#ship date start -->
		<div class="shipDate">
			<strong>
				納期
			</strong>

			<ul>
				<li>
					<div class="deliveryWrap">
						<?php echo Form::select('delivery_date' . $order_type, $data->get_order_type_delivery_date($order_type), $delivery_dates, array('id' => 'delivery_date_select' . $order_type, 'class' => 'js-delivery-date-select')); ?>
					</div>
					<p>
						納期がある場合はご指定ください。
					</p>
					<em>
						※ご希望に添えない場合もございます。ご了承ください。
					</em>
					<?php echo $validate_error_message('delivery_date' . $order_type); ?>
				</li>
			</ul>
		</div>
		<!--#ship date end -->
		<?php endif; ?>

		<!--#ship date start -->
		<div class="shipDate">
			<strong>
				オーダーNo.
			</strong>

			<ul>
				<li>
					<div class="deliveryWrap">
						<?php echo Form::input('order_no' . $order_type, $data->get_order_type_order_no($order_type), array('id' => 'order_no' . $order_type, 'placeholder' => '', 'class' => 'js-order-form-text')); ?>
						<?php echo $validate_error_message('order_no' . $order_type); ?>
					</div>
				</li>
			</ul>
		</div>
		<!--#ship date end -->

		<!--#remarks start -->
		<div class="remarks">
			<strong>
				備考
			</strong>

			<ul>
				<li>
					<p>
						伝言事項等々ございましたら、ご記入ください。
						備考欄に発注内容をご記入いただいても、処理はされませんので
						ご注意ください。
					</p>
				</li>
				<li>
					<?php echo Form::input('comment' . $order_type, $data->get_order_type_comment($order_type), array('id' => 'comment' . $order_type, 'placeholder' => '', 'class' => 'js-order-form-text')); ?>
					<?php echo $validate_error_message('comment' . $order_type); ?>
				</li>
			</ul>
		</div>
		<!--#ship date end -->
	<?php endforeach; ?>

	<!--#order done start -->
	<div class="orderDone">

		<a id="order_submit" href="#" title="発注する">
			<span class="icon-ok mr"></span>発注する
		</a>

	</div>
	<!--#order done end -->
<?php endif; ?>
