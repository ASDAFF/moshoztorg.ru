<?

foreach ($arResult['ITEMS'] as &$arItem){
    if ( is_array($arItem['PREVIEW_PICTURE']) ) {
        $file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>640, 'height'=>280), BX_RESIZE_IMAGE_EXACT, true);
        $arItem['PREVIEW_PICTURE']['SRC'] = $file['src'];
        $arItem['PREVIEW_PICTURE']['WIDTH'] = $file['width'];
        $arItem['PREVIEW_PICTURE']['HEIGHT'] = $file['height'];

    }

}

?>