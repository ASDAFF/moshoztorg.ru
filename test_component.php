<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
?>


<?

global $APPLICATION, $USER;

$arEventFields = array(
    "COUPON"    => 'coupon',
    "ACTIVE_TO" => 'active-to',
    "EMAIL"     => 'dmitry_plus@mail.ru',
    "USER_ID"   => 1234,
);

\CEvent::Send("ITSFERA_ORDER_COUPON", 'el', $arEventFields, 'Y', 174);

?>


<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
