<!--#item list start -->
<div class="itemList onlyTxt">

	<?php echo Form::open(Uri::create('/order/favorite/index', array(), Input::get())); ?>

	<!--#item box start -->
	<div class="itemBox">

		<?php if ($sort) : ?>
			<div class="sortDone accordionHide">
				<a class="submit" title="並び順を更新する" href="#">
					<span class="icon-save mr"></span>並び順を更新する
				</a>
			</div>
		<?php endif; ?>

		<div class="itemBoxWrap">
			<table>
				<thead>
					<tr>
						<th class="listItemName">商品名</th>
						<th class="listSize spOnly">入数</th>
						<?php if (Common_Setting::is_price()) : ?>
							<th class="listPrice spOnly">価格</th>
						<?php endif; ?>
						<th class="listFavorite spOnly">お気に入り</th>
						<?php if (Common_Setting::is_case()) : ?>
							<th class="itemCaseBara">ケース<br>バラ</th>
						<?php else : ?>
							<th class="itemCaseBara">数量</th>
						<?php endif; ?>
						<?php if ($sort) : ?>
							<th class="listFavSort spOnly">順番</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($rows as $key => $row) : ?>
						<?php $hidden_flg_single = Arr::get($row, 'hidden_flg_single'); ?>
						<?php $hidden_flg_case = Arr::get($row, 'hidden_flg_case'); ?>
						<?php $unit_name_case = Arr::get($row, 'unit_name_case'); ?>
						<?php $unit_name = Arr::get($row, 'unit_name'); ?>

						<tr id="item_<?php echo Arr::get($row, 'code'); ?>">
							<td class="left">
								<em>
									<?php echo Arr::get($row, 'category_name'); ?>
								</em>
								<strong>
									<?php echo Arr::get($row, 'name'); ?>
								</strong>
								<div class="list-accordion">
									<span class="icon-chevron-right"></span>
								</div>
							</td>

							<td class="right spOnly">
								<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
									<?php if (Common_Setting::is_case()) : ?>
										<p class="hun">
											<span class="histUnit"><i><?php echo Arr::get($row, 'unit_name_case'); ?></i></span>
											<span class="histNums"><?php echo Arr::get($row, 'size_case'); ?></span>
										</p>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
									<p class="hun">
										<?php if (Common_Setting::is_case()) : ?>
											<span class="histUnit"><i><?php echo Arr::get($row, 'unit_name'); ?></i></span>
										<?php endif; ?>
										<span class="histNums"><?php echo Arr::get($row, 'size'); ?></span>
									</p>
								<?php endif; ?>
							</td>

							<?php if (Common_Setting::is_price()) : ?>
								<td class="right spOnly">
									<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
										<?php if (Common_Setting::is_case()) : ?>
											<p class="hun">
												<span class="histUnit"><i><?php echo Arr::get($row, 'unit_name_case'); ?></i></span>
												<span class="histNums"><?php echo Common_Util::format_number(Arr::get($row, 'price_case_tax')); ?>円</span>
											</p>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
										<p class="hun">
											<?php if (Common_Setting::is_case()) : ?>
												<span class="histUnit"><i><?php echo Arr::get($row, 'unit_name'); ?></i></span>
											<?php endif; ?>
											<span class="histNums"><?php echo Common_Util::format_number(Arr::get($row, 'price_tax')); ?>円</span>
										</p>
									<?php endif; ?>
								</td>
							<?php endif; ?>

							<td class="center spOnly">
								<?php if (is_null(Arr::get($row, 'favorite_id'))) : ?>
									<div class="fav">
										<?php if (Common_Member::is_agency()) : ?>
											<a class="item_favorite" href="/order/ajax/favorite/toggle/<?php echo Arr::get($row, 'code'); ?>.json" title="お気に入り">
										<?php else : ?>
											<a class="" href="#" title="お気に入り">
										<?php endif; ?>
											<span class="icon-star-empty"></span>
										</a>
									</div>
								<?php else : ?>
									<div class="fav">
										<?php if (Common_Member::is_agency()) : ?>
											<a class="item_favorite" href="/order/ajax/favorite/toggle/<?php echo Arr::get($row, 'code'); ?>.json" title="お気に入り">
										<?php else : ?>
											<a class="" href="#" title="お気に入り">
										<?php endif; ?>
											<span class="icon-star"></span>
										</a>
									</div>
								<?php endif; ?>
							</td>

							<td class="center">
								<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
									<?php if (Common_Setting::is_case()) : ?>
										<div class="buttons buttonsLatent">
											<strong><?php echo Arr::get($row, 'unit_name_case'); ?></strong>
											<ul>
												<li>
													<input class="amount" type="text" size="2" value="<?php echo Arr::get($row, 'amount_case', 0); ?>" href="/order/ajax/cart/update_case/<?php echo Arr::get($row, 'id'); ?>.json">
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
												<li class="listViewTrashButton">
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
									<div class="buttons buttonsLatent">
										<?php if (Common_Setting::is_case()) : ?><strong class="strongBara"><?php echo Arr::get($row, 'unit_name'); ?></strong><?php endif; ?>
										<ul>
											<li>
												<input class="amount" type="text" size="2" value="<?php echo Arr::get($row, 'amount', 0); ?>" href="/order/ajax/cart/update/<?php echo Arr::get($row, 'id'); ?>.json">
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
											<li class="listViewTrashButton">
												<a href="/order/ajax/cart/del/<?php echo Arr::get($row, 'id'); ?>.json" title="ごみ箱" class="item_modify wave">
													<span class="ring"></span>
													<span class="icon-trash"></span>
													<b class="bara">B</b>
												</a>
											</li>
										</ul>
									</div>
								<?php endif; ?>
							</td>

							<?php if ($sort) : ?>
								<td class="center spOnly">
									<span class="sortNums">
										<?php echo $page_index + $key + 1; ?>
									</span>

									<?php echo Form::input('sort_num[' . Arr::get($row, 'favorite_id') . ']', Arr::get($sort_num, Arr::get($row, 'favorite_id')), array('class' => 'sortNum ' . $validate_error_class('sort_num.' . Arr::get($row, 'favorite_id')), 'size' => '2')); ?>
								</td>
							<?php endif; ?>

						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ($sort) : ?>
			<div class="sortDone accordionHide">
				<a class="submit" title="並び順を更新する" href="#">
					<span class="icon-save mr"></span>並び順を更新する
				</a>
			</div>
		<?php endif; ?>

	</div>
	<!--#item box end -->

	<?php echo Form::close(); ?>

</div>
<!--#item list end -->

<div style="display:none;">
	<!--#content start -->
	<div class="content" id="supportSearch">

		<!--#dig main start -->
		<div class="digMain">

			<div class="digTitle">
				<strong>
					<span class="icon-tags mr"></span>商品名検索
				</strong>

				<div class="digClose">
					<a href="#" title="#" class="close">
						<span class="icon-remove"></span>
					</a>
				</div>

			</div>

			<ul class="mainMenu lists">
				<?php foreach($rows as $row) : ?>
					<li>
						<a href="#item_<?php echo Arr::get($row, 'code'); ?>" title="<?php echo Arr::get($row, 'name'); ?>" class="link_close">
							<p>
								<?php echo Arr::get($row, 'name'); ?>
							</p>
							<span class="icon-chevron-right digNext"></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
		<!--#dig main end -->

	</div>
	<!--#contentend -->
</div>
