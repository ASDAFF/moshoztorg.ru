<?
//версия для запуска из консоли

function echo_cli($message){
    ob_start();
    echo $message,"\n";
    ob_end_flush();
    while (@ob_end_flush());
}

echo_cli('start');

$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

GLOBAL $APPLICATION, $USER;
CModule::IncludeModule("iblock");

$IBLOCK_ID = getIBlockIdByCode("discount_cards");

$arSelect = Array("ID", "NAME", "XML_ID", "PROPERTY_PERCENT", "PROPERTY_TOTAL");
$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();

    if ( $cards[$arFields['NAME']] ) {

        echo_cli("Дубль ".$arFields['NAME']);
        echo_cli("PERCENT:  ".$cards[$arFields['NAME']]['PROPERTY_PERCENT_VALUE'].' ->'.$arFields['PROPERTY_PERCENT_VALUE'] );
        echo_cli("TOTAL:    ".$cards[$arFields['NAME']]['PROPERTY_TOTAL_VALUE'].' ->'.$arFields['PROPERTY_TOTAL_VALUE'] );

    //запускаем по отдельности

        /* чистим по процентам */
//        if ($arFields['PROPERTY_PERCENT_VALUE'] > $cards[$arFields['NAME']]['PROPERTY_PERCENT_VALUE']) {
//            echo_cli("удалем ".$cards[$arFields['NAME']]['PROPERTY_PERCENT_VALUE']);
//            CIBlockElement::Delete( $cards[$arFields['NAME']]['ID'] );
//        }
//
//        if ($arFields['PROPERTY_PERCENT_VALUE'] < $cards[$arFields['NAME']]['PROPERTY_PERCENT_VALUE']) {
//            echo_cli("удалем ".$arFields['PROPERTY_PERCENT_VALUE']);
//            CIBlockElement::Delete( $arFields['ID'] );
//        }
//
//        if ($arFields['PROPERTY_PERCENT_VALUE'] == $cards[$arFields['NAME']]['PROPERTY_PERCENT_VALUE']) {
//            echo_cli("удалем ".$arFields['PROPERTY_PERCENT_VALUE']);
//            CIBlockElement::Delete( $arFields['ID'] );
//        }


        /* чистим по сумме */
//        if ($arFields['PROPERTY_TOTAL_VALUE'] > $cards[$arFields['NAME']]['PROPERTY_TOTAL_VALUE']) {
//            echo_cli("удалем ".$cards[$arFields['NAME']]['PROPERTY_TOTAL_VALUE']);
//            CIBlockElement::Delete( $cards[$arFields['NAME']]['ID'] );
//        }
//
//        if ($arFields['PROPERTY_TOTAL_VALUE'] < $cards[$arFields['NAME']]['PROPERTY_TOTAL_VALUE']) {
//            echo_cli("удалем ".$arFields['PROPERTY_TOTAL_VALUE']);
//            CIBlockElement::Delete( $arFields['ID'] );
//        }

    } else {
        $cards[$arFields['NAME']] = $arFields;
    }



}

echo_cli("Найдено карт: ".count($cards));


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");