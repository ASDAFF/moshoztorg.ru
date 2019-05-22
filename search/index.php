<?php header('Access-Control-Allow-Origin: *');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Поиск');

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs( "/search/jquery-ui.min.js");
Asset::getInstance()->addJs( "/local/templates/mht/js_/catalog.js");
Asset::getInstance()->addCss( "/search/jquery-ui.min.css");
Asset::getInstance()->addCss( "/search/searchStyle.css");
Asset::getInstance()->addJs( "/search/script_search.js");

$APPLICATION->SetAdditionalCSS("/search/owl.carousel/owl.carousel.min.css");
$APPLICATION->AddHeadScript("/search/owl.carousel/owl.carousel.min.js");

?><div class="gtcontainer search_block_wrap" style="min-height: 400px">

    <div class="field">
    <form name="seach" id="search_form" method="POST" data-ajax-action="request.php" action="/search/">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="offset" value="0">
        <input type="text" name="q" value="<?=strip_tags($_GET['q'])?>" placeholder="поиск..." class="search_field hayhopped">
        <input type="submit" name="sub" value=" " class="search_submit">
        <input type="hidden" name="order" value="0">
        <input type="hidden" name="discount" value="0">
        <?/*<input type="button" name="reset" value="Сбросить фильтры">*/?>
    </form>
    </div>

    <div id="current_filter"></div>

    <div id="search_result"></div>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>