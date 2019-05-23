<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //путь для подключения файлов include-ом
    $CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

	?>




  <?
    $phone = $arResult['PHONES']['global'];

  ?>

    <span class="phone-link light">
        <? echo file_get_contents($CurPath.'/svg/phone.svg'); ?>
        <?=$phone['html']?>
    </span>
    <p class="phone-shop">Интернет-магазин</p>
  <?
    if(($phone = $arResult['PHONES']['local']) && $phone['number']){
        ?>
        <span class="phone-link light">
            <? echo file_get_contents($CurPath.'/svg/phone.svg'); ?>
            <?=$phone['html']?>
        </span>
        <?
    }
?>
