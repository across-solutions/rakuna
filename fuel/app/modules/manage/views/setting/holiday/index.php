<!--#title start -->
<div class="title">
	<strong>
		非営業日設定
	</strong>
</div>
<!--#title end -->

<?php echo $message(); ?>

<!--#mainMenuWrap start -->
<div class="mainMenuWrap">
	<!--#mainMenu start -->
	<div class="mainMenu">
		<ul>
			<li>
				<a href="/manage/setting/holiday/upload_csv" class="dialog" title="CSVアップロード">
					<span class="icon-chevron-right mr"></span>CSVアップロード<span class="icon-upload abss"></span>
				</a>
			</li>

		</ul>
	</div>
	<!--#mainMenu end -->
</div>
<!--#mainMenuWrap end -->

<div>
	<p>
		非営業日の日付を選択してください。<br>
		発注画面の納品希望日選択時に表示されなくなります。
	</p>
</div>

<div class="subMenuWrap">
 	<!--#subMenu start -->
 	<div class="subMenu">
 		<ul>
 			<li>
 				<a href="/manage/setting/holiday/download_csv" title="CSVダウンロード" class="dialog w180 orderHistDl">
 					<span class="icon-chevron-right mr"></span>CSVダウンロード<span class="icon-download abss"></span>
 				</a>
 			</li>
 		</ul>
 	</div>
 	<!--#subMenu end -->
</div>
<!--#subMenuWrap end -->

<?php echo Form::open('/manage/setting/holiday/save', array('year' => $year)); ?>

	<!--#cldPaging start -->
	<div class="cldPaging">
 		<ul>
 			<li class="first">
 				<a href="/manage/setting/holiday/?<?php echo Uri::build_query_string(array('year' => $year - 1)); ?>">≪≪</a>
 			</li>
 			<li>
				<span>
 				<?php echo $year; ?>
				</span>
 			</li>
 			<li class="last">
 				<a href="/manage/setting/holiday/?<?php echo Uri::build_query_string(array('year' => $year + 1)); ?>">≫≫</a>
 			</li>
 		</ul>
	</div>

	<div class="calendar">
		<div class="selectAllDayOfWeekWrap clearfix">
			<div class="cld">
				<table>
					<caption>
						まとめて選択
					</catpion>

					<thead>
						<tr>
							<th class="sun">日</th>
							<th>月</th>
							<th>火</th>
							<th>水</th>
							<th>木</th>
							<th>金</th>
							<th class="sat">土</th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<?php foreach(array(0, 1, 2, 3, 4, 5, 6) as $w):?>
								<td>
									<?php $id = 'week'.$w; ?>
									<?php echo Form::checkbox($id, $w , false, array('id' => $id,
																					 'class' => 'select_calendar_all_day'));?>
									<label class="chk" for="<?php echo $id ?>">
										<span class="dateNum">
										</span>
									</label>
								</td>
							<?php endforeach; ?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<?php for($i = 1; $i <=12; $i++):?>
			<?php $month = $calendar[$i-1] ?>
			<div class="cld">
				<table>
					<caption>
						<?php echo $i; ?>月
					</catpion>

					<thead>
						<tr>
							<th class="sun">日</th>
							<th>月</th>
							<th>火</th>
							<th>水</th>
							<th>木</th>
							<th>金</th>
							<th class="sat">土</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach($month as $week): ?>
							<tr>
								<?php foreach($week as $date): ?>

									<?php $today_class = $date['date'] == date('Y-m-d') ? 'today' : '';?>

									<?php if($date['current_month']): ?>
										<td>
											<?php echo Form::checkbox('calendar[]', $date['date'], $date['is_holiday'],
																	  array('id' => $date['date']));?>
											<label class="chk <?php echo $today_class;?>" for="<?php echo $date['date'];?>">
												<span class="dateNum">
													<?php echo $date['day'];?>
												</span>
											</label>
										</td>
									<?php else: ?>
										<td class="extraDateNum">
											<?php echo $date['day'];?>
										</td>
									<?php endif; ?>

								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endfor;?>
	</div>
	<!-- calendar end -->

	<!--#dig nav start -->
	<div class="digNav">
		<ul>
			<li>
				<a href="#" title="追加" class="submit">
					<span class="icon-save mr"></span>保存
				</a>
			</li>
		</ul>
	</div>
	<!--#dig nav end -->

<?php echo Form::close(); ?>
