<!--#boxPadding start -->
<div class="boxPadding">
	<div class="mainNav">

		<div class="startOrder">
			<a href="/order/item/" title="発注開始" class="orderButton">
			<span class="icon-tablet"></span>発注開始
			</a>
		</div>

		<!--#home menu start -->
		<div class="choice">
			<a href="/order/history" title="発注履歴" class="mHistoryButton">
				<span class="icon-list mr"></span>発注履歴
			</a>

			<a href="/order/favorite" title="お気に入り" class="mfavButton">
				<span class="icon-star mr"></span>お気に入り
			</a>

			<a href="/order/recommended" title="いつもの" class="mRecomButton">
				<span class="icon-list-ol mr"></span>いつもの
			</a>
		</div>
		<!--#home menu end -->
	</div>

	<div class="boxPadding clearfix">
		<!--#news start -->
		<div class="news">
			<strong>
				お知らせ
			</strong>
			<div class="newsLoop">
				<ul>
					<?php if (count($notices) > 0) : ?>
						<?php foreach ($notices as $row) : ?>
							<li>
								<dl>
									<dt>
										<?php echo Common_Util::format_date(Arr::get($row, 'entry_datetime'), 'Y年m月d日'); ?>
									</dt>
									<dd>
										<a href="/order/notice/detail/<?php echo Arr::get($row, 'id'); ?>">
											<?php echo Arr::get($row, 'title'); ?>
										</a>
										<?php if (!Arr::get($row, 'read_id')) : ?>
											<span class="new">未読</span>
										<?php endif; ?>
									</dd>
								</dl>
							</li>
						<?php endforeach; ?>
					<?php else : ?>
							<li class="noNews">
								<p>
								現在、お知らせはありません。
								</p>
							</li>
					<?php endif; ?>
				</ul>
			</div>

			<?php if (count($notices) > 0) : ?>
				<div class="newsArchive">
					<a href="/order/notice" title="一覧へ">
						<span class="icon-chevron-sign-right mr"></span>一覧へ
					</a>
				</div>
			<?php endif; ?>
		</div>
		<!--#news end -->

		<!--#cat start -->
		<div class="cat home">
			<strong>
				すべてのカテゴリ
			</strong>

			<div class="catList">
				<ul>
					<?php foreach ($categories as $id => $name) : ?>
						<li>
							<a href="/order/item?category=<?php echo $id; ?>" title="<?php echo $name; ?>" data-tor-smoothScroll="noSmooth">
								<p>
									<?php echo $name; ?>
								</p>
								<span class="icon-chevron-right"></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<!--#cat end -->
	</div>
</div>