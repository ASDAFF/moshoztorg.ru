<?
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("DisableEventsCheck", true);
/************** CACHE **********************************************/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/uploader.php");
$res = new CFileUploaderServer($_SERVER["SCRIPT_NAME"]);
$result = true;
$GLOBALS["APPLICATION"]->RestartBuffer();
header('Content-Type: application/json; charset='.LANG_CHARSET);
/** @var $request \Bitrix\Main\HttpRequest */
if ($_POST["mode"] == "upload")
{
	$res->uploadData();
	$result =  array(
		"report" => $res->getLog(),
		"files" => $res->getData($res->files)
	);
	?><?=CUtil::PhpToJSObject($result);?><?
}
else if ($_POST["mode"] == "delete")
{
	$result = array("result" => $res->deleteFile($_REQUEST["hash"]));
	?><?=CUtil::PhpToJSObject($result);?><?
}
else
	$res->viewFile($_REQUEST["hash"]);
die();
?>
