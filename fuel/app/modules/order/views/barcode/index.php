
<!--#boxPadding start -->
<div class="boxPadding">

	<!--#page title start -->
	<div class="pageTitle">
		<strong>
			商品コード読取
		</strong>
	</div>
	<!--#page title end -->

	<!--#page dec start -->
	<div class="pageDec">
		<p>
		「読取開始」を押下してください。
		</p>
	</div>
	<!--#page dec end -->

</div>
<!--#boxPadding end -->

<!--#buy now start -->
<div class="buyNow">
	<a href="#" title="読取開始" id="readStart">
		<span class="icon-barcode mr"></span>読取開始<span class="icon-chevron-down ml"></span>
	</a>
</div>
<!--#buy now end -->

<!--#item list start -->
<div id="barcodeList" class="itemList" style="display:none;">
	<ul>

		<!--#item box start -->
		<li>
			<div class="codeItemBox clearfix">
				<div class="codeDec">
					<div class="codeForm">
						<div class="codeHead">
							<div class="codeHeadTitle">
								<strong>
									バーコード
								</strong>
							</div>
							<div class="codeHeadChange">
								<a id="count_type" href="#">バラ</a>
							</div>
						</div>
						<input type="tel" value="" id="barcode" />
					</div>

					<div class="itemBox clearfix">
						<div class="itemDec">
							<div class="itemName">
								<strong>
									<span id="item_name"></span>
								</strong>
							</div>
						</div>

						<div class="buttons codeButton">
							<div class="codeImgWrap">
								<img id="item_image" class="itemImg">
							</div>
							<div class="codeButtonWrap">
								<ul id="case_count">
									<li>
										<strong>ケース</strong>
									</li>
									<li>
										<input type="text" value="0" name="" id="" class="amount" />
									</li>
									<li>
										<a href="#" title="プラス" class="plus item_modify wave">
											<span class="ring"></span>
											<span class="icon-plus"></span>
										</a>
									</li>
									<li>
										<a href="#" title="マイナス" class="minus item_modify wave">
											<span class="ring"></span>
											<span class="icon-minus"></span>
										</a>
									</li>
									<li>
										<a href="#" title="ごみ箱" class="del item_modify wave">
											<span class="ring"></span>
											<span class="icon-trash"></span>
										</a>
									</li>
								</ul>
								<ul id="bara_count">
									<li>
										<strong>バラ</strong>
									</li>
									<li>
										<input type="text" value="0" name="" id="" class="amount" />
									</li>
									<li>
										<a href="#" title="プラス" class="plus item_modify wave">
											<span class="ring"></span>
											<span class="icon-plus"></span>
										</a>
									</li>
									<li>
										<a href="#" title="マイナス" class="minus item_modify wave">
											<span class="ring"></span>
											<span class="icon-minus"></span>
										</a>
									</li>
									<li>
										<a href="#" title="ごみ箱" class="del item_modify wave">
											<span class="ring"></span>
											<span class="icon-trash"></span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

			</div>
		</li>
		<!--#item box end -->

	</ul>
</div>
<!--#item list end -->

<div class="codeMsg">
	<strong>
	読取待機中…
	</strong>
	<p>
	バーコードリーダーを利用して商品のバーコードを読み取ってください。
	</p>
</div>