<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);


$sCurrentPage = $APPLICATION->GetCurPage();

if ( isset($arResult['SECTIONS'][0])){
$bHiddenBlockShown = false;
?>
<p class="heading">Разделы</p>
<ul><?
foreach ($arResult['SECTIONS'] as $k=>$arSection){
    $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
    $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
?><?if ($k>=$arParams['ITEMS_IN_BLOCK']  && !$bHiddenBlockShown){
    $bHiddenBlockShown = true;
    ?><div class="hidden_list"><?
    }
?><li id="<?= $this->GetEditAreaId($arSection['ID']); ?>" <?=strpos($sCurrentPage,$arSection['SECTION_PAGE_URL'])!==false?'class="active"':'';?> ><a href="<? echo $arSection["SECTION_PAGE_URL"]; ?>"><? echo $arSection["NAME"]; ?><?
            if ($arParams["COUNT_ELEMENTS"] && $arSection["ELEMENT_CNT"] ) {
	?> <?
            }
        ?></a></li><?
    }
    unset($arSection);
    ?><?if ( $bHiddenBlockShown ){?></div><?}

    ?><?if ( ($iItemsCount=count($arResult['SECTIONS']))>$arParams['ITEMS_IN_BLOCK']):?>
    <li class="showhidden"><a href="javascript:void(0)">Показать скрытые (<?=$iItemsCount-$arParams['ITEMS_IN_BLOCK']?>)</a></li>
    <li class="hidehidden"><a href="javascript:void(0)">Минимизировать список</a></li>
<?endif?>
<?}?></ul>
