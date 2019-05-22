<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach ($arResult['SECTIONS'] as $k=>&$arSection){
    $lastPart = array_pop(explode('/',  trim($arSection["SECTION_PAGE_URL"],'/')));
    $arSect = CIBlockSection::GetByID($lastPart)->GetNext();
    $arSection["SECTION_PAGE_URL"] = $arSect['SECTION_PAGE_URL'];
}

?>