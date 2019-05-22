<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define("PUBLIC_AJAX_MODE", true);

header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

if (!CModule::IncludeModule("socialservices"))
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'IM_MODULE_NOT_INSTALLED'));
	die();
}
if (intval($USER->GetID()) <= 0)
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'AUTHORIZE_ERROR'));
	die();
}

if (check_bitrix_sessid())
{
	CUtil::JSPostUnescape();
	if($_REQUEST['action'] == "getuserdata")
	{
		$arResult['SOCSERVARRAY'] = unserialize(CUserOptions::GetOption("socialservices", "user_socserv_array", '', $USER->GetID()));
		if(!is_array($arResult['SOCSERVARRAY']))
			$arResult['SOCSERVARRAY'] = '';
		$arResult['ENABLED'] = CUserOptions::GetOption("socialservices", "user_socserv_enable", "N", $USER->GetID());
		$arResult['STARTSEND'] = CUserOptions::GetOption("socialservices", "user_socserv_start_day", "N", $USER->GetID());
		$arResult['ENDSEND'] = CUserOptions::GetOption("socialservices", "user_socserv_end_day", "N", $USER->GetID());
		$arResult['STARTTEXT'] = CUserOptions::GetOption("socialservices", "user_socserv_start_text", GetMessage("JS_CORE_SS_WORKDAY_START"), $USER->GetID());
		$arResult['ENDTEXT'] = CUserOptions::GetOption("socialservices", "user_socserv_end_text", GetMessage("JS_CORE_SS_WORKDAY_END"), $USER->GetID());
		$arResult['SOCSERVARRAYALL'] = CSocServAuthManager::GetUserArrayForSendMessages($USER->GetID());

		echo CUtil::PhpToJSObject($arResult);
	}
	elseif($_REQUEST['action'] == "saveuserdata")
	{
		if(isset($_POST["ENABLED"]))
		{
			$userSocServSendEnable = $_POST["ENABLED"];
			CUserOptions::SetOption("socialservices","user_socserv_enable",$userSocServSendEnable, false,$USER->GetID());
		}
		else
		{
			$arUserSocServ = '';
			$userSocServSendEnable = $userSocServSendStart = $userSocServSendEnd = 'N';
			$userSocServEndText = GetMessage("JS_CORE_SS_WORKDAY_END");
			$userSocServStartText = GetMessage("JS_CORE_SS_WORKDAY_START");
			if(isset($_POST["SOCSERVARRAY"]) && !empty($_POST["SOCSERVARRAY"]))
				$arUserSocServ = serialize($_POST["SOCSERVARRAY"]);
			if(isset($_POST["STARTSEND"]))
				$userSocServSendStart = $_POST["STARTSEND"];
			if(isset($_POST["ENDSEND"]))
				$userSocServSendEnd = $_POST["ENDSEND"];
			if(isset($_POST["STARTTEXT"]))
				$userSocServStartText = $_POST["STARTTEXT"];
			if(isset($_POST["ENDTEXT"]))
				$userSocServEndText = $_POST["ENDTEXT"];

			CUserOptions::SetOption("socialservices","user_socserv_array",$arUserSocServ, '',$USER->GetID());
			CUserOptions::SetOption("socialservices","user_socserv_start_day",$userSocServSendStart, false,$USER->GetID());
			CUserOptions::SetOption("socialservices","user_socserv_end_day",$userSocServSendEnd, false,$USER->GetID());
			CUserOptions::SetOption("socialservices","user_socserv_start_text",$userSocServStartText, false,$USER->GetID());
			CUserOptions::SetOption("socialservices","user_socserv_end_text",$userSocServEndText, false,$USER->GetID());
		}
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>