<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

?>

    <!--===================================================== section stock -->
    <section class="section-stock" id="stock">
        <div class="container">

            <div class="sec-title">

                        <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_TEMPLATE_PATH."/include/action_first_header.php"
                        )
                        );?>

            </div>
            <div class="row">

<?

foreach ($arResult['ITEMS'] as $key => $arItem) {

?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>

    <? //dm( $key ); ?>

    <? if ($key==0 || $key==1) { ?>

        <div class="col-<?=$key==0?'2':'1'?>-3 col-xs-1 cre-animate" data-animation="slide-in-from-<?=$key==0?'left':'right'?>"
             data-speed="900"
             data-delay="200" data-offset="90%" data-easing="easeOutQuint">

    <? } ?>

        <a href="<?=$arItem['PROPERTIES']['BUTTON_LINK']['VALUE']?>" class="main-link action-first">
            <div class="stock-<?=$key>0?'miniItem':'item'?>">
                <img class="hidden-xs" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt=" ">
                <img class="visible-xs" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt=" ">

                <div class="stock-desc">
                    <? if ( $arItem['PROPERTIES']['PERCENT']['~VALUE'] ) { ?>
                        <div class="stock-discount">
                            <div class="pseudo-table">
                                <div class="pseudo-table-cell">
                                    <div class="stock-percent"><?=$arItem['PROPERTIES']['PERCENT']['~VALUE']?>
                                        <? if ($arItem['PROPERTIES']['IS_PERCENT']['VALUE']) { ?>
                                            <sup>%</sup>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    <div class="stock-descHolder">

                        <div class="med-title">
                            <?=$arItem['~DETAIL_TEXT']?$arItem['~DETAIL_TEXT']:$arItem['NAME']?>
                        </div>
                        <div class="stock-info">
                            <?=$arItem['PREVIEW_TEXT']?>
                        </div>
                        <div class="btn" data-extra="1">
                            <div class="pseudo-table">
                                <div class="pseudo-table-cell">
                                     <?=$arItem['PROPERTIES']['BUTTON_TEXT']['VALUE']?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>

    <? if ($key==0 || $key==2) { ?>

        </div>

    <? } ?>




    <?

}
?>

            </div>
        </div>
    </section>

