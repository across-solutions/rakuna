<!--#dig title start -->
<div class="digTitle">
	<strong>
	画像アップロード
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	登録商品に対して画像をアップロードします
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	JPG形式の画像をフォルダに入れZIP圧縮をしてアップロードしてください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open(array('action' => '/manage/item/upload_image_save', 'enctype' => 'multipart/form-data')); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="itemImg">
					ZIPファイル<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::file('item_image_zip', array('id' => 'itemImg')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>JPG画像のみご利用いただけます。<br/>画像ファイル名を商品番号にしてください。<br/>画像をフォルダに入れてZIP形式で圧縮してください。<br/>1度にアップロードできる容量は20MBまでです。推奨解像度 204 x 204 pixels">
						<span class="icon-question decEdit"></span>
					</a>
				</dd>
			</dl>

		</div>

		<div class="digForm">

			<div class="errorResult">
				<?php if($validate_error('item_image_zip')) : ?>
					<?php echo $validate_error_message('item_image_zip'); ?>
					<?php echo $validate_upload_error_message(); ?>
				<?php else : ?>
					<span>エラー内容が表示されます。</span>
				<?php endif;?>
			</div>

		</div>

	</div>
	<!--#dig edit form end -->

	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<li>
				<a href="#" title="アップロード" class="submit">
					<span class="icon-upload mr"></span>アップロード
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