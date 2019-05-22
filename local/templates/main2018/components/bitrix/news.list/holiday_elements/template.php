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
?>
<div class="row">


<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>

        <div class="col-1-3 col-xs-1 cre-animate" data-animation="slide-in-from-left"
             data-speed="900"
             data-delay="600" data-offset="90%" data-easing="easeOutQuint" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="productMarch8">
                <div class="productMarch8-image">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt=" ">
                </div>
                <div class="productMarch8-desc align-center">
                    <a href="<?=$arItem["PROPERTIES"]['LINK']["VALUE"]?>" class="dark"><?echo $arItem["NAME"]?></a><br/>
                    <div class="productMarch8-offtop">
                        <?=$arItem["PREVIEW_TEXT"]?>
                    </div>
                </div>
            </div>
        </div>

    <? if ( ($key+1)%3 == 0 ) {
        ?>
</div>
<div class="row">
        <?

    } ?>

<?endforeach;?>


</div>
