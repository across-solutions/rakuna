<!--#dig title start -->
<div class="digTitle">
	<strong>
		お知らせを編集
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		お知らせを編集します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		変更したい内容を入力してください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open(array('action' => '/manage/notice/edit_save', 'enctype' => 'multipart/form-data'), array('id' => Arr::get($data, 'id'))); ?>
	<?php echo $message(); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="newsName">
						タイトル<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('title', Arr::get($data, 'title'), array('placeholder' => 'サンプルタイトル')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>35文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('title'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="catMemberGroup">
						配信先グループ
					</label>
				</dt>
				<dd>
					<?php echo Form::select('member_group_id',Arr::get($data, 'member_group_id'), $member_groups, array('id' => 'catMemberGroup')); ?>
					<?php echo $validate_error_message('member_group_id'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="address">
						内容
					</label>
				</dt>
				<dd>
					<?php echo Form::textarea('message', Arr::get($data, 'message'), array('placeholder' => 'サンプル内容', 'rows' => '15')); ?>
					<a class="tooltip" rel="tooltip" title="5,000文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('message'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="newsCode">
						掲載商品コード
					</label>
				</dt>
				<dd>
					<?php echo Form::input('item_code', Arr::get($data, 'item_code'), array('placeholder' => '0000000000000')); ?>
					<a class="tooltip" rel="tooltip" title="20文字以内で入力してください。<br/>登録済みの商品コードを入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('item_code'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="newsImg">
						画像ファイル
					</label>
				</dt>
				<dd>
					<?php echo Form::file('notice_image'); ?>
					<a class="tooltip" rel="tooltip" title="JPG画像のみご利用いただけます。<br/>100KB以内のファイルをご用意ください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('notice_image'); ?>
					<?php if (Image_Notice::exist(Arr::get($data, 'id'))) : ?>
						<p>
							<?php echo Form::checkbox('image_del', '1', Arr::get($data, 'image_del')); ?>
							<?php echo Form::label('画像を削除する', 'image_del'); ?>
						</p>
						<?php echo Image_Notice::img(Arr::get($data, 'id')); ?>
					<?php endif; ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="newsPdf">
						PDFファイル
					</label>
				</dt>
				<dd>
					<?php echo Form::file('notice_pdf'); ?>
					<a class="tooltip" rel="tooltip" title="PDFファイルをご利用いただけます。<br/>2MB以内のファイルをご用意ください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('notice_pdf'); ?>
					<?php if (Pdf_Notice::exist(Arr::get($data, 'id'))) : ?>
						<p>
							<?php echo Form::checkbox('pdf_del', '1', Arr::get($data, 'pdf_del')); ?>
							<?php echo Form::label('PDFを削除する', 'pdf_del'); ?>
						</p>
						<div class="itemPdfDownload">
							<a target="_blank" href="<?php echo Pdf_Notice::url( Arr::get($data, 'id') ); ?>">
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
				<a href="/manage/notice/delete_save" title="削除" class="submit_delete">
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
