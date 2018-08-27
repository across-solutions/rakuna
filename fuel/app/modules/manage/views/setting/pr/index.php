<!--#title start -->
<div class="title">
	<strong>
		PR商品設定
	</strong>
</div>
<!--#title end -->

<!--#configMenuWrap start -->
<div class="configMenuWrap">
	<!--#configMenu start -->
	<div class="configMenu">
		<ul>
			<li>
				<a href="#" title="PR商品設定" class="selected">
					<span class="icon-angle-right mr"></span>PR商品設定
				</a>
			</li>
		</ul>
	</div>
	<!--#configMenu end -->
</div>
<!--#configMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/setting/pr/save', 'enctype' => 'multipart/form-data')); ?>
	<?php echo $message(); ?>

	<!--#config start -->
	<div class="config">
		<div id="prLink" class="configWrap">
			<strong>
				PR商品設定
			</strong>
			<p>
				PR商品ページのタイトル、誘導用バナーの設定をおこないます。
			</p>
		
			<dl class="clearfix">
				<dt>
					<label for="prTitle">
						PR商品ページタイトル<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('pr_title', Arr::get($data, 'pr_title'), array('placeholder' => '表示したいページタイトルを入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('pr_title'); ?>
				</dd>
			</dl>
		
			<dl class="clearfix">
				<dt>
					<label for="prBnr">
						PR商品誘導バナー画像
					</label>
				</dt>
				<dd>
					<?php echo Form::file('pr_image'); ?>
					<a class="tooltip" rel="tooltip" title="JPG,GIF,PNG画像のみご利用いただけます。<br/>2MB以内のファイルをご用意ください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('pr_image'); ?>
					<?php echo Image_Pr::img(Arr::get($data, 'pr_image_name'), array('style' => 'max-width:520px')); ?>
				</dd>
			</dl>
		
			<!--#dig nav start -->
			<div class="digNav configN">
				<ul>
					<li>
						<a href="#" title="保存" class="submit">
							<span class="icon-save mr"></span>保存
						</a>
					</li>
				</ul>
			</div>
			<!--#dig nav end -->
		</div>
	</div>
	<!--#config end -->
<?php echo Form::close(); ?>
