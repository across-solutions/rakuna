<?php
// 発注ログインURL
define('ORDER_LOGIN_URL', 'https://www.rakuna-web-order.com/order');

// メンテナンスモード
define('MAINTENANCE_MODE', false);

// バージョン
define('VERSION', '　');

// メッセージセッションキー
define('SESSION_KEY_INFO_MESSAGE', 'info_message');

// エラーメッセージセッションキー
define('SESSION_KEY_ERROR_MESSAGE', 'error_message');

// カート情報セッションキー
define('SESSION_KEY_CART', 'cart_info');

// 代理発注セッションキー
define('SESSION_KEY_SALES', 'sales_info');

// 自動ログインクッキーキー(発注)
define('COOKIE_KEY_ORDER_AUTO_LOGIN', 'oral');

// 自動ログインクッキーキー(受注)
define('COOKIE_KEY_MANAGE_AUTO_LOGIN', 'mgal');

// 自動ログインクッキーキー(代理発注)
define('COOKIE_KEY_SALES_AUTO_LOGIN', 'slal');

// 自動ログインクッキー有効期間(秒)
define('COOKIE_EXPIRATION_AUTO_LOGIN', 60 * 60 * 24 * 30);

// 商品一覧表示方式クッキーキー
define('COOKIE_KEY_ITEM_MODE', 'im');

// 商品一覧表示方式クッキー有効期間(秒)
define('COOKIE_EXPIRATION_ITEM_MODE', 60 * 60 * 24 * 30);

// 商品画像出力先
define('ITEM_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'item' . DS);

// 商品PDF出力先
define('ITEM_PDF_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'item_pdf' . DS);

// QRコード出力先
define('QR_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'qr' . DS);

// お知らせ画像出力先
define('NOTICE_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'notice' . DS);

// お知らせPDF出力先
define('NOTICE_PDF_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'notice_pdf' . DS);

// PR商品誘導バナー画像出力先
define('PR_IMAGE_PATH',  DOCROOT . 'assets' . DS . 'img' . DS . 'pr' . DS);

// 発注画面ロゴ画像出力先
define('LOGO_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'logo' . DS);

// 発注画面ロゴ画像URL
define('ORDER_LOGO_IMAGE_URL', '/logo/order_logo.png');

// 発注画面ロゴ画像URL(デフォルト)
define('DEFAULT_ORDER_LOGO_IMAGE_URL', '/logo/default_order_logo.png');

// ウェブクリップアイコン出力先
define('WEBCLIP_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'webclip' . DS);

// 受注CSV出力先
define('ORDER_CSV_PATH', APPPATH . 'output' . DS . 'csv' . DS . 'order' . DS);

// 発注側ログイン画面ロゴ画像出力先
define('ORDER_LOGIN_LOGO_IMAGE_PATH', DOCROOT . 'assets'  . DS . 'img' . DS . 'logo' . DS);

// 発注側ログイン画面ロゴ画像URL
define('ORDER_LOGIN_LOGO_IMAGE_URL', '/logo/order_login_logo.png');

// 発注側ログイン画面ロゴ画像URL(デフォルト)
define('DEFAULT_ORDER_LOGIN_LOGO_IMAGE_URL', '/logo/default_order_login_logo.png');

// NoImage画像出力先
define('NO_IMAGE_PATH', DOCROOT . 'assets' . DS . 'img' . DS . 'item' . DS);

// NoImage画像URL
define('NO_IMAGE_URL', 'item/noimage.jpg');

// NoImage画像URL(デフォルト)
define('DEFAULT_NO_IMAGE_URL', 'item/default_noimage.jpg');

// メール認証メールアドレス
define('MAIL_AUTH_MAIL', 'entry@acrossjapan.co.jp');

// メール認証タイトル
define('MAIL_AUTH_TITLE', 'このまま送信してください');

// 自動生成ログインID文字数
define('RANDOM_USERNAME_NUM', 5);

// 自動生成パスワード文字数
define('RANDOM_PASSWORD_NUM', 5);

// QR認証キー文字数
define('RANDOM_QR_KEY_NUM', 20);

// 削除
define('DELETED', 1);

// 未削除
define('UNDELETED', 0);

// ホーム画面お知らせ表示件数
define('HOME_NOTICE_NUM', 5);

// カート内最大個数
define('MAX_CART_ITEM_AMOUNT', 99);

// 受注履歴CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_ORDER_HISTORY', 'order_history.csv');

// 商品CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_ITEM', 'items.csv');

// グループ割当CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_GROUP_ASSIGN', 'group_assign.csv');

// 割当CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_ASSIGN', 'assign.csv');

// 発注者CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_MEMBER', 'member.csv');

// 納品先CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_DELIVERY', 'delivery.csv');

// 配達曜日CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_DELIVERY_WEEK', 'shipping_week.csv');

// 営業担当者CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_SALES_REPRESENTATIVE', 'sales_representative.csv');

// 非営業日CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_HOLIDAY', 'holiday.csv');

// いつもの商品CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_RECOMMENDED_ITEM', 'recommended_item.csv');

// いつものグループ割当CSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_RECOMMENDED_GROUP_ASSIGN', 'recommended_group_assign.csv');

// 商品発注タイプCSVダウンロードファイル名
define('FILE_NAME_DOWNLOAD_ITEM_ORDER_TYPE', 'item_order_type.csv');

// お気に入り一覧ソート順クッキーキー
define('COOKIE_KEY_SORT_MODE', 'sm');

// お気に入り一覧ソート順クッキー有効期間(秒)
define('COOKIE_EXPIRATION_SORT_MODE', 60 * 60 * 24 * 30);


// メンテナンスキャッシュキー
define('CACHE_KEY_MAINTENANCE_FLG', 'maintenance_flg');

// 表示
define('DISPLAY', '1');

// 非表示
define('NON_DISPLAY', '0');

//すべてのメンバーグループ
define('ALL_MEMBER_GROUP', '0');

//受注データ開始年のメンバーグループ
define('ORDER_START_YEAR', '2016');

return array(
	// 一覧表示件数
	'default_per_page' => 10,

	// メールテンプレート区分
	'mail_template_div' => array(
		'1' => 'ID・パスワード通知',
		'2' => '発注受付',
		'3' => 'お知らせ'
	),

	// 発注者アカウントステータス
	'member_status' => array(
		'enable' => '1',		// 有効
		'disable' => '2'		// 無効
	),

	// 管理者アカウントステータス
	'user_status' => array(
		'enable' => '1',		// 有効
		'disable' => '2'		// 無効
	),

	// 営業担当アカウントステータス
	'sales_status' => array(
		'enable' => '1',		// 有効
		'disable' => '2'		// 無効
	),

	// メールテンプレート区分
	'template_mail_div' => array(
		'login' => '1',			// ID・パスワード通知
		'order' => '2',			// 発注受付
		'notice' => '3'			// お知らせ
	),

	// 端数処理
	'tax_rounding' => array(
		'floor' => 1,			// 切り捨て
		'ceil' => 2,			// 切り上げ
		'round' => 3			// 四捨五入
	),

	// 商品一覧表示形式
	'search_mode' => array(
		'normal' => 1,			// 通常表示
		'image' => 2,			// 画像表示
		'list' => 3,			// リスト表示
	),

	// 商品一覧ソート順
	'search_sort' => array(
		'frequency' => 1,		// 発注頻度
		'item_name' => 2,		// 商品名
		'favorite_sort' => 3,	// お気に入りソート順
	),

	// CSVフォーマット区分
	'csv_format_div' => array(
		'item' => 1,						// 商品CSV
		'pr' => 2,							// 新商品CSV
		'assign' => 3,						// 割当商品CSV
		'order' => 4,						// 受注CSV
		'member' => 5,						// 発注者CSV
		'holiday' => 6,						// 非営業日CSV
		'recommended_item' => 7,			// いつもの商品CSV
		'recommended_group_assign' => 8,	// いつものグループ割当CSV
		'delivery' => 9,					// 納品先CSV
		'sales_representative' => 10,		// 営業担当者CSV
		'delivery_week' => 11,				// 配達曜日CSV
		'group_assign' => 12,				// グループ割当商品CSV
		'item_order_type' => 13,			// グループ割当商品CSV
	),

	// 管理側ユーザグループ種類
	'manage_group' => array(
		'1' => 'Administrators',
		'2' => 'Users'
	),

	// 管理側ページラベル種類
	'manage_page_label' => array(
		'1' => 'adminPage',
		'2' => 'userPage'
	),

	// 集計一覧ソート順
	'search_sort_summary_item' => array(
		'item_code_desc' => 1,			// 商品コード(降順)
		'item_name_desc' => 2,			// 商品名(降順)
		'amount_desc' => 3,				// 数量(バラ)(降順)
		'amount_case_desc' => 4,		// 数量(ケース)(降順)
		'item_code_asc' => 11,			// 商品コード(昇順)
		'item_name_asc' => 12,			// 商品名(昇順)
		'amount_asc' => 13,				// 数量(バラ)(昇順)
		'amount_case_asc' => 14,		// 数量(ケース)(昇順)
	),

	// 出荷区分
	'shipping_div' => array(
		'1' => '1',
		'80' => '80',
		'90' => '90'
	),

	// 出荷区分(表示)
	'shipping_div_disp' => array(
		'1' => '掛売上',
		'80' => '配送',
		'90' => '発送'
	),

	// 倉庫区分
	'warehouse_div' => array(
		'000900' => '000900',
		'001400' => '001400',
		'002090' => '002090',
		'009999' => '009999'
	),

	// 倉庫区分(表示)
	'warehouse_div_disp' => array(
		'000900' => 'ﾗｸﾅﾛｼﾞｽﾃｨｸｽ㈱',
		'001400' => '川越事業所',
		'002090' => 'ﾛｼﾞ預り品専用',
		'009999' => '直送'
	),

	// 商品タイプ
	'item_type' => array(
		'material' => '000000',
		'stock' => '000001',
		'order' => '000002',
		'special' => '000003',
		'other' => '000004'
	),

	// 商品タイプ(表示)
	'item_type_disp' => array(
		'000000' => '原料',
		'000001' => 'ﾛｼﾞｽ在庫',
		'000002' => '取寄品',
		'000003' => '取置・別製',
		'000004' => 'その他'
	)

);