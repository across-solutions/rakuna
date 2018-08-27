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
		受注データの確定が完了しました
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		確定した受注データのダウンロードをおこなう場合は「ダウンロード」
		を押してください。ダウンロードはダウンロード履歴からもおこなうことが
		できます。
	</p>
</div>
<!--#dig dec end -->

<!--#dig nav start -->
<div class="digNav">
	<?php echo Form::open('/manage/order/download_save', array('id' => Arr::get($data, 'id'))); ?>
		<?php echo $message(); ?>
		<ul>
			<li>
				<a href="#" title="ダウンロード" class="submit">
					<span class="icon-download mr"></span>ダウンロード
				</a>
			</li>
			<li>
				<a href="#" title="閉じる" class="close_reload">
					<span class="icon-remove mr"></span>閉じる
				</a>
			</li>
		</ul>
	<?php echo Form::close(); ?>
</div>
<!--#dig nav end -->
