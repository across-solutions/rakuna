<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="content-language" content="ja">
	<title><?php echo $title; ?></title>
	<meta name="description" content="">

	<!--#電話番号自動認識OFF -->
	<meta name="format-detection" content="telephone=no">

	<!--#favicon設定-->
	<link rel="shortcut icon" href="/assets/img/favicon.ico">

	<!--#viewport指定-->
	<meta name="viewport" content="width=device-width">

	<!--#cache設定-->
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT">

	<!--#normalizeCSS読み込み -->
	<?php echo Asset::css('normalize.css', array('media' => 'screen,projection')); ?>

	<!--#mainCSS読み込み start -->
	<?php echo Asset::css('style.css', array('media' => 'screen,projection')); ?>

	<?php echo Asset::css('font-awesome.css', array('media' => 'screen,projection')); ?>
	<!--#web fontCSSフォントファイル読み込み-->
	<style type="text/css">  
	@font-face {
	  font-family: 'FontAwesome';
	  src: url('/assets/manage/font/fontawesome-webfont.eot?v=3.2.1');
	  src: url('/assets/manage/font/fontawesome-webfont.eot?#iefix&v=3.2.1') format('embedded-opentype'), url('/assets/manage/font/fontawesome-webfont.woff?v=3.2.1') format('woff'), url('/assets/manage/font/fontawesome-webfont.ttf?v=3.2.1') format('truetype'), url('/assets/manage/font/fontawesome-webfont.svg#fontawesomeregular?v=3.2.1') format('svg');
	  font-weight: normal;
	  font-style: normal;
	}
	</style>

	<!--#メニュー用CSS読み込み(flexnav) -->
	<?php echo Asset::css('flexnav.css', array('media' => 'screen,projection')); ?>

	<!--#colorbox CSS読み込み-->
	<?php echo Asset::css('colorbox.css', array('media' => 'screen,projection')); ?>

	<!--#jquery読み込み-->
	<?php echo Asset::js('jquery.js'); ?>
	
	<?php echo Asset::css('jquery-ui-1.9.2.custom.min.css', array('media' => 'screen,projection')); ?>
	<?php echo Asset::js('jquery-ui-1.9.2.custom.min.js'); ?>

	<!--#メニュー用jquery読み込み(flexnav) -->
	<?php echo Asset::js('jquery.flexnav.js'); ?>
	
	<!--#colorbox jquery読み込み -->
	<?php echo Asset::js('jquery.colorbox.js'); ?>
	
	<?php echo Asset::js('tooltip.js'); ?>
	<?php echo Asset::css('tooltip.css', array('media' => 'screen,projection')); ?>
	
	<?php echo Asset::js('manage.js'); ?>
	<?php echo Asset::render('page_js'); ?>

</head>
<body>
	<?php echo View::forge('parts/header'); ?>

	<!--#content start -->
	<div class="content">
	
		<!--#content Wrapper start -->
		<div class="contentWrapper">
		
			<!--#boxPadding start -->
			<div class="boxPadding">
				<?php echo $content; ?>
			</div>
			<!--#boxPadding end -->
		
		</div>
		<!--#contentWrapper end -->
	
	</div>
	<!--#contentend -->

	<?php echo View::forge('parts/footer'); ?>

	<?php echo Asset::js('jquery.flexnav.js'); ?>
	<script type="text/javascript">
		$(".flexnav").flexNav({"animationSpeed" : 10 });
	</script>
</body>
</html>
