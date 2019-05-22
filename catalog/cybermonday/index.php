<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Киберпонедельник");
?><div class="blackfridaysale2015">
	<div class="attention-block">
	</div>
	 <?$APPLICATION->IncludeComponent(
	"mht:offers",
	"blackfriday",
	Array(
		"CACHE_TIME" => 86400,
		"CACHE_TYPE" => "A",
		"HIDE_BLACKFRIDAY_LABEL" => "Y",
		"NAME" => "Киберпонедельник",
		"PRODUCTS_BLOCK_VIEW_BLOCK" => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
		"SECTION_ID" => $_REQUEST['SECTION_ID'],
		"SHOW_SECTIONS" => "Y",
		"TYPE" => "SAYT_BLACK_FRIDAY_TOVAR"
	)
);?>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>