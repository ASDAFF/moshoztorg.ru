<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    $APPLICATION->SetTitle('Robokassa');
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:sale.order.payment.receive",
    "",
Array(
'PAY_SYSTEM_ID' => 17,
'PERSON_TYPE_ID' => 1
),
false
);?>
<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>