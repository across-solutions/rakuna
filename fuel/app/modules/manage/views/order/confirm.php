<!--#dig title start -->
<div class="digTitle">
	<strong>
		受注確定
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		受注データを確定しデータを受注履歴へ移動します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		受注データ確定後は受注データの変更ができなくなりますので、
		ご注意ください。
	</p>
</div>
<!--#dig dec end -->

<?php echo $message(); ?>

<!--#dig nav start -->
<div class="digNav">
	<?php echo Form::open('/manage/order/confirm_save' . Common_Util::get_query_string()); ?>
		<ul>
			<li>
				<a href="#" title="確定" class="submit">
					<span class="icon-ok mr"></span>確定
				</a>
			</li>
			<li>
				<a href="#" title="キャンセル" class="close">
					<span class="icon-remove mr"></span>キャンセル
				</a>
			</li>
		</ul>
	<?php echo Form::close(); ?>
</div>
<!--#dig nav end -->
