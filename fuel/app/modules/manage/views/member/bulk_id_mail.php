<!--#dig title start -->
<div class="digTitle">
	<strong>
		メール一斉送信
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		チェックした複数の発注者のメールアドレスに対してID・パスワード通知メールを一斉に送信します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		送信後の取り消しは出来ませんので、ご注意ください。
	</p>
</div>
<!--#dig dec end -->

<?php echo $message(); ?>

<?php echo Form::open('/manage/member/bulk_id_mail_send'); ?>
<!--#dig nav start -->
<div class="digNav">
	<ul>
		<li>
			<a href="#" title="送信" class="submit_bulk_id_mail_send">
				<span class="icon-envelope mr"></span>送信
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
