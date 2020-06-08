<!--#title start -->
<div class="title">
	<strong>
	商品発注タイプ管理
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/item/order/type/add" title="商品発注タイプを追加" class="dialog">
					<span class="icon-chevron-right mr"></span>商品発注タイプを追加
				</a>
			</li>
			<li>
				<a href="/manage/item/order/type/upload_csv" title="CSVアップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>CSVアップロード<span class="icon-upload abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/item/order/type', 'method' => 'get')); ?>
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
						発注者コード
						</label>
					</td>
					<td>
						<?php echo Form::input('member_code', Input::get('member_code')); ?>
						<?php echo $validate_error_message('member_code'); ?>
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
					<a href="/manage/item/order/type/download_csv<?php echo Common_Util::get_query_string(); ?>" title="CSVダウンロード" class="dialog w180">
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
					<th class="w10">発注者コード</th>
					<th class="w20">発注者名</th>
					<th class="w10">商品コード</th>
					<th>商品名</th>
					<th class="w12">発注タイプ</th>
					<th class="w5">編集</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr>
						<td class="center">
							<?php echo Arr::get($row, 'member_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'member_name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'item_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'item_name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'order_type_name'); ?>
						</td>
						<td class="center">
							<a href="/manage/item/order/type/edit/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
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
