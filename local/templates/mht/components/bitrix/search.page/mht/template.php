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
<div class="search_results_page">
      <div class="search_results">
        <div class="search_result_list">
			<? if($arResult['SEARCH']): ?>
				<?if ($_REQUEST["q"]) {
					echo '<div data-retailrocket-markup-block="58886fba65bf19377063c1d0" data-search-phrase="'.htmlspecialcharsbx($_REQUEST["q"]).'"></div>';
				}?>
	        	<?
	        		foreach($arResult['SEARCH'] as $item){
	        			if($item['PARAM1'] == 'mht_products' && substr($item['ITEM_ID'], 0, 1) != 'S'){
	        				$p = MHT\Product::byID($item['ITEM_ID']);
	        				echo $p->html('search');
	        			}
	        		}
	        	?>
			    <?=$arResult["NAV_STRING"]?>
			<? else: ?>
				<?if ($_REQUEST["q"]) {
					echo '<div data-retailrocket-markup-block="58886fc35a658842d81a0401" data-search-phrase="'.htmlspecialcharsbx($_REQUEST["q"]).'"></div>';
				}?>
				
				<div class="nothing-found-in-catalog">В каталоге товаров по запросу «<?=$arResult['REQUEST']['QUERY']?>» ничего не найдено</div>
			<? endif; ?>
        </div>
      </div>
    </div>