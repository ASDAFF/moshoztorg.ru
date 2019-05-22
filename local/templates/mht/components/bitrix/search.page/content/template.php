<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<?if($arResult['SEARCH']): ?>
    <br clear="both">
    <div class="search-in-content">
		<h2>Найдено на страницах сайта:</h2>
		<? foreach ($arResult['SEARCH'] as $key => $arItem) { ?>
			<div class="search-in-content__item">
				<div class="search-in-content__chain"><?= $arItem['CHAIN_PATH'] ?></div>
				<div class="search-in-content__title"><a href="<?= $arItem['URL_WO_PARAMS'] ?>"><?= $arItem['TITLE_FORMATED'] ?></a></div>
				<div class="search-in-content__description"><?= $arItem['BODY_FORMATED'] ?></div>
			</div>
		<? } ?>
	</div>
<?endif;?>

<?/*<pre>
	<? print_r($arResult['SEARCH']); ?>
</pre>*/?>
