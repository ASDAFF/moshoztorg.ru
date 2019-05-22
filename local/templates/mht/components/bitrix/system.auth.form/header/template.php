<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
 GLOBAL $APPLICATION, $USER;
 
 //костыль чтобы передать данные об ошибке компоненту авторизации на странице
 
 if ($arResult["ERROR"]) {
	 session_start();
	 $_SESSION['BX_ERROR'] = $arResult["ERROR_MESSAGE"];	 
 } else {
	 session_start();
	 unset ($_SESSION['BX_ERROR']);
 }
 
 
?>
    <div class="authorization-entry">
<? if ( !$USER->isAuthorized()) {

    //путь для подключения файлов include-ом
    $CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

    ?>

        <a href="#" class="open-authorization-modal offtop" data-extra="1">Вход</a>
        &nbsp;/&nbsp;&nbsp;
        <a href="https://<?=SITE_SERVER_NAME?>/personal/register/" class="offtop">Регистрация</a>


<?}else{?>

		<a href="/personal/" class="offtop">
		<?=$USER->GetFirstName().' '.substr($USER->GetLastName(),0,1).'.'?></a>
		&nbsp;/&nbsp;&nbsp;
		<a href="#" class="exit js-unlog-button offtop">Выход</a>

<?}?>

    </div>