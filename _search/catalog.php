<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

define('SEARCH_ITEMS_COUNT',15);

error_reporting(E_ERROR ); //| E_NOTICE
ini_set('display_errors', 1);

use Bitrix\Main\Loader;
Loader::includeModule("iblock");
$arResult = \Bitrix\Main\Web\Json::decode( $_POST['data'] ) ;

$iOffset = intval($_POST['offset'] );

if ( isset($arResult['results']['total']) ):
    ?><p>Найдено: <?=$arResult['results']['total']?></p><hr><?
    endif;


if ( isset($arResult['results']['filters']) ){
    ?><div class="filters" id="search_filters"><?



    foreach ($arResult['results']['filters'] as $kf=>$arFilter){

        if ( $arFilter['name'] == 'Цена' ) continue;

        if ( $arFilter['name'] == 'category' ) {

            $arSections = [];
            $arSectionsNames = [];


            foreach ($arFilter['values'] as $arSection){
                $arSections[] = $arSection['value'];
            }

            $arSectionFilter = Array( 'ID'=>$arSections);

            $db_list = CIBlockSection::GetList(Array($by=>$order), $arSectionFilter, true);
            while($arSection = $db_list->GetNext()){
                $arSectionsNames[ $arSection['ID'] ] = $arSection['NAME'];
            }

        }
        ?><div class="filter">
        <p><b><?=$arFilter['name']?></b></p><?
        foreach ( $arFilter['values'] as $kv=>$arValue):

            if ( $arFilter['name'] == 'category' ) {

            ?><input id="<?=$kf.$kv?>" type="checkbox" name="<?=$arFilter['name']?>"  value="<?=$arValue['value']?>">
                <label for="<?=$kf.$kv?>"><?=$arSectionsNames[$arValue['value']]?></label><?

            }else {

                ?><input id="<?= $kf . $kv ?>" type="checkbox" name="<?= $arFilter['name'] ?>"
                         value="<?= $arValue['value'] ?>">
                <label for="<?= $kf . $kv ?>"><?= $arValue['value'] ?></label><?
            }

        endforeach;?></div><?
    }

    ?></div><hr><?
}



if ( isset($arResult['results']['items'][0]) ) {


$APPLICATION->IncludeComponent(
    "itsfera:detectum_search",
    "",
    Array("ITEMS_IDS"=>$arResult['results']['items'])
);



?>

    <?

    $iItemsOnPageCount = SEARCH_ITEMS_COUNT;
    $iItemsCountAll = $arResult['results']['total'];
    $iPagesCount = ceil( $iItemsCountAll/$iItemsOnPageCount );



    ?><?if ($iPagesCount>1):?><div class="pagination">
        <nav>
            <ul class="pagination_center" id="search_pagination">
                <?for($iPage = 0; $iPage<$iPagesCount; $iPage++):?>
                    <?if ($iOffset==$iPage*$iItemsOnPageCount):?>
                        <li><span><?=$iPage+1?></span></li>
                    <?else:?>
                        <li><a data-offset="<?=$iPage*$iItemsOnPageCount?>" href="#"><?=$iPage+1?></a></li>
                    <?endif?>
                <?endfor?>

            </ul>

            <?/*
            <a href="#" class="pagination_right"></a>
            */?>
        </nav>
    </div><?endif?><?


}