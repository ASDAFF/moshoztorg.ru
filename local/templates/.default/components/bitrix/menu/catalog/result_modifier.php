<?
if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

//первый запрос - получаем все сущности с полем UF_SHOW_ON_MENU
//SELECT ENTITY_ID FROM b_user_field WHERE FIELD_NAME = 'UF_SHOW_ON_MENU'

//второй запрос - получаем ID и название разделов с UF_SHOW_ON_MENU=1
//SELECT allData.VALUE_ID, bs.NAME FROM (
//    SELECT VALUE_ID, UF_SHOW_ON_MENU FROM b_uts_iblock_455_section
//        UNION
//    SELECT VALUE_ID, UF_SHOW_ON_MENU FROM b_uts_iblock_457_section
//    ) as allData
//    INNER JOIN
//    b_iblock_section as bs ON bs.ID = allData.VALUE_ID
//WHERE allData.UF_SHOW_ON_MENU = 1
//;


$con = Bitrix\Main\Application::getConnection();
$res = $con->query(
    "SELECT ENTITY_ID FROM b_user_field WHERE FIELD_NAME = 'UF_SHOW_ON_MENU'"
);

$arUnion = array();
while ($arEntityID = $res->fetch()) {
    $arUnion[] = 'SELECT VALUE_ID, UF_SHOW_ON_MENU FROM b_uts_' . strtolower($arEntityID['ENTITY_ID']);
}

$res = $con->query(
    "SELECT allData.VALUE_ID, bs.NAME 
    FROM (" . implode(' UNION ', $arUnion) . ") as allData 
    INNER JOIN b_iblock_section as bs ON bs.ID = allData.VALUE_ID
    WHERE allData.UF_SHOW_ON_MENU = 1");

$arSectionIDs = array();
while ($arItem = $res->fetch()) {
    $arSectionIDs[$arItem['VALUE_ID']] = $arItem['NAME'];
}

//Делаем иерархию массива
$iParentDepth = $iLastDepth = 1;
$iParentKey   = $iLastKey = 0;
$arParentKeys = []; //массив родительских ключей по глубине
$arMovedItems = [];
foreach ($arResult as $key => $arItem) {

    $arLastItem = $arResult[$key - 1];
    if ($key > 0 && $arItem['DEPTH_LEVEL'] > $arLastItem['DEPTH_LEVEL']) {

            $arParentKeys[$arItem['DEPTH_LEVEL']] = $key - 1;

        }

    if (isset($arParentKeys[$arItem['DEPTH_LEVEL']])) {
        $iParentKey                          = $arParentKeys[$arItem['DEPTH_LEVEL']];
        $arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
        $arMovedItems[]                      = $key;
    }
}
foreach ($arResult as $key => $arItem) {
    if (in_array($key, $arMovedItems)) {
        unset($arResult[$key]);
    }
}
$arResult = array_values($arResult);

$arCatalogMenu = array();
foreach ($arResult as $arItem) {
    if ($arItem['LINK'] == "/catalog/") {
        $arCatalogMenu = $arItem;
        break;
    }
}

$arResult = $arCatalogMenu['CHILDREN'];


//собираем бренды
$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CODE", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "PROPERTY_SHOW_ON_MENU");

$arFilter = Array(
    "IBLOCK_ID"        => getIBlockIdByCode("brands"),
    "ACTIVE"           => "Y",
    "!PREVIEW_PICTURE" => false,
);
foreach ($arResult as &$item) {

    $arFilter["PROPERTY_SHOW_ON_MENU"] = $item['TEXT'];

    $res = CIBlockElement::GetList(Array(), $arFilter, false, array("nTopCount" => 5), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();

        $item['BRANDS'][] = array(
            'NAME'            => $arFields['NAME'],
            'SRC'             => CFile::GetPath($arFields['PREVIEW_PICTURE']),
            'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
        );

    }
}

$arResult['isSHOW'] = $arSectionIDs;
