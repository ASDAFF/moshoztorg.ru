<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


$arWaterMark = Array(
    array(
        "name" => "watermark",
        "position" => "center", // Положение
        "type" => "image",
        "size" => "real",
        "file" => $_SERVER["DOCUMENT_ROOT"].'/watermark.png', // Путь к картинке
        "fill" => "exact",
        "alpha_level"=> 40,
    )
);
$arFileTmp = CFile::ResizeImageGet(
    3929530,
    array("width" => 700, "height" => 700),
    BX_RESIZE_IMAGE_EXACT,
    true,
    $arWaterMark
);

echo '<pre>';
print_r($arFileTmp);
echo '</pre>';
echo '<img src="'.$arFileTmp['src'].'"">';