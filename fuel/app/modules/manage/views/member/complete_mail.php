<!--#dig title start -->
<div class="digTitle">
	<strong>
	ID・パスワード通知メールを送信
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	ID・パスワード通知メールの送信が完了しました
	</p>
</div>
<!--#dig text end -->


<!--#dig edit form start -->
<div class="digEditForm clearfix">
	<div class="digAccDone">
		<strong>
			<?php echo Arr::get($data, 'email'); ?>
		</strong>
		<p>
			上記アドレス宛にログインID、パスワード、発注用URLを記載した
			メールをお送りしました。
		</p>
	</div>
</div>
<!--#dig edit form end -->

<!--#dig nav start -->
<div class="digNav">
	<ul>
		<li>
			<a href="#" title="閉じる" class="close_reload">
				<span class="icon-remove mr"></span>閉じる
			</a>
		</li>
	</ul>
</div>
<!--#dig nav end -->
