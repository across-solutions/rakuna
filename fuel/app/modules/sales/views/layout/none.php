<!DOCTYPE html>
<html lang="ja" class="sales">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="content-language" content="ja">
	<title><?php echo $TITLE; ?></title>
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
	<?php echo Asset::css('normalize.css', array('media' => 'screen,projection,print')); ?>

	<?php echo Asset::css('jquery-ui.min.css', array('media' => 'screen,projection,print')); ?>
	
	<!--#mainCSS読み込み start -->
	<?php if(Common_Terminal::is_pc()) : ?>
		<?php echo Asset::css('style_pc.css', array('media' => 'screen,projection,print')); ?>
	<?php else : ?>
		<?php echo Asset::css('style.css', array('media' => 'screen,projection,print')); ?>
	<?php endif; ?>

	<?php echo Asset::css('font-awesome.css', array('media' => 'screen,projection,print')); ?>
	<!--#web fontCSSフォントファイル読み込み-->
	<style type="text/css">  
	@font-face {
	  font-family: 'FontAwesome';
	  src: url('/assets/sales/font/fontawesome-webfont.eot?v=3.2.1');
	  src: url('/assets/sales/font/fontawesome-webfont.eot?#iefix&v=3.2.1') format('embedded-opentype'), url('/assets/sales/font/fontawesome-webfont.woff?v=3.2.1') format('woff'), url('/assets/sales/font/fontawesome-webfont.ttf?v=3.2.1') format('truetype'), url('/assets/sales/font/fontawesome-webfont.svg#fontawesomeregular?v=3.2.1') format('svg');
	  font-weight: normal;
	  font-style: normal;
	}
	</style>

	<?php echo Asset::css('colorbox.css', array('media' => 'screen,projection,print')); ?>
	
	<!--#jquery読み込み-->
	<?php echo Asset::js('jquery.js'); ?>
	
	<?php echo Asset::js('jquery-ui.min.js'); ?>
	
	<?php echo Asset::js('jquery.cookie.js'); ?>
	
	<?php echo Asset::css('jquery-ui-1.9.2.custom.min.css'); ?>
	
	<?php echo Asset::js('jquery-ui-1.9.2.custom.min.js'); ?>
	
	<?php echo Asset::js('sales.js'); ?>
	
	<?php echo Asset::render('page_js'); ?>
	
	<script type="text/javascript">
		$(function() {
			if (parent.location.href != location.href) {
				parent.location.href = parent.location.href;
				parent.$.colorbox.close();
			}
		});
	</script>
	
</head>
<body id="top">
	<?php echo $content; ?>
</body>
</html>