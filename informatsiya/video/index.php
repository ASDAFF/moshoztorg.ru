<?	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");	$APPLICATION->SetTitle("Полезные видео"); $GLOBALS['NEWS_NAME'] = 'Видео'; ?>
<?$APPLICATION->IncludeComponent(
	"mht:youtube",
	"",
	Array(
	)
);?>
<ul id="results">
</ul><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>