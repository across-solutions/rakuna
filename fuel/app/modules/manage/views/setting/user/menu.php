<!--#title start -->
<div class="title">
	<strong>
		ユーザ設定
	</strong>
</div>
<!--#title end -->

<!--#configMenuWrap start -->
<div class="configMenuWrap">
	<!--#configMenu start -->
	<div class="configMenu">
		<ul>
			<li>
				<a href="/manage/setting/user/account" title="管理者アカウント設定" <?php echo $selected == 'account' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>管理者アカウント設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/user/admin" title="管理者設定" <?php echo $selected == 'admin' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>管理者設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/user/order" title="発注画面設定" <?php echo $selected == 'order' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>発注管理設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/user/smart" title="端末設定" <?php echo $selected == 'smart' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>端末設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/user/maintenance" title="メンテナンス設定" <?php echo $selected == 'maintenance' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>メンテナンス設定
				</a>
			</li>
		</ul>
	</div>
	<!--#configMenu end -->
</div>
<!--#configMenuWrap end -->
