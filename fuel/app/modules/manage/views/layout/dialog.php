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

	<!--#jquery読み込み-->
	<?php echo Asset::js('jquery.js'); ?>

	<!--#colorbox jquery読み込み -->
	<?php echo Asset::js('jquery.colorbox.js'); ?>
	
	<?php echo Asset::js('tooltip.js'); ?>
	<?php echo Asset::css('tooltip.css', array('media' => 'screen,projection')); ?>
	
	<?php echo Asset::js('dialog.js'); ?>
</head>
<body class="dialog">

	<!--#dialog wrapper start -->
	<div class="digWrap">

		<?php echo $content; ?>

	</div>
	<!--#dialog wrapper end -->

</body>
</html>