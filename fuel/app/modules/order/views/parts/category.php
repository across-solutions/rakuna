<!--#cat start -->
<div class="cat sub">
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
