<div class="narrow">
	<!--#dig title start -->
	<div class="digTitle">
		<strong>
			グループ割当CSVダウンロード
		</strong>
	</div>
	<!--#dig title end -->

	<!--#dig text start -->
	<div class="digText">
		<p>
			登録済みグループ割当データをCSV形式でダウンロードします
		</p>
	</div>
	<!--#dig text end -->

	<!--#dig dec start -->
	<div class="digDec">
		<p>
			登録済みのグループ割当データをMOSフォーマットのCSV形式でダウンロードします。
		</p>
	</div>
	<!--#dig dec end -->

	<?php echo Form::open('/manage/group/assign/download_csv_save' . Common_Util::get_query_string()); ?>
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