<?php header('Access-Control-Allow-Origin: *');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Поиск');

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs( "/_search/jquery-ui.min.js");
Asset::getInstance()->addJs( "/_search/script.js");

Asset::getInstance()->addCss( "/_search/jquery-ui.min.css");





?><style>

    #search_result input[type=checkbox] + label {
        color: #ccc;
        font-style: italic;
    }
    #search_result  input[type=checkbox]:checked + label {
        color: #f00;
        font-style: normal;
    }

</style>
<div class="gtcontainer" style="min-height: 400px">


    <form name="seach" id="search_form" method="POST" action="request.php">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="offset" value="0">
        <input type="text" name="q" value="<?=strip_tags($_GET['q'])?>" placeholder="поиск...">
        <input type="submit" name="sub" value="Поиск"><input type="button" name="reset" value="Сбросить фильтры">
    </form>


    <div id="search_result"></div>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>