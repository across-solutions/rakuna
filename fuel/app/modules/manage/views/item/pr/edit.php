<!--#dig title start -->
<div class="digTitle">
	<strong>
		PR商品を解除
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	PR商品を解除します。
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		PR商品を解除する場合は、解除ボタンを押下してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open('/manage/item_pr/delete_save', array('id' => Arr::get($data, 'id'))); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">
		
		<div class="digForm">
			
			<dl class="clearfix">
				<dt>
					<label for="catCategory">
					カテゴリ
					</label>
				</dt>
				<dd>
					<?php echo Arr::get($data, 'item_categories.name'); ?>
				</dd>
			</dl>
			
			<dl class="clearfix">
				<dt>
					<label for="itemCode">
					商品コード
					</label>
				</dt>
				<dd>
					<?php echo Arr::get($data, 'code'); ?>
				</dd>
			</dl>
			
			<dl class="clearfix">
				<dt>
					<label for="itemName">
					商品名
					</label>
				</dt>
				<dd>
					<?php echo Arr::get($data, 'name'); ?>
				</dd>
			</dl>
			
			<dl class="clearfix">
			<dt>
				<label for="quantity">
				入数
				</label>
			</dt>
				<dd>
					<?php echo Arr::get($data, 'size'); ?>
				</dd>
			</dl>
			
			<dl class="clearfix">
				<dt>
					<label for="comment">
					商品説明文
					</label>
				</dt>
				<dd>
					<?php echo nl2br(Arr::get($data, 'comment')); ?>
				</dd>
			</dl>
			
			<?php if (Common_Setting::is_price()) : ?>
				<dl class="clearfix">
					<dt>
						<label for="price">
						<?php echo Common_Setting::is_case() ? 'バラ単価' : '単価'; ?>
						</label>
					</dt>
					<dd>
						<?php echo Common_Util::format_number(Common_Util::add_tax(Arr::get($data, 'price'))); ?>円
					</dd>
				</dl>
				
				<?php if (Common_Setting::is_case()) : ?>
					<dl class="clearfix">
						<dt>
							<label for="priceCase">
							ケース単価
							</label>
						</dt>
						<dd>
							<?php echo Common_Util::format_number(Common_Util::add_tax(Arr::get($data, 'price_case'))); ?>円
						</dd>
					</dl>
				<?php endif; ?>
			<?php endif; ?>
			
			<dl class="clearfix">
				<dt>
					<label for="itemImg">
					商品画像
					</label>
				</dt>
				<dd>
					<?php if (Image_Item::exist(Arr::get($data, 'code'))) : ?>
						<?php echo Image_Item::img(Arr::get($data, 'code')); ?>
					<?php endif; ?>
				</dd>
			</dl>
		
		</div>
	
	</div>
	<!--#dig edit form end -->
	
	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<li>
				<a href="#" title="解除" class="submit">
					<span class="icon-save mr"></span>解除
				</a>
			</li>
			<li>
				<a href="#" title="キャンセル" class="close">
					<span class="icon-remove mr"></span>キャンセル
				</a>
			</li>
		</ul>
	</div>
	<!--#dig nav end -->
<?php echo Form::close(); ?>
