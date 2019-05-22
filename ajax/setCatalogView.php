<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!empty($_REQUEST["VIEW"])){
	if($_REQUEST["VIEW"] == "block"){
		$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"] = true;
	}else{
		$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"] = false;
	}
}
?>