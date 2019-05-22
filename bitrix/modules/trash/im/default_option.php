<?php
$im_default_option = array(
	'path_to_user_profile' => (IsModuleInstalled("intranet") ? '/company/personal/user/#user_id#/':'/club/user/#user_id#/'),
	'path_to_user_lf' => (IsModuleInstalled("intranet") ? '/company/personal/log/':'/club/log/'),
	'path_to_user_bp' => '/company/personal/bizproc/',
	'turn_server_self' => 'N',
	'turn_server' => 'turn.calls.bitrix24.com',
	'turn_server_firefox' => '54.217.240.163',
	'turn_server_login' => 'bitrix',
	'turn_server_password' => 'bitrix',

	'view_offline' => true,
	'view_group' => true,
	'send_by_enter' => false,
	'panel_position_horizontal' => 'right',
	'panel_position_vertical' => 'bottom',
	'load_last_message' => true,
	'load_last_notify' => true,
	'privacy_message' => 'all',
	'privacy_chat' => IsModuleInstalled('intranet')? 'all': 'contact',
	'privacy_call' => IsModuleInstalled('intranet')? 'all': 'contact',
	'privacy_search' => 'all',
	'privacy_profile' => 'all',
);
?>