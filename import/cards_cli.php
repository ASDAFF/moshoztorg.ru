<?
//версия для запуска из консоли

function echo_cli ($message)
{
    ob_start();
    echo $message, "\n";
    ob_end_flush();
    while (@ob_end_flush()) {
        ;
    }
}

echo_cli('start');

$_SERVER["DOCUMENT_ROOT"] = str_replace('/import', '', dirname(__FILE__));
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
//require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");
//require_once($_SERVER["DOCUMENT_ROOT"].'/local/vendor/autoload.php');
GLOBAL $APPLICATION, $USER;
CModule::IncludeModule("iblock");

$IBLOCK_ID = getIBlockIdByCode("discount_cards");

$arSelect = Array("ID", "NAME", "XML_ID", "PROPERTY_PERCENT", "PROPERTY_TOTAL");
$arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y");
$res      = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields                 = $ob->GetFields();
    $cards[$arFields['NAME']] = $arFields;

}

echo_cli("Найдено карт: " . count($cards));

/**
 *  переписываем на работу с большими файлами
 * берем по несколько строк из файла за раз
 *
 * WARNING будет работать тольок с файлами вида
 * <?xml version="1.0" encoding="UTF-8"?>
 * <КартыЛояльности>
 * <FIX UID="aa652a8c-7f2a-11e7-80d7-005056011172" Name="29062" FIO="00000000-0000-0000-0000-000000000000" SummaSkidki="7"/>
 * <FIX UID="c6c5fb2e-7fff-11e7-80d7-005056011172" Name="29674" FIO="00000000-0000-0000-0000-000000000000" SummaSkidki="7"/>
 * <FIX UID="7101ba7c-8029-11e7-80d7-005056011172" Name="4819660614" FIO="00000000-0000-0000-0000-000000000000" SummaSkidki="7"/>
 * <FIX UID="5e344948-ac21-11e7-80db-005056011172" Name="29694" FIO="00000000-0000-0000-0000-000000000000" SummaSkidki="7"/>
 * </КартыЛояльности>
 *
 *
 */

//файл с данными
$file_name = $_SERVER["DOCUMENT_ROOT"] . '/discount_cards.xml';

echo $file_name;

if (file_exists($file_name)) {

    \CEventLog::Add(array(
        "SEVERITY"      => "ERROR",
        "AUDIT_TYPE_ID" => "DEBUG",
        "MODULE_ID"     => "discount.updater",
        "ITEM_ID"       => "UPDATE",
        "DESCRIPTION"   => "Обновление запущено.",
    ));


    echo_cli($file_name);

    $original_file_name = $file_name;
    $new_file_name      = $file_name . '.lock';

    if (rename($file_name, $new_file_name)) {
        $file_name = $new_file_name;
    }

    $first_line  = '<?xml version="1.0" encoding="UTF-8"?>';
    $second_line = '<КартыЛояльности>';
    $end_line    = '</КартыЛояльности>';

    $isFix   = 0;
    $isNakop = 0;

    $isUpdated = 0;
    $isCreated = 0;
    $done      = $errors = 0;


    /**
     * основной обработчик
     *
     * @param $action
     * @param bool $id
     * @param $IBLOCK_ID
     * @param $fields
     * @param $props
     */
    function processItem ($action, $id = false, $IBLOCK_ID, $fields, $props)
    {
        global $isUpdated, $isCreated, $done, $errors;

        switch ($action) {
            case 'update':
                // подготовленый апдейт, надо протестировать
                $el  = new CIBlockElement;
                $res = $el->Update($id, $fields);
                CIBlockElement::SetPropertyValuesEx($id, false, $props);

                $isUpdated++;

                break;
            case 'create':
                $el                        = new CIBlockElement;
                $fields['IBLOCK_ID']       = $IBLOCK_ID;
                $fields['PROPERTY_VALUES'] = $props;
                $PRODUCT_ID                = $el->Add($fields);

                $isCreated++;

                break;
        }

        echo_cli("processItemDone, action: " . $action . "; id:" . ($id ? $id : $PRODUCT_ID . "(new)") . "; XML_ID(UID):" . $fields['XML_ID']);

        $done++;

        if ($el->LAST_ERROR) {
            echo_cli("Error: " . $el->LAST_ERROR);
            $errors++;
        }
    }

    $line_count = 1;

    $handle = @fopen($file_name, "r");
    if ($handle) {
        while (($file_line = fgets($handle)) !== false) {

            if (strpos($file_line, 'UID=') !== false) {

                //тут обработка

                echo_cli('Cтрока ' . $line_count . ': ' . trim($file_line));

                $xmlstr = $first_line . $second_line . $file_line . $end_line;


                $xml = simplexml_load_string($xmlstr);

                $json  = json_encode($xml);
                $array = json_decode($json, true);

                $isFix   += count($array['FIX']);
                $isNakop += count($array['Nakoplenie']);

                foreach ($array['FIX'] as $fix) {

                    //костыль
                    $fix['@attributes'] = $fix;

                    $action            = '';
                    $fields            = Array(
                        "MODIFIED_BY"    => $USER->GetID(),
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $fix['@attributes']['Name'],
                        "ACTIVE"         => "Y",
                        "XML_ID"         => $fix['@attributes']['UID'],
                    );
                    $props             = array();
                    $props['CARDTYPE'] = 317085;
                    $props['PERCENT']  = $fix['@attributes']['SummaSkidki'];
                    $props['PHONE']    = $fix['@attributes']['Phone'];
                    $props['EMAIL']    = $fix['@attributes']['Mail'];
                    $props['UID']      = $fix['@attributes']['UID'];
                    $props['FIO']      = $fix['@attributes']['FIO'];
                    if ($cards[$fix['@attributes']['Name']]) {
                        $action = 'update';
                    } else {
                        $action = 'create';
                    }
                    if ($props['PERCENT'] == $cards[$fix['@attributes']['Name']]['PROPERTY_PERCENT_VALUE'] && $action == 'update') {
                        processItem($action, $cards[$fix['@attributes']['Name']]['ID'], $IBLOCK_ID, $fields, $props);
                    } else {
                        processItem($action, $cards[$fix['@attributes']['Name']]['ID'], $IBLOCK_ID, $fields, $props);
                    }

                    //if($i>=100) break;
                }
                //echo '<br>';
                foreach ($array['Nakoplenie'] as $i => $fix) {

                    //костыль
                    $fix['@attributes'] = $fix;

                    $action            = '';
                    $fields            = Array(
                        "MODIFIED_BY"    => $USER->GetID(),
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $fix['@attributes']['Name'],
                        "ACTIVE"         => "Y",
                        "XML_ID"         => $fix['@attributes']['UID'],
                    );
                    $props             = array();
                    $props['CARDTYPE'] = 317086;
                    $props['PHONE']    = $fix['@attributes']['Phone'];
                    $props['EMAIL']    = $fix['@attributes']['Mail'];
                    $props['UID']      = $fix['@attributes']['UID'];
                    $props['FIO']      = $fix['@attributes']['FIO'];
                    $props['TOTAL']    = floatval(str_replace(array(" ", ",", " "), array("", ".", ""),
                        trim($fix['@attributes']['NakoplenSumma'])));

                    if ($cards[$fix['@attributes']['Name']]) {
                        $action = 'update';
                    } else {
                        $action = 'create';
                    }
                    if ($props['TOTAL'] == $cards[$fix['@attributes']['Name']]['PROPERTY_TOTAL_VALUE'] && $action == 'update') {
                        processItem($action, $cards[$fix['@attributes']['Name']]['ID'], $IBLOCK_ID, $fields, $props);
                    } else {
                        processItem($action, $cards[$fix['@attributes']['Name']]['ID'], $IBLOCK_ID, $fields, $props);
                    }
                    //if($i>=100) break;
                }


            }

            $line_count++;
        }
    }


    echo_cli("");
    echo_cli("isUpdated: " . $isUpdated);
    echo_cli("isCreated: " . $isCreated);
    echo_cli("");
    echo_cli('Фиксированых:' . $isFix);
    echo_cli('Накопительных:' . $isNakop);
    echo_cli("");
    echo_cli("Обработано: " . $done);
    echo_cli("Ошибок: " . $errors);


    \CEventLog::Add(array(
        "SEVERITY"      => "INFO",
        "AUDIT_TYPE_ID" => "DEBUG",
        "MODULE_ID"     => "discount.updater",
        "ITEM_ID"       => "UPDATE",
        "DESCRIPTION"   => "Обновление завершено. " . " Обновлено: " . $isUpdated . ", cоздано: " . $isCreated . ". Всего обработанно записей: " . $done . " (Фиксированых: " . $isFix . "; Накопительных: " . $isNakop . "). Ошибок: " . $errors,
    ));

    @unlink($file_name);
    @unlink($original_file_name);

    if (file_exists($file_name) || file_exists($original_file_name)) {
        \CEventLog::Add(array(
            "SEVERITY"      => "ERROR",
            "AUDIT_TYPE_ID" => "DEBUG",
            "MODULE_ID"     => "discount.updater",
            "ITEM_ID"       => "UPDATE",
            "DESCRIPTION"   => "Ошибка удаления файлов данных.",
        ));
    }

}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");