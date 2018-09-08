<!--#footer start -->
<footer>

	<!--#footer wrapper start -->
	<div class="footerWrapper">

		<!--#footer nav start -->
		<nav class="footerNav clearfix">
			<ul>

				<li>
					<a href="/order/favorite" title="「お気に入り」から選ぶ" data-tor-smoothScroll="noSmooth">
						<span class="icon-angle-right mr"></span>「お気に入り」から選ぶ
					</a>
				</li>

				<li>
					<a href="/order/item" title="商品を検索する" data-tor-smoothScroll="noSmooth">
						<span class="icon-angle-right mr"></span>商品を検索する
					</a>
				</li>

				<li>
					<a href="/order/history" title="発注履歴を見る" data-tor-smoothScroll="noSmooth">
						<span class="icon-angle-right mr"></span>発注履歴を見る
					</a>
				</li>

				<li>
					<a href="/order/recommended" title="「いつもの」から選ぶ" data-tor-smoothScroll="noSmooth">
						<span class="icon-angle-right mr"></span>「いつもの」から選ぶ
					</a>
				</li>

				<?php if (Common_Member::is_agency()) : ?>
					<li>
						<a href="/sales/member" title="発注者選択" data-tor-smoothScroll="noSmooth">
							<span class="icon-angle-right mr"></span>発注者選択
						</a>
					</li>
				<?php endif; ?>

				<?php if(false) : ?>
				<li>
					<a href="/order/barcode" title="バーコードを読み取る" data-tor-smoothScroll="noSmooth">
						<span class="icon-angle-right mr"></span>バーコードを読み取る
					</a>
				</li>
				<?php endif; ?>

			</ul>

			<div class="logout">
				<a href="/order/login/logout" title="ログアウト">
					ログアウト
				</a>
			</div>

		</nav>
		<!--#footer nav end -->

		<!--#copyright start -->
		<div class="copyright">
			<p>
				powerd by <a href="http://www.mosjapan.jp/" title="MOS" target="_blank">MOS</a>
			</p>
			<p>
				&copy;ACROSS Solutions,inc. All Rights Reserved.
			</p>
		</div>
		<!--#copyright end -->

	</div>
	<!--#footer wrapper end -->

</footer>
<!--#footer end -->