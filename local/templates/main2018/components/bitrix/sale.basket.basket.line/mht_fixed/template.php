<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;
?>
<div class="ordered">
    <? echo file_get_contents($CurPath.'/svg/cart.svg'); ?>
    <div class="count activated">
        <span><?=array_shift(explode(' ',MHT::getBasketItemsAmount(true)));?></span>
    </div>
</div>
<?