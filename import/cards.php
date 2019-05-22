<?$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
//require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");
//require_once($_SERVER["DOCUMENT_ROOT"].'/local/vendor/autoload.php');
GLOBAL $APPLICATION, $USER;
CModule::IncludeModule("iblock");

$IBLOCK_ID = getIBlockIdByCode("discount_cards");

$arSelect = Array("ID", "NAME", "XML_ID", "PROPERTY_PERCENT", "PROPERTY_TOTAL");
$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $cards[$arFields['XML_ID']] = $arFields;
}
echo "<p>Найдено карт: ".count($cards)."</p>";


$xmlstr = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/1812_5.xml');
$xml = simplexml_load_string($xmlstr);
if($xml) {
    echo "loaded";
}else{
    echo "FAIL to load";
}

$json = json_encode($xml);
$array = json_decode($json,TRUE);
$done = $errors = 0;

echo'<pre>Фиксированых:';print_r(count($array['FIX']));echo"</pre>";
echo'<pre>Накопительных:';print_r(count($array['Nakoplenie']));echo"</pre>";

function processItem ($action, $id = false, $IBLOCK_ID, $fields, $props)
{

    switch ($action) {
        case 'update':
            // подготовленый апдейт, надо протестировать 
            $el = new CIBlockElement;
            $res = $el->Update($id, $fields);
            CIBlockElement::SetPropertyValuesEx($id, false, $props);
            break;
        case 'create':
            $el = new CIBlockElement;
            $fields['IBLOCK_ID'] = $IBLOCK_ID;
            $fields['PROPERTY_VALUES'] = $props;
            $PRODUCT_ID = $el->Add($fields);
            break;
    }

    echo "processItemDone, action: ".$action."; id:".($id?$id:$PRODUCT_ID."(new)")."; XML_ID(UID):".$fields['XML_ID']." <br>";
    //echo'<pre>fields:';print_r($fields);echo"</pre>";
    //echo'<pre>props:';print_r($props);echo"</pre>";
    $done++;
    if($el->LAST_ERROR) {
        echo "Error: " . $el->LAST_ERROR . '<br>';
        $errors++;
    }
}



foreach ($array['FIX'] as $fix){
    $action = '';
    $fields = Array(
        "MODIFIED_BY"    => $USER->GetID(),
        "IBLOCK_SECTION" => false,
        "NAME"           => $fix['@attributes']['Name'],
        "ACTIVE"         => "Y",
        "XML_ID"         => $fix['@attributes']['UID'],
    );
    $props = array();
    $props['CARDTYPE'] = 317085;
    $props['PERCENT'] = $fix['@attributes']['SummaSkidki'];
    $props['PHONE'] = $fix['@attributes']['Phone'];
    $props['EMAIL'] = $fix['@attributes']['Mail'];
    $props['UID'] = $fix['@attributes']['UID'];
    $props['FIO'] = $fix['@attributes']['FIO'];
    if($cards[$fix['@attributes']['UID']])
        $action = 'update';
    else
        $action = 'create';
    if($props['PERCENT'] == $cards[$fix['@attributes']['UID']]['PROPERTY_PERCENT_VALUE'] && $action == 'update'){
        processItem($action, $cards[$fix['@attributes']['UID']]['ID'], $IBLOCK_ID, $fields, $props);
    }else{
        processItem($action, $cards[$fix['@attributes']['UID']]['ID'], $IBLOCK_ID, $fields, $props);
    }

    //if($i>=100) break;
}
//echo '<br>';
foreach ($array['Nakoplenie'] as $i => $fix){
    $action = '';
    $fields = Array(
        "MODIFIED_BY"    => $USER->GetID(),
        "IBLOCK_SECTION" => false,
        "NAME"           => $fix['@attributes']['Name'],
        "ACTIVE"         => "Y",
        "XML_ID"         => $fix['@attributes']['UID'],
    );
    $props = array();
    $props['CARDTYPE'] = 317086;
    $props['PHONE'] = $fix['@attributes']['Phone'];
    $props['EMAIL'] = $fix['@attributes']['Mail'];
    $props['UID'] = $fix['@attributes']['UID'];
    $props['FIO'] = $fix['@attributes']['FIO'];
    $props['TOTAL'] = floatval(str_replace(array(" ",","," "),array("",".",""),trim($fix['@attributes']['NakoplenSumma'])));

    if($cards[$fix['@attributes']['UID']])
        $action = 'update';
    else
        $action = 'create';
    if($props['TOTAL'] == $cards[$fix['@attributes']['UID']]['PROPERTY_TOTAL_VALUE'] && $action == 'update'){
        processItem($action, $cards[$fix['@attributes']['UID']]['ID'], $IBLOCK_ID, $fields, $props);
    }else{
        processItem($action, $cards[$fix['@attributes']['UID']]['ID'], $IBLOCK_ID, $fields, $props);
    }
    //if($i>=100) break;
}

echo "<p>Обработано: ".$done."</p>";
echo "<p>Ошибок: ".$errors."</p>";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");