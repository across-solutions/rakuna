<!--#title start -->
<div class="title">
	<strong>
	いつもの商品管理
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/recommended/item/add?recommended_group_code=<?php echo Input::get('recommended_group_code'); ?>" title="いつもの商品を追加" class="dialog w240">
					<span class="icon-chevron-right mr"></span>いつもの商品を追加<span class="icon-plus abss"></span>
				</a>
			</li>
			<li>
				<a href="/manage/recommended/item/upload_csv" title="CSVアップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>CSVアップロード<span class="icon-upload abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/recommended/item', 'method' => 'get')); ?>
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
						いつものグループ
						</label>
					</td>
					<td>
						<?php echo Form::select('recommended_group_code', $recommended_group_code, $recommended_groups, array('id' => 'recommendedGroupCode')); ?>

						<?php echo $validate_error_message('recommended_group_code'); ?>
					</td>
					<td class="searchTitle">
						商品コード
					</td>
					<td>
						<?php echo Form::input('item_code', Input::get('item_code')); ?>
						<?php echo $validate_error_message('item_code'); ?>
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
					<a href="/manage/recommended/item/download_csv<?php echo Common_Util::get_query_string(); ?>" title="CSVダウンロード" class="dialog w180">
						<span class="icon-chevron-right mr"></span>CSVダウンロード<span class="icon-download abss"></span>
					</a>
				</li>
			</ul>
		</div>
		<!--#subMenu end -->
	</div>
	<!--#subMenuWrap end -->

	<?php if(!$is_show_sort_button) : ?>
		<div>
			<p>
				<span class="icon-info-sign"></span> 検索条件で「いつものグループ」のみを指定すると並び替えできます
			</p>
		</div>
		<?php endif?>

	<?php echo Form::open(Uri::create('/manage/recommended/item', array(), Input::get()),
						  array('recommended_group_code' => $recommended_group_code)); ?>

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<?php echo $validate_error_all(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th class="w15">いつものグループコード</th>
					<th class="w15">いつものグループ名</th>
					<th class="w15">商品コード</th>
					<th>商品名</th>

					<?php if($is_show_sort_button) : ?>
						<th class="w8">順番</th>
					<?php endif ?>

					<th class="w8">削除</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $index => $row) : ?>
					<?php $id = Arr::get($row, 'id'); ?>
					<tr>
						<td class="center">
							<?php echo Arr::get($row, 'recommended_group_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'recommended_group_name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'item_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'item_name'); ?>
						</td>

						<?php if($is_show_sort_button) : ?>
							<td class="center">
								<?php echo Form::input('sort_num[' . $id . ']', Arr::get($data, 'sort_num.'.$id),
array('class' => 'sortNum '.$validate_error_class('sort_num.'.$id), 'size' => '2', 'placeholder' => $page_index + $index + 1)); ?>

							</td>
						<?php endif ?>

						<td class="center">
							<a href="/manage/recommended/item/delete/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
								<span class="icon-trash decEdit"></span>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>


		<?php if($is_show_sort_button) : ?>
			<div class="sortDone">
				<a class="submit" title="並び順を更新する" href="#">
					<span class="icon-save mr"></span>並び順を更新する
				</a>
			</div>
		<?php endif ?>
		
	
	</div>
	<!--#list end -->
	<?php echo Form::close(); ?>
	
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
