<!--#title start -->
<div class="title">
	<strong>
		メール設定
	</strong>
</div>
<!--#title end -->

<!--#configMenuWrap start -->
<div class="configMenuWrap">
	<!--#configMenu start -->
	<div class="configMenu">
		<ul>
			<li>
				<a href="/manage/setting/mail/login" title="登録用メール設定" <?php echo $selected == 'login' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>登録用メール設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/mail/order" title="発注受付用メール設定" <?php echo $selected == 'order' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>発注受付用メール設定
				</a>
			</li>
			<li>
				<a href="/manage/setting/mail/notice" title="お知らせ用メール設定" <?php echo $selected == 'notice' ? 'class="selected"' : ''; ?>>
					<span class="icon-angle-right mr"></span>お知らせ用メール設定
				</a>
			</li>
		</ul>
	</div>
	<!--#configMenu end -->
</div>
<!--#configMenuWrap end -->