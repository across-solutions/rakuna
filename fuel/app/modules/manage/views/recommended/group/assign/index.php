<!--#title start -->
<div class="title">
	<strong>
	いつものグループ割当管理
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/recommended/group/assign/add" title="いつものグループ割当を追加" class="dialog w240">
					<span class="icon-chevron-right mr"></span>いつものグループ割当を追加<span class="icon-plus abss"></span>
				</a>
			</li>
			<li>
				<a href="/manage/recommended/group/assign/upload_csv" title="CSVアップロード" class="dialog">
					<span class="icon-chevron-right mr"></span>CSVアップロード<span class="icon-upload abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/recommended/group/assign', 'method' => 'get')); ?>
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
					<td class="searchTitle w180">
						いつものグループコード
					</td>
					<td>
						<?php echo Form::input('recommended_group_code', Input::get('recommended_group_code')); ?>
						<?php echo $validate_error_message('recommended_group_code'); ?>
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
					<a href="/manage/recommended/group/assign/download_csv<?php echo Common_Util::get_query_string(); ?>" title="CSVダウンロード" class="dialog w180">
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
					<th class="w15">発注者コード</th>
					<th class="w15">発注者名</th>
					<th class="w15">いつものグループコード</th>
					<th>いつものグループ名</th>
					<th class="w8">削除</th>
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
							<?php echo Arr::get($row, 'recommended_group_code'); ?>
						</td>
						<td class="left">
							<?php echo Arr::get($row, 'recommended_group_name'); ?>
						</td>
						<td class="center">
							<a href="/manage/recommended/group/assign/delete/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
								<span class="icon-trash decEdit"></span>
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
