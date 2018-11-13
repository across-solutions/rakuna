<!--#dig title start -->
<div class="digTitle">
	<strong>
		商品を編集
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	登録商品の画像、情報を編集します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	フォームに更新する情報を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open(array('action' => '/manage/item/edit_save', 'enctype' => 'multipart/form-data'), array('id' => Arr::get($data, 'id'))); ?>
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
					<?php echo Form::select('item_category_id', Arr::get($data, 'item_category_id'), $categories, array('id' => 'catCategory')); ?>
					<?php echo $validate_error_message('item_category_id'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="itemCode">
					商品コード<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('code', Arr::get($data, 'code'), array('id' => 'itemCode', 'placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>半角英数字で入力してください。<br/>20文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="itemName">
					商品名<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('name', Arr::get($data, 'name'), array('id' => 'itemName', 'placeholder' => 'サンプル商品')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="yomigana">
					商品カナ名
					</label>
				</dt>
				<dd>
					<?php echo Form::input('yomigana', Arr::get($data, 'yomigana'), array('id' => 'yomigana', 'placeholder' => 'サンプル商品カナ名')); ?>
					<a class="tooltip" rel="tooltip" title="全角カタカナで入力してください。<br/>50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('yomigana'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="unitName">
					バラ単位<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('unit_name', Arr::get($data, 'unit_name'), array('id' => 'unitName', 'placeholder' => '箱')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('unit_name'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="unitNameCase">
					ケース単位
					</label>
				</dt>
				<dd>
					<?php echo Form::input('unit_name_case', Arr::get($data, 'unit_name_case'), array('id' => 'unitNameCase', 'placeholder' => 'ケース')); ?>
					<a class="tooltip" rel="tooltip" title="10文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('unit_name_case'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="quantity">
					バラ入数<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('size', Arr::get($data, 'size'), array('id' => 'quantity', 'placeholder' => '1')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>数字で入力してください。<br/>10,000未満で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('size'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="quantityCase">
					ケース入数
					</label>
				</dt>
				<dd>
					<?php echo Form::input('size_case', Arr::get($data, 'size_case'), array('id' => 'quantityCase', 'placeholder' => '12')); ?>
					<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>1,000,000未満で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('size_case'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="itemType">
						商品タイプ<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::radio('type', Config::get('define.item_type.material'), Arr::get($data, 'type', '000000'), array('id' => 'form_type_0')); ?>
					<?php echo Form::label('原料', 'type_0'); ?>
					<?php echo Form::radio('type', Config::get('define.item_type.stock'), Arr::get($data, 'type', '000000'), array('id' => 'form_type_1')); ?>
					<?php echo Form::label('ﾛｼﾞｽ在庫', 'type_1'); ?>
					<?php echo Form::radio('type', Config::get('define.item_type.order'), Arr::get($data, 'type', '000000'), array('id' => 'form_type_2')); ?>
					<?php echo Form::label('取寄品', 'type_2'); ?>
					<?php echo Form::radio('type', Config::get('define.item_type.special'), Arr::get($data, 'type', '000000'), array('id' => 'form_type_3')); ?>
					<?php echo Form::label('取置・別製', 'type_3'); ?>
					<?php echo Form::radio('type', Config::get('define.item_type.other'), Arr::get($data, 'type', '000000'), array('id' => 'form_type_4')); ?>
					<?php echo Form::label('その他', 'type_4'); ?>
					<?php echo $validate_error_message('type'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="comment">
					商品説明文
					</label>
				</dt>
				<dd>
					<?php echo Form::textarea('comment', Arr::get($data, 'comment'), array('id' => 'comment', 'placeholder' => '350ml')); ?>
					<a class="tooltip" rel="tooltip" title="500文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('comment'); ?>
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
						<?php echo Form::input('price', Arr::get($data, 'price'), array('id' => 'price', 'placeholder' => '0000')); ?>
						<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>10,000,000円未満で入力してください。">
							<span class="icon-question decEdit"></span>
						</a>
						<?php echo $validate_error_message('price'); ?>
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
							<?php echo Form::input('price_case', Arr::get($data, 'price_case'), array('id' => 'priceCase', 'placeholder' => '0000')); ?>
							<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>10,000,000円未満で入力してください。">
								<span class="icon-question decEdit"></span>
							</a>
							<?php echo $validate_error_message('price_case'); ?>
						</dd>
					</dl>
				<?php endif; ?>
			<?php endif; ?>

			<dl class="clearfix">
				<dt>
					<label for="cost">
						原価
					</label>
				</dt>
				<dd>
					<?php echo Form::input('cost', Arr::get($data, 'cost'), array('id' => 'cost', 'placeholder' => '0000')); ?>
					<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>10,000,000円未満で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('cost'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="jan_code">
					JANコード
					</label>
				</dt>
				<dd>
					<?php echo Form::input('jan_code', Arr::get($data, 'jan_code'), array('id' => 'jan_code', 'placeholder' => '00000000000')); ?>
					<a class="tooltip" rel="tooltip" title="数字で入力してください。<br/>13桁以下で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('jan_code'); ?>
				</dd>
			</dl>

			<?php if(false) : ?>
			<dl class="clearfix">
				<dt>
					<label for="itemPr">
					PR商品
					</label>
				</dt>
				<dd>
					<?php echo Form::radio('pr_flg', 0, Arr::get($data, 'pr_flg'), array('id' => 'form_pr_flg_0')); ?>
					<?php echo Form::label('通常商品', 'pr_flg_0'); ?>
					<?php echo Form::radio('pr_flg', 1, Arr::get($data, 'pr_flg'), array('id' => 'form_pr_flg_1')); ?>
					<?php echo Form::label('PR商品', 'pr_flg_1'); ?>
					<?php echo $validate_error_message('pr_flg'); ?>
				</dd>
			</dl>
			<?php endif; ?>

			<dl class="clearfix">
				<dt>
					<label for="itemImg">
					商品画像
					</label>
				</dt>
				<dd>
					<?php echo Form::file('item_image'); ?>
					<a class="tooltip" rel="tooltip" title="JPG画像のみご利用いただけます。<br/>2MB以内のファイルをご用意ください。<br/>推奨解像度 204 x 204 pixels">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('item_image'); ?>
					<?php if (Image_Item::exist(Arr::get($data, 'code'))) : ?>
						<p>
							<?php echo Form::checkbox('image_del', '1', Arr::get($data, 'image_del')); ?>
							<?php echo Form::label('画像を削除する', 'image_del'); ?>
						</p>
						<?php echo Image_Item::img(Arr::get($data, 'code')); ?>
					<?php endif; ?>
				</dd>
			</dl>

		  <dl class="clearfix">
				<dt>
					<label for="itemPdf">
						PDFファイル
					</label>
				</dt>
				<dd>
					<?php echo Form::file('item_pdf'); ?>
					<a class="tooltip" rel="tooltip" title="PDFファイルをご利用いただけます。<br/>2MB以内のファイルをご用意ください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('item_pdf'); ?>
					<?php if (Pdf_Item::exist(Arr::get($data, 'code'))) : ?>
						<p>
							<?php echo Form::checkbox('pdf_del', '1', Arr::get($data, 'pdf_del')); ?>
							<?php echo Form::label('PDFを削除する', 'pdf_del'); ?>
						</p>
						<div class="itemPdfDownload">
							<a target="_blank" href="<?php echo Pdf_Item::url( Arr::get($data, 'code') ); ?>">
								<span class="icon-file mr"></span> PDFを表示
							</a>
						</div>
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
				<a href="#" title="保存" class="submit">
					<span class="icon-save mr"></span>保存
				</a>
			</li>
			<li>
				<a href="/manage/item/delete_save" title="削除" class="submit_delete">
					<span class="icon-trash mr"></span>削除
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
