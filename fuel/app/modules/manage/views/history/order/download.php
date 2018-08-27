<!--#dig title start -->
<div class="digTitle">
	<strong>
		CSVダウンロード
	</strong>
</div>
<!--#dig title end -->

<!--#dig text start -->
<div class="digText">
	<p>
		受注履歴のデータをCSV形式でダウンロードします
	</p>
</div>
<!--#dig text end -->

<!--#dig dec start -->
<div class="digDec">
	<p>
		検索したデータをダウンロードします。検索せずにCSVダウンロードを
		実行した場合は直近データを含む、すべてのデータをダウンロードします。
	</p>
</div>
<!--#dig dec end -->

<!--#dig nav start -->
<div class="digNav">
	<?php echo Form::open('/manage/history/order/download_save' . Common_Util::get_query_string()); ?>
		<ul>
			<li>
				<a href="#" title="ダウンロード" class="submit">
					<span class="icon-download mr"></span>ダウンロード
				</a>
			</li>
			<li>
				<a href="#" title="閉じる" class="close">
					<span class="icon-remove mr"></span>閉じる
				</a>
			</li>
		</ul>
	<?php echo Form::close(); ?>
</div>
<!--#dig nav end -->

