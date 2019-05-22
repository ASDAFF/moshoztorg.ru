<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizproc/include.php");

if (!check_bitrix_sessid())
	die();
if (!CBPDocument::CanUserOperateDocumentType(CBPCanUserOperateOperation::CreateWorkflow, $GLOBALS["USER"]->GetID(), $_REQUEST['DocumentType']))
	die();

$runtime = CBPRuntime::GetRuntime();
$runtime->StartRuntime();
$documentService = $runtime->GetService("DocumentService");

CUtil::DecodeUriComponent($_REQUEST);
CUtil::DecodeUriComponent($_POST);

if (LANG_CHARSET != "UTF-8" && isset($_REQUEST['Type']['Options']) && is_array($_REQUEST['Type']['Options']))
{
	$newarr = array();
	foreach ($_REQUEST['Type']['Options'] as $k => $v)
		$newarr[CharsetConverter::ConvertCharset($k, "UTF-8", LANG_CHARSET)] = $v;
	$_REQUEST['Type']['Options'] = $newarr;
}

$v = $documentService->GetFieldInputValue($_REQUEST['DocumentType'], $_REQUEST['Type'], $_REQUEST['Field'], $_REQUEST, $arErrors);

$vp = $documentService->GetFieldInputValuePrintable($_REQUEST['DocumentType'], $_REQUEST['Type'], $v);
if (is_array($vp))
	$vp = implode(", ", $vp);

echo CUtil::PhpToJSObject(array($v, $vp));
?>