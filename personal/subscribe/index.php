<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?>
<div class="personal new-subscribe">
	<h1>Рассылка</h1>
	<?$APPLICATION->IncludeComponent("bitrix:subscribe.edit", "template1", Array(
		"SHOW_HIDDEN" => "N",	// Показать скрытые рубрики подписки
			"ALLOW_ANONYMOUS" => "Y",	// Разрешить анонимную подписку
			"SHOW_AUTH_LINKS" => "Y",	// Показывать ссылки на авторизацию при анонимной подписке
			"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
			"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
			"AJAX_MODE" => "N",	// Включить режим AJAX
			"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
			"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
			"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
			"CACHE_TYPE" => "A",	// Тип кеширования
			"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
		),
		false
	);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>