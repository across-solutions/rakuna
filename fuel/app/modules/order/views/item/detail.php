<!--#dig main start -->
<div class="digMain">

	<div class="digTitle">
		<strong>
			<span class="icon-file mr"></span>商品詳細
		</strong>

		<div class="digClose">
			<a href="#" title="閉じる" class="close">
				<span class="icon-remove"></span>
			</a>
		</div>
	</div>

	<div class="digzTable">

		<!--#dig zoom image start -->
		<div class="digzImg">
			<?php echo Image_Item::img(Arr::get($data, 'code')); ?>

			<?php if (PDF_Item::exist(Arr::get($data, 'code'))) : ?>
				<div class="itemPdfDownload">
					<a target="_blank" href="<?php echo PDF_Item::url(Arr::get($data, 'code')); ?>">
						<span class="icon-file mr"></span> 商品PDF表示
					</a>
				</div>
			<?php endif; ?>
			<div class="counts">
				<div class="buttonWrap">

					<?php $unit_name_case = Arr::get($data, 'unit_name_case'); ?>
					<?php $hidden_flg_case = Arr::get($data, 'hidden_flg_case'); ?>
					<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
						<?php if (Common_Setting::is_case()) : ?>
							<div class="buttons">
							<input class="main_set" type="hidden" value="amount_case<?php echo Arr::get($data, 'id'); ?>">
								<strong><?php echo Arr::get($data, 'unit_name_case'); ?></strong>
								<ul>
									<li>
										<input class="amount" type="text" size="2" value="<?php echo Arr::get($data, 'amount_case', 0); ?>" href="/order/ajax/cart/update_case/<?php echo Arr::get($data, 'id'); ?>.json">
									</li>
									<li>
										<a href="/order/ajax/cart/plus_case/<?php echo Arr::get($data, 'id'); ?>.json" title="プラス" class="plus item_modify wave">
											<span class="ring"></span>
											<span class="icon-plus"></span>
											<b class="case">C</b>
										</a>
									</li>
									<li>
										<a href="/order/ajax/cart/minus_case/<?php echo Arr::get($data, 'id'); ?>.json" title="マイナス" class="minus item_modify wave">
											<span class="ring"></span>
											<span class="icon-minus"></span>
											<b class="case">C</b>
										</a>
									</li>
									<li>
										<a href="/order/ajax/cart/del_case/<?php echo Arr::get($data, 'id'); ?>.json" title="ごみ箱" class="item_modify wave">
											<span class="ring"></span>
											<span class="icon-trash"></span>
											<b class="case">C</b>
										</a>
									</li>
								</ul>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php $unit_name = Arr::get($data, 'unit_name'); ?>
					<?php $hidden_flg_single = Arr::get($data, 'hidden_flg_single'); ?>
					<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
						<div class="buttons">
						<input class="main_set" type="hidden" value="amount<?php echo Arr::get($data, 'id'); ?>">
							<?php if (Common_Setting::is_case()) : ?>
								<strong><?php echo Arr::get($data, 'unit_name'); ?></strong>
							<?php endif; ?>
							<ul>
								<li>
									<input class="amount" type="text" size="2" value="<?php echo Arr::get($data, 'amount', 0); ?>" href="/order/ajax/cart/update/<?php echo Arr::get($data, 'id'); ?>.json">
								</li>
								<li>
									<a href="/order/ajax/cart/plus/<?php echo Arr::get($data, 'id'); ?>.json" title="プラス" class="plus item_modify wave">
										<span class="ring"></span>
										<span class="icon-plus"></span>
										<b class="bara">B</b>
									</a>
								</li>
								<li>
									<a href="/order/ajax/cart/minus/<?php echo Arr::get($data, 'id'); ?>.json" title="マイナス" class="minus item_modify wave">
										<span class="ring"></span>
										<span class="icon-minus"></span>
										<b class="bara">B</b>
									</a>
								</li>
								<li>
									<a href="/order/ajax/cart/del/<?php echo Arr::get($data, 'id'); ?>.json" title="ごみ箱" class="item_modify wave">
										<span class="ring"></span>
										<span class="icon-trash"></span>
										<b class="bara">B</b>
									</a>
								</li>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!--#dig zoom image end -->

		<div class="digzRight">

			<!--#dig zoom item name start -->
			<div class="digzItemName">
				<dl>
					<dt>
						<?php echo Arr::get($data, 'item_categories.name'); ?>
					</dt>
					<dd>
						<?php echo Arr::get($data, 'name'); ?>
					</dd>
				</dl>
			</div>
			<!--#dig zoom item name end -->

			<!--#dig zoom dec start -->
			<div class="digzDec">
				<dl>
					<dt>
						説明
					</dt>
					<dd>
						<?php echo nl2br(Arr::get($data, 'comment')); ?>
					</dd>
				</dl>
			</div>
			<!--#dig zoom dec end -->

			<!--#dig zoom dec start -->
			<div class="digzDec">
				<dl>
					<dt>
						入数
					</dt>
					<dd>
						<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
							<?php if (Common_Setting::is_case()) : ?>
								<p>
									<span><?php echo Arr::get($data, 'unit_name_case'); ?></span>
									<?php echo Arr::get($data, 'size_case'); ?>
								</p>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
							<p>
								<?php if (Common_Setting::is_case()) : ?>
									<span><?php echo Arr::get($data, 'unit_name'); ?></span>
								<?php endif; ?>
								<?php echo Arr::get($data, 'size'); ?>
							</p>
						<?php endif; ?>
					</dd>
				</dl>
			</div>
			<!--#dig zoom dec end -->

			<?php if (Common_Setting::is_price()) : ?>
				<!--#dig zoom dec start -->
				<div class="digzDec">
					<dl>
						<dt>
							価格
						</dt>
						<dd>
							<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
								<?php if (Common_Setting::is_case()) : ?>
									<p>
										<span><?php echo Arr::get($data, 'unit_name_case'); ?></span>
										<?php echo Common_Util::format_number(Arr::get($data, 'price_case_tax')); ?>円
									</p>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
								<p>
									<?php if (Common_Setting::is_case()) : ?>
										<span><?php echo Arr::get($data, 'unit_name'); ?></span>
									<?php endif; ?>
									<?php echo Common_Util::format_number(Arr::get($data, 'price_tax')); ?>円
								</p>
							<?php endif; ?>
						</dd>
					</dl>
				</div>
				<!--#dig zoom dec end -->

				<?php if (Arr::get($data, 'type') == Config::get('define.item_type.order') || Arr::get($data, 'type') == Config::get('define.item_type.special')) : ?>
					<dl class="digzDec">
						<dd>
							取り寄せのため、お届けに日数がかかります
						</dd>
					</dl>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<!--#dig main end -->