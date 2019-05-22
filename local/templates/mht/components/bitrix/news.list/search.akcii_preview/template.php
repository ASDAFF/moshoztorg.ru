<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>





<div class="search-actions">



    <h1>Акции</h1>
    <div class="clearer owl-carousel owl-theme">


        <? foreach ($arResult["ITEMS"] as $arItem): ?>

            <? if (is_array($arItem["PREVIEW_PICTURE"])) { ?>

                <div class="action-block-container item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <div class="action-block">
                       <!-- <a class="action-title" href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a> -->
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                            <img
                                    src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                    width="100%"
                                    alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                                    title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                            >
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
            <? } ?>
        <? endforeach; ?>

    </div>

</div>
<br><br>

