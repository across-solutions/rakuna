<?php echo View::forge('setting/logo/menu', array('selected' => 'order_logo_login')); ?>

<?php echo $message(); ?>

<!--#config start -->
<div class="config">
	<?php echo Form::open(array('action' => '/manage/setting/logo/order/login/save','enctype' => 'multipart/form-data')); ?>
		<div id="orderLink" class="configWrap">
			<strong>
				発注画面ログインロゴ設定
			</strong>
			<p>
				発注ログイン画面に表示するロゴ画像の設定をおこないます。
			</p>

			<dl class="clearfix">
				<dt>
					<label for="logo">
						発注側ログイン画面ロゴ画像
					</label>
				</dt>
				<dd>
					<?php echo Form::file('order_login_logo_image'); ?>
					<a class="tooltip" rel="tooltip" title="PNG画像のみご利用いただけます。<br/>100KB以内のファイルをご用意ください。<br/>推奨解像度 221 x 96 pixels">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('order_login_logo_image'); ?>
					<?php echo Image_Logo::img_order_login_logo(); ?>
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
					<li>
						<a href="/manage/setting/logo/order/login/default" title="デフォルト画像に戻す" class="submit_img_confirm">
							<span class="icon-save mr"></span>デフォルト画像に戻す
						</a>
					</li>
				</ul>
			</div>
			<!--#dig nav end -->
		</div>
	<?php echo Form::close(); ?>
</div>
<!--#config end -->