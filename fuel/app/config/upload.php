<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(
	/**
	 * global configuration
	*/

	// if true, the $_FILES array will be processed when the class is loaded
	'auto_process'		=> false,

	/**
	 * file validation settings
	*/

	// maximum size of the uploaded file in bytes. 0 = no maximum
	'max_size'			=> 2097152,

	// list of file extensions that a user is allowed to upload
	'ext_whitelist'		=> array(),

	// list of file extensions that a user is NOT allowed to upload
	'ext_blacklist'		=> array(),

	// list of file types that a user is allowed to upload
	// ( type is the part of the mime-type, before the slash )
	'type_whitelist'	=> array(),

	// list of file types that a user is NOT allowed to upload
	'type_blacklist'	=> array(),

	// list of file mime-types that a user is allowed to upload
	'mime_whitelist'	=> array(),

	// list of file mime-types that a user is NOT allowed to upload
	'mime_blacklist'	=> array(),

	/**
	 * file save settings
	*/

	// prefix given to every file when saved
	'prefix'			=> '',

	// suffix given to every file when saved
	'suffix'			=> '',

	// replace the extension of the uploaded file by this extension
	'extension'			=> '',

	// default path the uploaded files will be saved to
	'path'				=> '',

	// create the path if it doesn't exist
	'create_path'		=> true,

	// permissions to be set on the path after creation
	'path_chmod'		=> 0777,

	// permissions to be set on the uploaded file after being saved
	'file_chmod'		=> 0666,

	// if true, add a number suffix to the file if the file already exists
	'auto_rename'		=> false,

	// if true, overwrite the file if it already exists (only if auto_rename = false)
	'overwrite'			=> true,

	// if true, generate a random filename for the file being saved
	'randomize'			=> false,

	// if true, normalize the filename (convert to ASCII, replace spaces by underscores)
	'normalize'			=> false,

	// valid values are 'upper', 'lower', and false. case will be changed after all other transformations
	'change_case'		=> false,

	// maximum lengh of the filename, after all name modifications have been made. 0 = no maximum
	'max_length'		=> 0,

	'setting' => array(
		'item_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'item_image_zip' => array(
			'ext_whitelist' => array('zip'),
			'type_whitelist' => array('application'),
			'extension' => 'zip',
			'max_size' => 20971520
		),
		'item_pdf_zip' => array(
			'ext_whitelist' => array('zip'),
			'type_whitelist' => array('application'),
			'extension' => 'zip',
			'max_size' => 20971520
		),
		'pr_item_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'assign_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'recommended_item_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'recommended_group_assign_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'member_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		),
		'item_image' => array(
			'ext_whitelist' => array('jpg', 'jpeg'),
			'type_whitelist' => array('image'),
			'path' => ITEM_IMAGE_PATH,
			'extension' => 'jpg',
			'max_size' => 2097152
		),
		'item_pdf' => array(
			'ext_whitelist' => array('pdf'),
			'type_whitelist' => array('application'),
			'path' => ITEM_PDF_PATH,
			'extension' => 'pdf',
			'max_size' => 2097152
		),
		'notice_image' => array(
			'ext_whitelist' => array('jpg', 'jpeg'),
			'type_whitelist' => array('image'),
			'path' => NOTICE_IMAGE_PATH,
			'extension' => 'jpg',
			'max_size' => 102400
		),
		'pr_image' => array(
			'ext_whitelist' => array('jpg', 'jpeg', 'gif', 'png'),
			'type_whitelist' => array('image'),
			'path' => PR_IMAGE_PATH,
			'max_size' => 2097152
		),
		'logo_image' => array(
			'ext_whitelist' => array('png'),
			'type_whitelist' => array('image'),
			'path' => LOGO_IMAGE_PATH,
			'new_name' => 'order_logo.png',
			'auto_process ' => false,
			'max_size' => 102400
		),
		'order_login_logo_image' => array(
			'ext_whitelist' => array('png'),
			'type_whitelist' => array('image'),
			'path' => ORDER_LOGIN_LOGO_IMAGE_PATH,
			'new_name' => 'order_login_logo.png',
			'auto_process ' => false,
			'max_size' => 102400
		),
		'webclip_image' => array(
			'ext_whitelist' => array('png'),
			'type_whitelist' => array('image'),
			'path' => WEBCLIP_IMAGE_PATH,
			'new_name' => 'apple-touch-icon.png',
			'max_size' => 102400
		),
		'no_image' => array(
			'ext_whitelist' => array('jpg'),
			'type_whitelist' => array('image'),
			'path' => NO_IMAGE_PATH,
			'new_name' => 'noimage.jpg',
			'max_size' => 2097152,
			'auto_process ' => false
		),
		'notice_pdf' => array(
			'ext_whitelist' => array('pdf'),
			'type_whitelist' => array('application'),
			'path' => NOTICE_PDF_PATH,
			'extension' => 'pdf',
			'max_size' => 2097152
		),
		'holiday_csv' => array(
			'ext_whitelist' => array('csv'),
			'type_whitelist' => array('text'),
			'extension' => 'csv',
			'max_size' => 20971520
		)
	),

	'message_replaces' => array(
		'item_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'item_image_zip' => array(
			'extension' => 'ZIP',
			'max_size' => '20MB'
		),
		'item_pdf_zip' => array(
			'extension' => 'ZIP',
			'max_size' => '20MB'
		),
		'pr_item_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'assign_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'recommended_item_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'recommended_group_assign_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'member_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		),
		'item_image' => array(
			'extension' => 'JPG',
			'max_size' => '2MB'
		),
		'item_pdf' => array(
			'extension' => 'PDF',
			'max_size' => '2MB'
		),
		'notice_image' => array(
			'extension' => 'JPG',
			'max_size' => '100KB'
		),
		'pr_image' => array(
			'extension' => 'JPG,GIF,PNG',
			'max_size' => '2MB'
		),
		'logo_image' => array(
			'extension' => 'PNG',
			'max_size' => '100KB'
		),
		'order_login_logo_image' => array(
			'extension' => 'PNG',
			'max_size' => '100KB'
		),
		'webclip_image' => array(
			'extension' => 'PNG',
			'max_size' => '100KB'
		),
		'no_image' => array(
			'extension' => 'JPG',
			'max_size' => '2MB'
		),
		'notice_pdf' => array(
			'extension' => 'PDF',
			'max_size' => '2MB'
		),
		'holiday_csv' => array(
			'extension' => 'CSV',
			'max_size' => '20MB'
		)
	)
);


