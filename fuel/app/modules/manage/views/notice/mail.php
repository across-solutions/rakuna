<!--#dig title start -->
<div class="digTitle">
	<strong>
		メール送信
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		発注者に対してお知らせメールを送信します
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

<?php echo Form::open('/manage/notice/mail_send', array('id' => Arr::get($data, 'id'))); ?>
<!--#dig nav start -->
<div class="digNav">
	<ul>
		<li>
			<a href="#" title="送信" class="submit">
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
