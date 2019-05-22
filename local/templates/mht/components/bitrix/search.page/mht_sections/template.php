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

if(empty($arResult['REQUEST']['QUERY'])){
	?>
	<div class="search_results_page">
    	<div class="search_results">
			<h1>Поиск</h1>
        	<div class="search_result_list">
				Введите запрос в поисковую строку.
			</div>
		</div>
	</div>
	<?
	return;
}
?>
<h1 class="gtsearchh1">Результаты поиска по запросу «<?=$arResult['REQUEST']['QUERY']?>»</h1>
<div class="filter_aside">
  <div class="search_results">
	<div class="search_result_list">
		<? /* if($GLOBALS['USER']->IsAdmin()){ ?>
			<pre><?= print_r($arResult); ?></pre>
		<? } */ ?>
		<? if($arResult['SEARCH']): ?>
			<? if( isset($arResult['SECTIONS_FILTER'][0])){ ?>
			<div class="search-found-sections filter-sections">
				<h2 class="gtxpad">Найдены товары в категориях:</h2>
				<? foreach ($arResult['SECTIONS_FILTER'] as $arSection) {


						$count = isset($arResult['SECTIONS_ELEMENT_COUNTS'][$arSection['SECTION_CODE']])?$arResult['SECTIONS_ELEMENT_COUNTS'][$arSection['SECTION_CODE']]:1;
					?>
				<a href="<?= $arSection['URL'] ?>" class="search-found-section">
					<?/*<ul class="search-found-section-chain">
									<? array_pop($arSection['PATH']); foreach ($arSection['PATH'] as $key => $arChain) { ?>
										<li><?= $arChain['NAME'] ?></li>
									<? } ?>
								</ul>*/?>
					<div class="search-found-section-title">
						<span class="catname"><?=$arSection['TITLE_FORMATED']?></span>
						<?if (!isset($_GET['tags'])):?>
						<span class="elements-count"><?=$count ?></span>
						<?endif?> 
					</div>
				</a>
				<? } ?>
			</div>
			<? } ?>
			<? if($arResult['SECTIONS']){ ?>
				<div class="search-found-sections catfounds">
					<h2 class="gtxpad">Найдены разделы каталога:</h2>
					<? foreach ($arResult['SECTIONS'] as $arSection) { ?>
						<div class="search-found-section">
							<?/*<ul class="search-found-section-chain">
								<? array_pop($arSection['PATH']); foreach ($arSection['PATH'] as $key => $arChain) { ?>
									<li><a href="<?= $arChain['SECTION_PAGE_URL'] ?>"><?= $arChain['NAME'] ?></a></li>
								<? } ?>
							</ul>*/?>
							<div class="search-found-section-title"><a href="<?= $arSection['URL'] ?>"><?= $arSection['TITLE_FORMATED'] ?></a></div>
						</div>
					<? } ?>
				</div>
			<? } ?>

			

			<?if (isset($_GET['tags'])):?>
			<p class="kill_filter"><a href="<?=$arResult['SHOW_ALL_SECTION_FILTER_LINK'];?>">Сбросить фильтр</a></p>
			<?endif?>


		<? endif; ?>
	</div>
  </div>
</div>
