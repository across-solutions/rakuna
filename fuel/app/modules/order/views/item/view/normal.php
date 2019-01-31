<!--#item list start -->
<?php if (isset($single) && $single) : ?>
<div class="itemList single">
<?php else : ?>
<div class="itemList">
<?php endif; ?>
	<ul>
		<?php foreach($rows as $row) : ?>
			<!--#item box start -->
			<li id="item_<?php echo Arr::get($row, 'code'); ?>" class="lineup">
				<div class="pd">
					<div class="itemBox clearfix">
						<div class="items">
							<div class="itemImg">
								<a href="/order/item/detail/<?php echo Arr::get($row, 'id'); ?>" title="<?php echo Arr::get($row, 'name'); ?>" class="dialog">
									<?php echo Image_Item::img(Arr::get($row, 'code'), array('class' => 'lineupimage')); ?>
								</a>
							</div>

							<div class="itemDec">

								<div class="itemName lineup">
									<em>
										<?php echo Arr::get($row, 'category_name'); ?>
									</em>
									<strong>
										<?php echo Arr::get($row, 'name'); ?>
									</strong>
									<p>
										<?php echo nl2br(Arr::get($row, 'comment')); ?>
									</p>
								</div>

								<?php if (is_null(Arr::get($row, 'favorite_id'))) : ?>
									<div class="fav">
										<p class="label_fav">
										星ボタンをタップでお気に入りに登録できます。
										</p>
										<a class="item_favorite" href="/order/ajax/favorite/toggle/<?php echo Arr::get($row, 'code'); ?>.json" title="お気に入り">
											<span class="icon-star-empty"></span>
										</a>
									</div>
								<?php else : ?>
									<div class="fav">
										<p class="label_fav">
										星ボタンをタップでお気に入りを解除できます。
										</p>
										<a class="item_favorite" href="/order/ajax/favorite/toggle/<?php echo Arr::get($row, 'code'); ?>.json" title="お気に入り">
											<span class="icon-star"></span>
										</a>
									</div>
								<?php endif; ?>

								<div class="numbers">

									<dl class="clearfix">
										<dt>
											入数
										</dt>
										<dd>
											<?php $hidden_flg_case = Arr::get($row, 'hidden_flg_case'); ?>
											<?php $unit_name_case = Arr::get($row, 'unit_name_case'); ?>
											<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
												<?php if (Common_Setting::is_case()) : ?>
													<p>
														<span><?php echo Arr::get($row, 'unit_name_case'); ?></span>
														<?php echo Arr::get($row, 'size_case'); ?>
													</p>
												<?php endif; ?>
											<?php endif; ?>
											<?php $hidden_flg_single = Arr::get($row, 'hidden_flg_single'); ?>
											<?php $unit_name = Arr::get($row, 'unit_name'); ?>
											<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
												<p>
													<?php if (Common_Setting::is_case()) : ?>
														<span><?php echo Arr::get($row, 'unit_name'); ?></span>
													<?php endif; ?>
													<?php echo Arr::get($row, 'size'); ?>
												</p>
											<?php endif; ?>
										</dd>
									</dl>
									<?php if (Common_Setting::is_price()) : ?>
										<dl class="clearfix">
											<dt>
												価格
											</dt>
											<dd>
												<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
													<?php if (Common_Setting::is_case()) : ?>
														<p>
															<span><?php echo Arr::get($row, 'unit_name_case'); ?></span>
															<?php echo Common_Util::format_number(Arr::get($row, 'price_case_tax')); ?>円
														</p>
													<?php endif; ?>
												<?php endif; ?>
												<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
													<p>
														<?php if (Common_Setting::is_case()) : ?>
															<span><?php echo Arr::get($row, 'unit_name'); ?></span>
														<?php endif; ?>
														<?php echo Common_Util::format_number(Arr::get($row, 'price_tax')); ?>円
													</p>
												<?php endif; ?>
											</dd>
										</dl>
									<?php endif; ?>
									<?php if (Arr::get($row, 'type') == Config::get('define.item_type.order') || Arr::get($row, 'type') == Config::get('define.item_type.special')) : ?>
										<dl class="clearfix">
											<dt>
												取り寄せのため、お届けに日数がかかります
											</dt>
											<dd>
											</dd>
										</dl>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="counts">
							<div class="buttonWrap">
								<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
									<?php if (Common_Setting::is_case()) : ?>
										<div class="buttons">
											<strong><?php echo $unit_name_case; ?></strong>
											<ul>
												<li>
													<input id="amount_case<?php echo Arr::get($row, 'id'); ?>" class="amount" type="text" size="2" value="<?php echo Arr::get($row, 'amount_case', 0); ?>" href="/order/ajax/cart/update_case/<?php echo Arr::get($row, 'id'); ?>.json">
												</li>
												<li>
													<a href="/order/ajax/cart/plus_case/<?php echo Arr::get($row, 'id'); ?>.json" title="プラス" class="plus item_modify wave">
														<span class="ring"></span>
														<span class="icon-plus"></span>
														<b class="case">C</b>
													</a>
												</li>
												<li>
													<a href="/order/ajax/cart/minus_case/<?php echo Arr::get($row, 'id'); ?>.json" title="マイナス" class="minus item_modify wave">
														<span class="ring"></span>
														<span class="icon-minus"></span>
														<b class="case">C</b>
													</a>
												</li>
												<li>
													<a href="/order/ajax/cart/del_case/<?php echo Arr::get($row, 'id'); ?>.json" title="ごみ箱" class="item_modify wave">
														<span class="ring"></span>
														<span class="icon-trash"></span>
														<b class="case">C</b>
													</a>
												</li>
											</ul>
										</div>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
									<div class="buttons">
										<?php if (Common_Setting::is_case()) : ?>
											<strong><?php echo Arr::get($row, 'unit_name'); ?></strong>
										<?php endif; ?>
										<ul>
											<li>
												<input id="amount<?php echo Arr::get($row, 'id'); ?>" class="amount" type="text" size="2" value="<?php echo Arr::get($row, 'amount', 0); ?>" href="/order/ajax/cart/update/<?php echo Arr::get($row, 'id'); ?>.json">
											</li>
											<li>
												<a href="/order/ajax/cart/plus/<?php echo Arr::get($row, 'id'); ?>.json" title="プラス" class="plus item_modify wave">
													<span class="ring"></span>
													<span class="icon-plus"></span>
													<b class="bara">B</b>
												</a>
											</li>
											<li>
												<a href="/order/ajax/cart/minus/<?php echo Arr::get($row, 'id'); ?>.json" title="マイナス" class="minus item_modify wave">
													<span class="ring"></span>
													<span class="icon-minus"></span>
													<b class="bara">B</b>
												</a>
											</li>
											<li>
												<a href="/order/ajax/cart/del/<?php echo Arr::get($row, 'id'); ?>.json" title="ごみ箱" class="item_modify wave">
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
				</div>
			</li>
			<!--#item box end -->
		<?php endforeach; ?>
	</ul>
</div>
<!--#item list end -->

<div style="display:none;">
	<!--#content start -->
	<div class="content" id="supportSearch">

		<!--#dig main start -->
		<div class="digMain">

			<div class="digTitle">
				<strong>
					<span class="icon-picture mr"></span>画像検索
				</strong>

				<div class="digClose">
					<a href="#" title="#" class="close">
						<span class="icon-remove"></span>
					</a>
				</div>

			</div>


			<div class="digImgList">
				<?php foreach($rows as $row) : ?>
					<a href="#item_<?php echo Arr::get($row, 'code'); ?>" title="<?php echo Arr::get($row, 'name'); ?>" class="link_close">
						<?php echo Image_Item::img(Arr::get($row, 'code')); ?>
					</a>
				<?php endforeach; ?>
			</div>

		</div>
		<!--#dig main end -->

	</div>
	<!--#contentend -->
</div>