<?php echo View::forge('setting/user/menu', array('selected' => 'smart')); ?>

<?php echo $message(); ?>

<!--#config start -->
<div class="config">
	<?php echo Form::open(array('action' => '/manage/setting/user/smart/save','enctype' => 'multipart/form-data')); ?>
		<div id="mobileLink" class="configWrap">
			<strong>
				端末設定
			</strong>
			<p>
				モバイル端末へMOSを登録する場合に表示されるアイコンの設定をおこないます。
			</p>

			<dl class="clearfix">
				<dt>
					<label for="clip">
						ウェブクリップアイコン<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::file('webclip_image'); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>PNG画像のみご利用いただけます。<br/>100KB以内のファイルをご用意ください。<br/>推奨解像度 150 x 150 pixels">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('webclip_image'); ?>
					<img src="/assets/img/webclip/apple-touch-icon.png">
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
	<?php echo Form::close(); ?>
</div>
<!--#config end -->