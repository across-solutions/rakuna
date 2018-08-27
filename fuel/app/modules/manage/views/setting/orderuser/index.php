<!--#title start -->
<div class="title">
	<strong>
	受注担当者管理
	</strong>
</div>
<!--#title end -->

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/setting/orderuser/add" class="dialog" title="受注担当者を追加">
					<span class="icon-chevron-right mr"></span>受注担当者を追加<span class="icon-plus abss"></span>
				</a>
			</li>
		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<?php echo Form::open(array('action' => '/manage/setting/orderuser', 'method' => 'get')); ?>
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
						受注担当者名
						</label>
					</td>
					<td>
						<?php echo Form::input('name', Input::get('name'), array('id' => 'freeword')); ?>
						<?php echo $validate_error_message('name'); ?>
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

<!--#resultTop start -->
<div class="resultTop clearfix">
	<div class="resultText">
		<?php if ($data_count > 0) : ?>
			<strong>
			検索結果一覧
			</strong>
			<p>
			<?php echo $data_count; ?>件のデータが見つかりました。
			</p>
		<?php else : ?>
			データが見つかりませんでした。
		<?php endif ?>
	</div>

	<div class="paging">
		<?php echo $pager; ?>
	</div>
</div>
<!--#resultTop end -->

<?php if ($data_count > 0) : ?>

	<!--#list start -->
	<div class="list">
		<?php echo $message(); ?>
		<table class="resultList stripe">
			<thead>
				<tr>
					<th>受注担当者名</th>
					<th class="w10">編集</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($rows as $row) : ?>

				<?php $id = Arr::get($row, 'id'); ?>
					<tr>
						<td class="left">
							<?php echo Arr::get($row, 'name'); ?>
						</td>
						<td class="center">
							<a href="/manage/setting/orderuser/edit/<?php echo Arr::get($row, 'id'); ?>" class="dialog">
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

<?php endif ?>
