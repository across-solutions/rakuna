<div class="narrow">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
		商品発注タイプCSVダウンロード
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
		登録済み商品発注タイプデータをCSV形式でダウンロードします
		</p>
	</div>
	<!--#dig text end -->

	<!--#dig dec start -->
	<div class="digDec">
		<p>
		登録済みの商品発注タイプデータをMOSフォーマットのCSV形式でダウンロードします。
		</p>
	</div>
	<!--#dig dec end -->

	<?php echo Form::open('/manage/item/order/type/download_csv_save' . Common_Util::get_query_string()); ?>
		<!--#dig nav start -->
		<div class="digNav">
			<ul>
				<li>
					<a href="#" title="ダウンロード" class="submit">
						<span class="icon-download mr"></span>ダウンロード
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
</div>