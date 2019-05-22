<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (is_array($arResult['PREVIEW_PICTURE'])) {
    $file = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE'], array('width'=>250, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $arResult['PREVIEW_PICTURE']['SRC'] = $file['src'];
    $arResult['PREVIEW_PICTURE']['WIDTH'] = $file['width'];
    $arResult['PREVIEW_PICTURE']['HEIGHT'] = $file['height'];
}