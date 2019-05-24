<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			発注履歴
		</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<strong>
			発注履歴について
		</strong>
		<p>
			発注日付を選択する事で、発注内容の確認が行えます。
		</p>
	</div>
	<!--#page dec end -->

</div>
<!--#boxPadding end -->

<div class="histBox">
	<!--#history search start -->
	<div class="histSearch">
		<?php echo Form::open(array('action' => '/order/history', 'method' => 'get')); ?>
			<!--#sub title start -->
			<div class="subTitle">
				<strong>
					発注履歴を検索する
				</strong>
			</div>
			<!--#sub title end -->

			<div class="formSelect sub">
				<span class="formSelectSubWrap w45"><?php echo Form::select('year', $year, $years, array('class' => '')); ?></span>年
				<span class="formSelectSubWrap w20"><?php echo Form::select('month', $month, $months, array('class' => '')); ?></span>月
			</div>

			<!--#buy now start -->
			<div class="buyNow">
				<a href="#" title="履歴を検索する" class="submit">
					<span class="icon-search mr"></span>履歴を検索する
				</a>
			</div>
			<!--#buy now end -->
		<?php echo Form::close(); ?>
	</div>
	<!--#history search end -->

	<?php if (count($rows) > 0) : ?>
		<!--#history nav start -->
		<div class="histNav">
			<?php foreach ($rows as $date => $orders) : ?>
				<ul>
					<li id="<?php echo $date; ?>">
						<span class="trigger">
							<strong>
								<?php echo substr($date, 0, 4); ?>年<?php echo substr($date, 4, 2); ?>月<?php echo substr($date, 6, 2); ?>日<?php echo Common_Util::add_week($date); ?>
							</strong>
							<span class="icon-chevron-right"></span>
						</span>
						<ul>
							<?php foreach ($orders as $row) : ?>
							<li <?php if ($cancelled($row)) : ?>class="delFlg"<?php endif; ?>>
								<a href="/order/history/detail/<?php echo $row->id; ?>" title="<?php echo Common_Util::format_datetime(Arr::get($row, 'order_datetime'), 'Y年m月d日H時i分'); ?>">
									<span class="icon-angle-right mr"></span>
									<i class="histItemTime"><?php echo Common_Util::format_datetime(Arr::get($row, 'order_datetime'), 'Y年m月d日H時i分'); ?></i>
									<?php if ($cancelled($row)) : ?>
										<span class="cansel">
											キャンセル済
										</span>
									<?php else : ?>
										<?php if (Common_Setting::is_price()) : ?>
											<span class="price">
												<?php echo Common_Util::format_number(Arr::get($row, 'payment_tax')); ?>円
											</span>
										<?php endif; ?>
									<?php endif; ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
					</li>
				</ul>
			<?php endforeach; ?>
		</div>
		<!--#history nav end -->
	<?php else : ?>
		<div class="boxPadding">
			<p class="info">
				指定された条件では発注履歴はみつかりませんでした
			</p>
		</div>
	<?php endif; ?>
</div>
