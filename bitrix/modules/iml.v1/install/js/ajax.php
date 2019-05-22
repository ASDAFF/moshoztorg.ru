<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$module_id = "iml.v1";
CModule::IncludeModule($module_id);

imlHelper::getAjaxAction($_POST['action'],$_REQUEST['action']);
?>