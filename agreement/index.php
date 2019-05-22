<?	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");	$APPLICATION->SetTitle("Соглашение");?>	
<div class="styles_page">
	<div class="styles">
	<?
	$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR.'/include/agreement.php'
	));
	?>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>