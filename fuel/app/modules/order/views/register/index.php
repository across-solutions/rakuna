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

	<!--#estimate start -->
	<div class="estimate">

		<div class="estimateWrap clearfix">

			<div class="estimateTitle">
				<strong>
					発注内容
				</strong>
			</div>
			<?php if (Common_Setting::is_price()) : ?>
				<div class="amount">
					<ul>
						<li>
							<strong>
								<span>合計</span><label id="payment_tax"><?php echo number_format($data->get_payment_tax()); ?></label>円
							</strong>
						</li>
						<li>
							<p>
								<span>小計</span><label id="payment"><?php echo number_format($data->get_payment()); ?></label>円
							</p>
						</li>
						<li>
							<p>
								<span>消費税</span><label id="tax"><?php echo number_format($data->get_tax()); ?></label>円
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
								<span>ケース合計</span><label id="total_amount_case"><?php echo number_format($data->get_total_amount_case()); ?></label>
							</p>
						</li>
					<?php endif; ?>
					<li>
						<p>
							<?php if (Common_Setting::is_case()) : ?>
								<span>バラ合計</span>
							<?php endif; ?>
							<label id="total_amount"><?php echo number_format($data->get_total_amount()); ?></label>
						</p>
					</li>
				</ul>
			</div>

		</div>

	</div>
	<!--#estimate end -->

	<?php if ($mode == Config::get('define.search_mode.normal')) : ?>
		<?php echo View::forge('item/view/normal', array('rows' => $rows)); ?>
	<?php elseif ($mode == Config::get('define.search_mode.image')) : ?>
		<?php echo View::forge('item/view/image', array('rows' => $rows)); ?>
	<?php elseif ($mode == Config::get('define.search_mode.list')) : ?>
		<?php echo View::forge('item/view/list', array('rows' => $rows, 'sort' => false)); ?>
	<?php endif; ?>

	<?php echo Form::open('/order/register/save'); ?>
		<!--#ship date start -->
		<div class="shipDate">
			<strong>
				納品希望日
				<?php echo $validate_error_message('delivery_date_check'); ?>
			</strong>

			<ul>
				<li>
					<div class="deliveryWrap">
						<?php echo Form::select('delivery_date', $data->get_delivery_date(), $dates, array('id' => 'delivery_date_select')); ?>
					</div>
					<p>
						納品希望日がある場合はご指定ください。
					</p>
					<em>
						※ご希望に添えない場合もございます。ご了承ください。
					</em>
					<?php echo $validate_error_message('delivery_date'); ?>
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
					<?php echo Form::textarea('comment', $data->get_comment()); ?>
					<?php echo $validate_error_message('comment'); ?>
				</li>
			</ul>
		</div>
		<!--#ship date end -->

		<!--#order done start -->
		<div class="orderDone">

			<a id="order_submit" href="#" title="発注する">
				<span class="icon-ok mr"></span>発注する
			</a>

		</div>
		<!--#order done end -->
	<?php echo Form::close(); ?>
<?php endif; ?>
