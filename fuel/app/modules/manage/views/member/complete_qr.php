<!--#dig title start -->
<div class="digTitle">
	<strong>
	発注者を追加
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	QRコードから登録を行ってください
	</p>
</div>
<!--#dig text end -->

<!--#dig edit form start -->
<div class="digEditForm clearfix">
	<div class="digAccDone">
		<strong>
			<?php echo Asset::img('qr/' . Arr::get($data, 'qr_key') . '.png'); ?>
		</strong>
		<p>
			発注者の仮登録が完了しました。発注者の端末でQRコードを
			読み取り、表示されたメールを送信してください。
		</p>
		<p>
			登録完了のメールと共にログインID、パスワード、発注用URLを
			ご案内致します。ログインID、パスワードを入力して発注をおこなってください。
		</p>
	</div>
</div>
<!--#dig edit form end -->

<!--#dig nav start -->
<div class="digNav">
	<ul>
		<li>
			<a href="/manage/member/download_qr/<?php echo Arr::get($data, 'id'); ?>" title="ダウンロード" class="download">
				<span class="icon-download mr"></span>ダウンロード
			</a>
		</li>
		<li>
			<a href="#" title="閉じる" class="close_reload">
				<span class="icon-save mr"></span>閉じる
			</a>
		</li>
	</ul>
</div>
<!--#dig nav end -->
