<?	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");	$APPLICATION->SetTitle("Вакансии"); WP::loadScript('/js_/vacancies.js')?><?	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");	$APPLICATION->SetTitle("Вакансии"); WP::loadScript('/js_/vacancies.js')?>
<?$APPLICATION->IncludeComponent(
	"mht:vacancies",
	"",
	Array(
		"SEF_FOLDER" => "/o_kompanii/vakansii/",
		"SEF_MODE" => "Y"
	)
);?>
  
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>