<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//Делаем иерархию массива
$iParentDepth = $iLastDepth = 1;
$iParentKey = $iLastKey = 0;
$arParentKeys = []; //массив родительских ключей по глубине
$arMovedItems = [];
foreach($arResult as $key=>$arItem){

    $arLastItem = $arResult[$key-1];
    if ( $key>0 && $arItem['DEPTH_LEVEL']>$arLastItem['DEPTH_LEVEL'] ){

        $arParentKeys[$arItem['DEPTH_LEVEL']] = $key-1;

    }

    if ( isset($arParentKeys[$arItem['DEPTH_LEVEL']]) ){
        $iParentKey = $arParentKeys[$arItem['DEPTH_LEVEL']];
        $arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
        $arMovedItems[] = $key;
    }
}
foreach($arResult as $key=>$arItem){
    if (in_array($key,$arMovedItems)) unset($arResult[$key]);
}
$arResult = array_values($arResult);

$arCatalogMenu = array();
foreach ($arResult as $arItem){
    if ($arItem['LINK']=="/catalog/"){
        $arCatalogMenu = $arItem;
        break;
    }
}

$arResult = $arCatalogMenu['CHILDREN'];

//dump( $arResult , __FILE__, __LINE__ );

/*echo '<textarea>';
echo '$ar='.var_export($arResult,true).';';
echo '</textarea>';
die();*/


/*
 * if ( $arItem['DEPTH_LEVEL']>$iParentDepth ) {
        $iParentKey = $arParentKeys[$arItem['DEPTH_LEVEL']];
        $arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
        $arMovedItems[] = $key;
    }else ($arItem['DEPTH_LEVEL']<$iParentDepth) {
        $iParentKey = $arParentKeys[$arItem['DEPTH_LEVEL']];
        $arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
        $arMovedItems[] = $key;
    }

    if( isset($arResult[$key+1]) && $arItem['DEPTH_LEVEL']<$arResult[$key+1]['DEPTH_LEVEL'] ){ //если произошел переход уровней

        $arParentKeys[$arResult[$key+1]['DEPTH_LEVEL']] = $key; //для такой-то глубины свой ключ родительского раздела
        $iParentDepth   = $arItem['DEPTH_LEVEL'];
    }

 */