<!--#modalMenu start -->
<div class="mMenu">

	<!--#modal menu wrapper start -->
	<div class="mMenuWrap">

		<div class="start">
			<a href="#startMenu" title="メニュー" class="dialog_inline" data-tor-smoothScroll="noSmooth">
				<span class="icon-list"></span>
			</a>
		</div>

		<?php if ($visible_support_search) : ?>
			<div class="support">
				<a href="#supportSearch" title="ページ内検索" class="dialog_inline" data-tor-smoothScroll="noSmooth">
					<span class="icon-zoom-in"></span>
				</a>
			</div>
		<?php endif; ?>

		<?php if ($visible_clear_carts) : ?>
			<?php if ($visible_cach) : ?>
				<div class="cash clear_carts" style="visibility:visible;">
			<?php else : ?>
				<div class="cash" style="visibility:hidden;">
			<?php endif; ?>
					<a href="#" title="カートクリア" data-tor-smoothScroll="noSmooth">
						<span class="icon icon-trash"></span>
					</a>
				</div>
		<?php endif; ?>

		<?php if ($visible_cach) : ?>
			<div class="cash" style="visibility:visible;">
		<?php else : ?>
			<div class="cash" style="visibility:hidden;">
		<?php endif; ?>
				<a href="/order/register" title="カート" data-tor-smoothScroll="noSmooth">
					<svg>
						<title>カート</title>
						<desc>レジ画面へ移動します</desc>
						<use xlink:href="#svgCart"></use>
					</svg>
				</a>
			</div>

	</div>
	<!--#modal menu wrapper end -->

</div>
<!--#modalMenu end -->


<div style="display:none;">
	<!--#content start -->
	<div class="content" id="startMenu">

		<!--#dig main start -->
		<div class="digMain">

			<div class="digTitle">
				<strong>
					<span class="icon-reorder mr"></span>メニュー
				</strong>

				<div class="digClose">
					<a href="#" title="閉じる" class="close">
						<span class="icon-remove"></span>
					</a>
				</div>

			</div>

			<ul class="mainMenu">
				<li>
					<a href="/order/home" title="ホーム" data-tor-smoothScroll="noSmooth">
						<span class="icon-home digMr"></span>
						<p>
							ホーム
						</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>

				<li>
					<a href="/order/favorite" title="お気に入り" data-tor-smoothScroll="noSmooth">
						<span class="icon-star digMr"></span>
						<p>
							お気に入り
						</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>

				<li>
					<a href="/order/history" title="発注履歴" data-tor-smoothScroll="noSmooth">
						<span class="icon-rotate-left digMr"></span>
							<p>
								発注履歴
							</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>

				<li>
					<a href="/order/item" title="商品検索" data-tor-smoothScroll="noSmooth">
						<span class="icon-search digMr"></span>
							<p>
								商品検索
							</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>

				<li>
					<a href="/order/recommended" title="いつもの" data-tor-smoothScroll="noSmooth">
						<span class="icon-list-ol digMr"></span>
							<p>
								いつもの
							</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>

				<?php if (Common_Member::is_agency()) : ?>
				<li>
					<a href="/sales/member"" title="発注者選択" data-tor-smoothScroll="noSmooth">
						<span class="icon-user digMr"></span>
							<p>
								発注者選択
							</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>
				<?php endif; ?>

				<?php if(false) : ?>
				<li>
					<a href="/order/barcode" title="バーコード読取" data-tor-smoothScroll="noSmooth">
						<span class="icon-barcode digMr"></span>
							<p>
								バーコード読取
							</p>
						<span class="icon-chevron-right digNext"></span>
					</a>
				</li>
				<?php endif; ?>
			</ul>

		</div>
		<!--#dig main end -->

	</div>
	<!--#contentend -->
</div>