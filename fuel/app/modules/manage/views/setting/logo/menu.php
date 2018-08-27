<!--#title start -->
<div class="title">
	<strong>
		画像設定
	</strong>
</div>
<!--#title end -->

<!--#configMenuWrap start -->
<div class="configMenuWrap">
	<!--#configMenu start -->
	<div class="configMenu">
		<ul>
			<li>
				<a href="/manage/setting/logo/order/base" title="発注画面ロゴ設定" <?php echo $selected == 'order_logo_base' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>ロゴ設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/logo/order/login" title="発注画面ログインロゴ設定" <?php echo $selected == 'order_logo_login' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>ログインロゴ設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/logo/order/noimage" title="発注画面NoImage設定" <?php echo $selected == 'order_logo_noimage' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>NoImage設定
				</a>
			</li>
		</ul>
	</div>
	<!--#configMenu end -->
</div>
<!--#configMenuWrap end -->
