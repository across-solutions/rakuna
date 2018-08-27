<!DOCTYPE html>
<html lang="ja" class="err repair">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="content-language" content="ja">
	<title>現在メンテナンス中です</title>
	<meta name="description" content="">

	<!--#電話番号自動認識OFF -->
	<meta name="format-detection" content="telephone=no">

	<!--#webclipicon設定-->
	<link rel="apple-touch-icon" href="/assets/img/webclip/apple-touch-icon-precomposed.png">
	<link rel="apple-touch-icon" href="/assets/img/webclip/apple-touch-icon.png" sizes="57x57 72x72 114x114">

	<!--#favicon設定-->
	<link rel="shortcut icon" href="/assets/img/favicon.ico">

	<!--#viewport指定-->
	<meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />

	<!--#cache設定-->
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT">

	<!--#normalizeCSS読み込み -->
	<link media="screen,projection" type="text/css" rel="stylesheet" href="/assets/order/css/normalize.css" />

	<!--#mainCSS読み込み start -->
	<?php if(Common_Terminal::is_pc()) : ?>
		<?php echo Asset::css('style_pc.css', array('media' => 'screen,projection')); ?>
	<?php else : ?>
		<?php echo Asset::css('style.css', array('media' => 'screen,projection')); ?>
	<?php endif; ?>

	<?php echo Asset::css('font-awesome.css', array('media' => 'screen,projection')); ?>
	<!--#web fontCSSフォントファイル読み込み-->

</head>
<body id="top">
	<!--#content start -->
	<div class="content">

		<!--#content Wrapper start -->
		<div class="contentWrapper">

			<div class="errArea">

				<div class="repairLogo">
					<?php echo Image_Logo::img_order_login_logo(); ?>
				</div>

				<h2>
				現在メンテナンス中です
				</h2>

				<strong>Under Maintenance</strong>

				<div class="clock">
					<div class="time12">12</div>
					<div class="time3">3</div>
					<div class="time6">6</div>
					<div class="time9">9</div>
				    <div class="hour-hand"></div>
				    <div class="minute-hand"></div>
				    <div class="second-hand"></div>
				</div>

				<div class="repArea">
					<p>
						現在メンテナンス中のため一時的にアクセスができない状況です。<br />
						時間を置いて再度アクセスしてください。
					</p>
				</div>

				<a href="/order/home" title="再確認する">
					<span class="icon-chevron-right mr"></span>
					再確認する
				</a>
			</div>

		</div>
		<!--#contentWrapper end -->

	</div>
	<!--#contentend -->

	<!--#footer start -->
	<footer class="err">

		<!--#footer wrapper start -->
		<div class="footerWrapper">

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

	<?php echo Asset::js('analytics.js'); ?>
</body>
</html>
