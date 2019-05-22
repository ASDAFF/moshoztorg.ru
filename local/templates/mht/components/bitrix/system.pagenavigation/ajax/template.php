<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	$this->setFrameMode(true);

	if(
		$arResult["NavRecordCount"] == 0 ||
		(
			$arResult["NavPageCount"] == 1 &&
			$arResult["NavShowAll"] == false
		)
	){
		return;
	}

	$q = $arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "";
	$qf = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");



    if($arResult["NavPageNomer"] < $arResult["NavPageCount"]){
        ?>

        <div class="pagination load">

            <a href="<?=$arResult["sUrlPath"].'?'.$q.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1)?>" class="page-loader"

                data-pages="<?=$arResult['NavPageCount']?>"
                data-items="<?=$arResult['NavRecordCount']?>"
                data-itemsonpage="<?=$arResult['NavPageSize']?>"
                data-offset="<?=$arResult['NavNum']?>"

            >
                <img src="<?=$this->GetFolder()?>/images/ajax-loader.gif">
            </a>

        </div>
        <br clear="all">
    <? } ?>



