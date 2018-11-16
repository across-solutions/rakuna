<!--#item list start -->
<div class="itemList onlyImg">

	<ul>
		<?php foreach($rows as $index => $row) : ?>
			<!--#item box start -->
			<li id="item_<?php echo Arr::get($row, 'code'); ?>" class="lineup">
				<div class="itemBox clearfix">

					<div class="itemDec">
						<div class="itemName lineup">
							<strong>
								<?php echo Arr::get($row, 'name'); ?>
							</strong>
						</div>
					</div>

					<div class="itemDecBottom">
						<div class="itemImg">
							<a href="/order/item/detail/<?php echo Arr::get($row, 'id'); ?>" title="<?php echo Arr::get($row, 'name'); ?>" class="dialog">
								<?php echo Image_Item::img(Arr::get($row, 'code'), array('class' => 'lineupimage')); ?>
							</a>
						</div>

						<?php $unit_name_case = Arr::get($row, 'unit_name_case'); ?>
						<?php $hidden_flg_case = Arr::get($row, 'hidden_flg_case'); ?>
						<?php if ($hidden_flg_case == UNDELETED && !empty($unit_name_case)) : ?>
							<?php if (Common_Setting::is_case()) : ?>
								<div class="buttons">
									<strong><?php echo Arr::get($row, 'unit_name_case'); ?></strong>
									<ul>
										<li>
											<input id="amount_case<?php echo Arr::get($row, 'id'); ?>" class="amount" type="text" value="<?php echo Arr::get($row, 'amount_case', 0); ?>" href="/order/ajax/cart/update_case/<?php echo Arr::get($row, 'id'); ?>.json">
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
									</ul>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<?php $unit_name = Arr::get($row, 'unit_name'); ?>
						<?php $hidden_flg_single = Arr::get($row, 'hidden_flg_single'); ?>
						<?php if ($hidden_flg_single == UNDELETED && !empty($unit_name)) : ?>
							<div class="buttons">
								<strong><?php if (Common_Setting::is_case()) : ?><?php echo Arr::get($row, 'unit_name'); ?><?php endif; ?></strong>
								<ul>
									<li>
										<input id="amount<?php echo Arr::get($row, 'id'); ?>" class="amount" type="text" value="<?php echo Arr::get($row, 'amount', 0); ?>" href="/order/ajax/cart/update/<?php echo Arr::get($row, 'id'); ?>.json">
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
								</ul>
							</div>
						<?php endif; ?>

					</div>

				</div>
			</li>
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
