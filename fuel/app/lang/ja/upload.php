<?php

return array(
	'error_'.\Upload::UPLOAD_ERR_OK						=> '',
	'error_'.\Upload::UPLOAD_ERR_INI_SIZE				=> ':max_size以下のファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_FORM_SIZE				=> ':max_size以下のファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_PARTIAL				=> '選択に失敗しました。時間をおいて再度お試しください。',
	'error_'.\Upload::UPLOAD_ERR_NO_FILE				=> 'ファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_NO_TMP_DIR				=> 'システムエラー[NO_TMP_DIR]',
	'error_'.\Upload::UPLOAD_ERR_CANT_WRITE				=> 'システムエラー[CANT_WRITE]',
	'error_'.\Upload::UPLOAD_ERR_EXTENSION				=> 'システムエラー[EXTENSION]',
	'error_'.\Upload::UPLOAD_ERR_MAX_SIZE				=> ':max_size以下のファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_EXT_BLACKLISTED		=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED	=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_TYPE_BLACKLISTED		=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED	=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_MIME_BLACKLISTED		=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED	=> ':extensionファイルを選択してください',
	'error_'.\Upload::UPLOAD_ERR_MAX_FILENAME_LENGTH	=> 'ファイル名を:max_length以下にしてください',
	'error_'.\Upload::UPLOAD_ERR_MOVE_FAILED			=> 'システムエラー[MOVE_FAILED]',
	'error_'.\Upload::UPLOAD_ERR_DUPLICATE_FILE 		=> 'システムエラー[DUPLICATE_FILE]',
	'error_'.\Upload::UPLOAD_ERR_MKDIR_FAILED			=> 'システムエラー[MKDIR_FAILED]',
);
