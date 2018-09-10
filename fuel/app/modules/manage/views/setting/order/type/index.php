<!--#title start -->
<div class="title">
	<strong>
		発注タイプ設定
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/setting/order/type/add" title="発注タイプを追加" class="dialog">
					<span class="icon-chevron-right mr"></span>発注タイプを追加<span class="icon-plus abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/setting/order/type', 'method' => 'get')); ?>
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

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th>発注タイプ名</th>
					<th class="w15">出荷区分コード</th>
					<th class="w15">倉庫コード</th>
					<th class="w8">編集</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($rows as $row) : ?>
					<tr>
						<td class="left">
							<?php echo Arr::get($row, 'name'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'code'); ?>
						</td>
						<td class="center">
							<?php echo Arr::get($row, 'warehouse_code'); ?>
						</td>
						<td class="center">
							<a href="/manage/setting/order/type/edit/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
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
