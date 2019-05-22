<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;
?>

    <div class="favorited">
        <? echo file_get_contents($CurPath.'/svg/heart.svg'); ?>
        <div class="count">
            <span><?=sizeof($arResult['PRODUCTS'])?></span>
        </div>
    </div>