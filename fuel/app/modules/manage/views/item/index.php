<!--#title start -->
<div class="title">
	<strong>
	商品管理
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/item/add" title="商品を追加" class="dialog">
					<span class="icon-chevron-right mr"></span>商品を追加<span class="icon-plus abss"></span>
				</a>
			</li>
			<li>
				<a href="/manage/item/upload_csv" title="CSVアップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>CSVアップロード<span class="icon-upload abss"></span>
				</a>
			</li>
			<li>
				<a href="/manage/item/upload_image" title="画像アップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>画像アップロード<span class="icon-picture abss"></span>
				</a>
			</li>
			</li>
	 			<li>
				<a href="/manage/item/upload_pdf" title="PDFアップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>PDFアップロード<span class="icon-file abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/item', 'method' => 'get')); ?>
	<!--#search start -->
	<div class="search">
		<p>
		検索条件を指定して検索してください
		</p>

		<table class="searchBox">
			<tbody>
				<tr>
					<td class="searchTitle">
						<label for="freeword">
						フリーワード
						</label>
					</td>
					<td>
						<?php echo Form::input('search_field', Input::get('search_field'), array('id' => 'freeword')); ?>
						<?php echo $validate_error_message('search_field'); ?>
					</td>
					<td class="searchTitle">
						カテゴリ
					</td>
					<td>
						<?php echo Form::select('item_category_id', Input::get('item_category_id'), $categories); ?>
						<?php echo $validate_error_message('item_category_id'); ?>
					</td>
				</tr>
				<tr>
					<td class="searchTitle">
						<label for="freeword">
						単位表示
						</label>
					</td>
					<td colspan="3">
						<?php echo Form::checkbox('empty_unit_name', '1', Input::get('empty_unit_name')); ?>
						<?php echo Form::label('バラ単位が空', 'empty_unit_name'); ?>
						<?php echo Form::checkbox('empty_unit_name_case', '1', Input::get('empty_unit_name_case')); ?>
						<?php echo Form::label('ケース単位が空', 'empty_unit_name_case'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="searchSubmit">
			<a href="#" class="submit" title="この条件で検索する">
				<span class="icon-search mr"></span>この条件で検索する
			</a>
		</div>
	</div>
	<!--#search end -->
<?php echo Form::close(); ?>

<?php if ($data_count > 0) : ?>
	<!--#resultTop start -->
	<div class="resultTop clearfix">
		<div class="resultText">
			<strong>
			検索結果一覧
			</strong>
			<p>
			<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		</div>

		<div class="paging">
			<?php echo $pager; ?>
		</div>
	</div>
	<!--#resultTop end -->

	<!--#subMenuWrap start -->
	<div class="subMenuWrap">
		<!--#subMenu start -->
		<div class="subMenu">
			<ul>
				<li>
					<a href="/manage/item/download_csv<?php echo Common_Util::get_query_string(); ?>" title="CSVダウンロード" class="dialog w180">
						<span class="icon-chevron-right mr"></span>CSVダウンロード<span class="icon-download abss"></span>
					</a>
				</li>
			</ul>
		</div>
		<!--#subMenu end -->
	</div>
	<!--#subMenuWrap end -->

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th class="w10">カテゴリ</th>
					<th class="w12">商品コード</th>
					<th>商品名</th>
					<th class="w8">バラ単位</th>
					<th class="w8">ケース単位</th>
					<th class="w8">SMILE単位</th>
					<th class="w8">バラ入数</th>
					<th class="w8">ケース入数</th>
					<?php if (Common_Setting::is_price()) : ?>
						<?php if (Common_Setting::is_case()) : ?>
							<th class="w8">バラ単価</th>
							<th class="w8">ケース単価</th>
						<?php else : ?>
							<th class="w8">単価</th>
						<?php endif; ?>
					<?php endif; ?>
					<th class="w8">編集</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr>
						<td class="left">
							<?php echo Arr::get($row, 'item_categories.name'); ?>
						</td>
						<td class="center">
							<a href="/manage/item/edit/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
								<?php echo Arr::get($row, 'code'); ?>
							</a>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'unit_name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'unit_name_case'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'smile_unit_name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'size'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'size_case'); ?>
						</td>
						<?php if (Common_Setting::is_price()) : ?>
							<td class="right">
								<?php echo $format_price($row, 'price'); ?>
							</td>
							<?php if (Common_Setting::is_case()) : ?>
								<td class="right">
									<?php echo $format_price($row, 'price_case'); ?>
								</td>
							<?php endif; ?>
						<?php endif; ?>
						<td class="center">
							<a href="/manage/item/edit/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
								<span class="icon-edit decEdit"></span>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<!--#list end -->

	<!--#resultBottom start -->
	<div class="resultBottom clearfix">
		<div class="resultText">
			<p>
			<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		</div>

		<div class="paging">
			<?php echo $pager; ?>
		</div>
	</div>
	<!--#resultBottom end -->
<?php else : ?>
	<!--#resultTop start -->
	<div class="resultTop clearfix">
		<div class="resultText">
			データが見つかりませんでした。
		</div>
	</div>
	<!--#resultTop end -->
<?php endif; ?>
