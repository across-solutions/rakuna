<!--#dig title start -->
<div class="digTitle">
	<strong>
	一括削除
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
	選択されたデータを削除します
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
	一度削除したデータを戻す事はできませんのでご注意ください。
	</p>
</div>
<!--#dig dec end -->

<?php echo Form::open(array('action' => '/manage/item/category/bulk_delete_save')); ?>
	<?php echo $message(); ?>
	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<li>
				<a href="#" title="削除" class="submit_bulk_delete">
					<span class="icon-save mr"></span>削除
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
