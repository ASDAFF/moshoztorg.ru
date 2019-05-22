<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$module_id = "iml.v1";
CModule::IncludeModule($module_id);

imldriver::printBKs($_REQUEST['ORDER_ID']);
?>