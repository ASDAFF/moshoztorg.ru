<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

?>


    <!--===================================================== section latest -->
    <section class="section-latest" id="latest">
        <div class="container">

            <div class="sec-title">

                <?$APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                Array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => SITE_TEMPLATE_PATH."/include/new_header.php"
                )
                );?>

            </div>

            <div class="row">

<?

foreach ($arResult['ITEMS'] as $key_original => $arItem) {

?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>


    <?
    //делаем блоки по 7 элементов
    if ($key_original%7==0)
        $key = 0;

    $animation = 'slide-in-from-left';
    $col = 'col-1-3';

    if ( $key>2 && $key<5 )
       $animation = 'slide-in-from-right';

    if (( $key == 3 ) || ($key == 6) )
        $col = 'col-7-12';

    if (( $key == 4 ) || ($key == 5) )
        $col = 'col-5-12';

    // добавляем строку
    if ( in_array($key,[3,5]) ) {
        ?>
            </div>
            <div class="row">
        <?
    }

    $key++;

    ?>


               <div class="<?=$col?> col-xs-1 cre-animate" data-animation="<?=$animation?>"
                     data-speed="900"
                     data-delay="600" data-offset="90%" data-easing="easeOutQuint">
                    <a href="<?=$arItem["PROPERTIES"]['LINK']["VALUE"]?>" class="main-link">
                        <div class="latest">
                            <img class="hidden-xs" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt=" ">
                            <img class="visible-xs" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt=" ">
                            <div class="latest-desc">
                                <div class="latest-news-zhenya">
                                    <div class="pseudo-table">
                                        <div class="pseudo-table-cell">
                                            <div class="latest-percent">NEW</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="latest-descHolder">
                                    <div class="med-title">
                                        <?=$arItem['~DETAIL_TEXT']?$arItem['~DETAIL_TEXT']:$arItem['~NAME']?>
                                    </div>
                                    <div class="latest-info">
                                        <?=$arItem['~PREVIEW_TEXT']?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>









    <?

}
?>
            </div>


            </div>
        </div>
    </section>

