<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'] . '/' . SITE_TEMPLATE_PATH;

\Bitrix\Main\Page\Asset::getInstance()->addJs("jquery.scrollbar.js");

?>

<nav class="catalog-nav float-left top-menu">

    <ul class="nav">
        <li class="catalog-toggle">
            <a href="javascript:void(0)" class="dark catalog-btn">
                <div class="show-menu">
                    <span class="icon-sandwich"></span>
                </div>
                Каталог
            </a>

            <div class="tabs gtxnotmob mhtCatalog" >
                <ul class="gtx_second_level ">
                    <? $k = 0;
                    $t    = 0; ?>
                    <? foreach ($arResult as $ID => $arItem): ?>

                        <li data-index="#tab-<?= $k;
                        $k++; ?>" data-page="<?= $t;
                        $t++; ?>">
                            <a href="<?= $arItem['LINK'] ?>" data-index="<?= $ID ?>">
                                <div class="gtx_imgholder" data-jaja = "<?=$arItem["PARAMS"]["CODE"]?>">
                                    <? echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/menu_icons/' . $arItem["PARAMS"]["CODE"] . '.svg'); ?>
                                </div>
                                <p><?= $arItem['TEXT'] ?></p>
                            </a>
                        </li>
                    <? endforeach; ?>
                </ul>
                <div class="submenu_left">
                    <div class="submenu_left__wrap">
                        <? $j = 0; ?>
                        <? foreach ($arResult as $ID => $arItem): ?>

                            <div class="gtx_third_level" id="tab-<?= $j;
                            $j++; ?>" style="display: none;">
                                <div class="levelcontentwrapper">
                                <div class="heightcheck">
                                <ul>
                                    <? foreach ($arItem['CHILDREN'] as $arItemFirstChild): ?>
                                        <li>
                                            <a href="<?= $arItemFirstChild['LINK'] ?>"
                                               class="sub_title"><?= $arItemFirstChild['TEXT'] ?></a>

                                            <div class="sub_desc">
                                                <?

                                                $arSecondItems = array();

                                                foreach ($arItemFirstChild['CHILDREN'] as $key => $arItemSecondChild):

                                                    if (in_array($arItemSecondChild['TEXT'], $arResult['isSHOW'])) {
                                                        $arSecondItems[] = '<a href="' . $arItemSecondChild['LINK'] . '">' . $arItemSecondChild['TEXT'] . '</a>';
                                                    }


                                                endforeach;

                                                echo implode('<br>', $arSecondItems); ?>

                                                <? if (count($arSecondItems) > 0) { ?>
                                                    <a href="<?= $arItemFirstChild['LINK'] ?>" class="all">Посмотреть все</a>
                                                <? } ?>
                                            </div>

                                        </li>
                                    <? endforeach; ?>


                                </ul>

                                <? if ($arItem['BRANDS']) { ?>
                                    <div class="brands_img">
                                        <? foreach ($arItem['BRANDS'] as $arBrand): ?>
                                            <a href="<?= $arBrand['DETAIL_PAGE_URL'] ?>" alt="<?= $arBrand['NAME'] ?>"
                                               title="<?= $arBrand['NAME'] ?>"><img src="<?= $arBrand['SRC'] ?>"/></a>
                                        <? endforeach; ?>
                                    </div>
                                <? } ?>
                                </div>
                                </div>
                            </div>
                        <? endforeach; ?>

                    </div>

                </div>

            </div>
        </li>
        <li class="forfix"><a href="/aktsii/" class="sale">АКЦИИ</a></li>
        <? $APPLICATION->IncludeComponent("bitrix:menu", "catalog_top", Array(
            "ROOT_MENU_TYPE"        => "catalog2018",
            "MAX_LEVEL"             => "1",
            "USE_EXT"               => "N",
            "DELAY"                 => "N",
            "ALLOW_MULTI_SELECT"    => "N",
            "MENU_CACHE_TYPE"       => "N",
            "MENU_CACHE_TIME"       => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS"   => "",
        ),
            false
        ); ?>


    </ul>

</nav>
