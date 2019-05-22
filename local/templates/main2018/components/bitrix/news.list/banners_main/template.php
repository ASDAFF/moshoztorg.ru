<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

?>

    <!--===================================================== section heading -->
    <section class="section-heading" id="heading">
        <div class="heading-slider-holder">
            <div class="heading-slider swiper-container">
                <div class="swiper-wrapper">

<?

foreach ($arResult['ITEMS'] as $arItem) {

?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
    <!-- single slide -->
    <div class="swiper-slide">
        <div class="heading-slide">
            <img class="osx-img hidden-xs" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt=" ">
            <img class="visible-xs" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt=" ">
            <div class="container-holder">

                <div class="container">
                    <div class="row">

                        <div class="slide-info">
                            <div class="big-title">

                                <?=$arItem['~DETAIL_TEXT']?$arItem['~DETAIL_TEXT']:$arItem['NAME']?>

                                <div class="big-subtitle">

                                     <?=$arItem['PREVIEW_TEXT']?>

                                </div>
                                <div class="slide-price"> <?=$arItem['PROPERTIES']['PRICE']['VALUE']?> </div>
                            </div>
                            <a href="<?=$arItem['PROPERTIES']['BUTTON_LINK']['VALUE']?>" class="btn" data-extra="1">
                                <div class="pseudo-table">
                                    <div class="pseudo-table-cell">
                                        <?=$arItem['PROPERTIES']['BUTTON_TEXT']['VALUE']?>
                                    </div>
                                </div>
                            </a>
                            <div class="slide-until"><?=$arItem['PROPERTIES']['UNTIL_TEXT']['VALUE']?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?

}
?>

                </div>
            </div>

            <!-- pagination -->
            <div class="swiper-pagination heading-pagination"></div>

            <!-- navigation buttons -->
            <div class="pagination_buttons_">
                <div class="swiper-button-prev heading-prev hidden-xs">
                    <? echo file_get_contents($CurPath.'/svg/arrow-left.svg'); ?>
                </div>
                <div class="swiper-button-next heading-next hidden-xs">
                    <? echo file_get_contents($CurPath.'/svg/arrow-right.svg'); ?>
                </div>
            </div>
        

        </div>
    </section>


