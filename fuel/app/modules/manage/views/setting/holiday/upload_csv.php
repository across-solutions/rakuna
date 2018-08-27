<!--#dig title start -->
<div class="digTitle">
	<strong>
		CSVアップロード
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		非営業日CSVファイルをアップロードします
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		CSV形式のファイルをアップロードしてください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open(array('action' => '/manage/setting/holiday/upload_csv_save', 'enctype' => 'multipart/form-data')); ?>
	<!--#dig edit form start -->
	<div class="digEditForm clearfix">

		<div class="digForm">

			<dl class="clearfix">
				<dt>
					<label for="holidayCsv">
						CSVファイル<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::file('holiday_csv', array('id' => 'holidayCsv')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>1行目はヘッダ行とみなされますのご注意ください。<br/>1度にアップロードできる容量は20MBまでです。">
						<span class="icon-question decEdit" ></span>
					</a>
					<?php echo $validate_error_message('holiday_csv'); ?>
				</dd>
			</dl>

		</div>

	</div>
	<!--#dig edit form end -->

	<?php echo $validate_upload_error_message(); ?>

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
