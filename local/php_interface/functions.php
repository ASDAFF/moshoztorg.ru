<?php
//define("MY_DEBUG_MODE",true);

if (isset($_GET['admitad_uid'])) {
	$days = 90;
	setcookie('_aid', $_GET['admitad_uid'], time() + 60 * 60 * 24 * $days, '/');
}

function get_admitad_uid() {
	if (!isset($_COOKIE['_aid'])) {
		return null;
	}
	return $_COOKIE['_aid'];
}


function getPropertyEnumValueById($propertyCode, $enumId, $iIblockId)
{
    CModule::IncludeModule("iblock");

	
    if (intval($enumId)>0 && strlen($propertyCode)>0) {
		$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"),
            Array("IBLOCK_ID"=>$iIblockId, "CODE"=>$propertyCode, 'ID'=>intval($enumId)));
        if($enum_fields = $property_enums->GetNext()){
            return $enum_fields["VALUE"];
        }
    }
    return "";

}

//получает айдишник значения в свойстве типа список
function getPropertyEnumIdByValue($propertyCode, $sValue,$iIblockId)
{
    CModule::IncludeModule("iblock");

    if (strlen($sValue)>0 && strlen($propertyCode)>0) {
        $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"),
            Array("IBLOCK_ID"=>$iIblockId, "CODE"=>$propertyCode, 'VALUE'=>$sValue));
        if($enum_fields = $property_enums->GetNext()){
            return $enum_fields["ID"];
        }
    }
    return false;

}

//получает айдишник значения в свойстве типа список
function getPropertyEnumIdByCode($propertyCode, $enumCode,$iIblockId)
{
    CModule::IncludeModule("iblock");

    if (strlen($enumCode)>0 && strlen($propertyCode)>0) {
        $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"),
            Array("IBLOCK_ID"=>$iIblockId, "CODE"=>$propertyCode, 'EXTERNAL_ID'=>$enumCode));
        if($enum_fields = $property_enums->GetNext()){
            return $enum_fields["ID"];
        }
    }
    return false;

}

//получает айдишник значения в свойстве типа список
function getPropertyEnumCodeById($propertyCode, $enumId,$iIblockId)
{
    CModule::IncludeModule("iblock");

    if (intval($enumId)>0 && strlen($propertyCode)>0) {
        $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"),
            Array("IBLOCK_ID"=>$iIblockId, "CODE"=>$propertyCode, 'ID'=>intval($enumId)));
        if($enum_fields = $property_enums->GetNext()){
            return $enum_fields["EXTERNAL_ID"];
        }
    }
    return "";

}

function myPrintR( $array , $file="", $line="",$title="" )
{
    if (MY_DEBUG_MODE===true){

        if (!empty($title))
            echo '<b>'.$title.'</b><br>';

        echo $file.' '.$line.'<pre>';
        print_r( $array );
        echo '</pre>';

    }
}

function dump( $array , $file="", $line="",$title="" )
{
    myPrintR( $array , $file, $line,$title );
}

function date_smart($date_input, $time=false) {
    $monthes = array(
        '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    );
    $date = strtotime($date_input);

    //Время
    if($time) $time = ' в G:i';
    else $time = '';

    //Сегодня, вчера, завтра
    if(date('Y') == date('Y',$date)) {
        if(date('z') == date('z', $date)) {
            $result_date = '<span class="redtext"><b>сегодня</b></span>'.date($time, $date);
        } elseif(date('z') == date('z',mktime(0,0,0,date('n',$date),date('j',$date)+1,date('Y',$date)))) {
            $result_date = '<span class="redtext" style="color:#138A18"><b>вчера</b></span>'.date($time, $date);
        } elseif(date('z') == date('z',mktime(0,0,0,date('n',$date),date('j',$date)-1,date('Y',$date)))) {
            $result_date = '<span class="redtext"><b>завтра</b></span>'.date($time, $date);
        }

        if(isset($result_date)) return $result_date;
    }

    //Месяца
    $month = $monthes[date('n',$date)];

    //Года
    if(date('Y') != date('Y', $date)) $year = 'Y г.';
    else $year = '';

    $result_date = date('j '.$month.' '.$year.$time, $date);
    return $result_date;
}


/**
 *
 * Возвращает ID инфоблока по символьному коду
 *
 * @param $sIBlockCode
 * @param bool $bRefreshCache
 * @return int
 */
function getIBlockIdByCode($sIBlockCode, $bRefreshCache = false)
{ 
    $obCache = new CPHPCache;
    $iReturnId = 0;
    $CACHE_ID = 'getIBlockIdByCode'.$sIBlockCode.'________';
    $iCacheTime = 10800; //3 часа
    if ( empty($sIBlockCode)) return false;

    if($obCache->StartDataCache($iCacheTime, $CACHE_ID)):

        if(CModule::IncludeModule('iblock')) {
            $arFilter = array(
                'CODE' => $sIBlockCode,
                'ACTIVE' => 'Y',
                'CHECK_PERMISSIONS' => 'N'
            );
            $dbItems = CIBlock::GetList(array('ID' => 'ASC'), $arFilter, false);
            if($arItem = $dbItems->Fetch()) {
                $iReturnId = intval($arItem['ID']);
            }
        }

        $obCache->EndDataCache($iReturnId);
    else:
        $iReturnId = $obCache->GetVars();
    endif;
    unset($obCache);
    return $iReturnId;
}




function getPropertyIdByCode ( $propertyCode,  $iIblockID = false, $bRefreshCache = false )
{
    if ( strlen($propertyCode)>0 ){

        $obCache = new CPHPCache;
        $iReturnId = 0;
        $CACHE_ID = 'getPropertyIdByCode'.$propertyCode.$iIblockID;
        $iCacheTime = 10800; //3 часа

        if($obCache->StartDataCache($iCacheTime, $CACHE_ID)):

            if(CModule::IncludeModule('iblock')) {
                $arFilter = Array("CODE"=>$propertyCode);
                if ($iIblockID){
                    $arFilter['IBLOCK_ID'] = $iIblockID;
                }

                $properties = CIBlockProperty::GetList(Array("id"=>"desc", "name"=>"asc"), $arFilter);
                if ($prop_fields = $properties->GetNext()){
                    $iReturnId = $prop_fields["ID"];
                }
            }

            $obCache->EndDataCache($iReturnId);
        else:
            $iReturnId = $obCache->GetVars();
        endif;
        unset($obCache);

        return $iReturnId;

    }
    return false;
}



function getCatalogTree($bIncludebSubsections=false)
{
    $obCache = new CPHPCache;
    $CACHE_ID = __METHOD__.'12';

    if($obCache->StartDataCache(10800, $CACHE_ID)): //3 часа

        $arIblocks = [];
        MHT::eachCatalogIBlock(function($iblock) use (&$arIblocks,$bIncludebSubsections){
            $arIblock = array(
                'id'   => $iblock['ID'],
                'name' => $iblock['NAME'],
                'link' => $iblock['LIST_PAGE_URL'],
                'image' => CFile::GetPath($iblock['PICTURE']),
                'image_id'=>$iblock['PICTURE'],
                'CODE'=>$iblock['CODE']
            );

            $arSections = [];
            //получаем список разделов
            WP::sections(array(
                'filter' => array(
                    'IBLOCK_ID' => $arIblock['id'],
                    'SECTION_ID' => 0,
                    'ACTIVE'    => "Y",
                ),
                'each' => function($f) use (&$arSections){
                    $arSections[] = array(
                        'name' => $f['NAME'],
                        'link' => $f['SECTION_PAGE_URL'],
                        'id'   => $f['ID'],
                    );
                },
                'sort' => array(
                    'SORT'=>'ASC',
                    'NAME' => 'ASC'
                )
            ));

            if ($bIncludebSubsections) {

                foreach ($arSections as &$arSection) {
                    $arSubSections = array();

                    WP::sections(array(
                        'filter' => array(
                            'IBLOCK_ID' => $arIblock['id'],
                            'SECTION_ID' => $arSection['id'],
                            'ACTIVE' => "Y",
                        ),
                        'each' => function ($f) use (&$arSubSections) {
                            $arSubSections[] = array(
                                'name' => $f['NAME'],
                                'link' => $f['SECTION_PAGE_URL'],
                            );
                        },
                        'sort' => array(
                            'SORT' => 'ASC',
                            'NAME' => 'ASC'
                        )
                    ));
                    $arSection['children'] = $arSubSections;
                }
            }

            $arIblock['sections'] = $arSections;
            $arIblocks[] = $arIblock;


        });

        $aMenuLinksExt = [];
        foreach($arIblocks as $arIblock) {
            $iDepthLevel = 1;
            $aMenuLinksExt[] = [
                $arIblock['name'],
                $arIblock['link'],
                $arIblock['image'],
                [
                    'FROM_IBLOCK' => 1,
                    'IS_PARENT'   => isset($arIblock['sections'][0]),
                    'DEPTH_LEVEL' => 1,
                    'IMAGE_ID'=>$arIblock['image_id'],
                    'CODE' => $arIblock['CODE']
                ]
            ];



            foreach($arIblock['sections'] as $arSection){
                $aMenuLinksExt[] = [
                    $arSection['name'],
                    $arSection['link'],
                    $arSection['link'],
                    [
                        'FROM_IBLOCK' => 1,
                        'IS_PARENT'   => isset($arSection['children']) && !empty($arSection['children']),
                        'DEPTH_LEVEL' => 2
                    ]
                ];

                if ( isset($arSection['children']) && !empty($arSection['children'])){
                    foreach($arSection['children'] as $arSubSection){

                        $aMenuLinksExt[] = [
                            $arSubSection['name'],
                            $arSubSection['link'],
                            $arSubSection['link'],
                            [
                                'FROM_IBLOCK' => 1,
                                'IS_PARENT'   => false,
                                'DEPTH_LEVEL' => 3
                            ]
                        ];

                    }

                }
            }

        }

        $obCache->EndDataCache($aMenuLinksExt);
    else:
        $aMenuLinksExt = $obCache->GetVars();
    endif;
    return $aMenuLinksExt;
}

function getCatalogTreeHierarchy()
{
     $obCache = new CPHPCache;
     $CACHE_ID = 'getCatalogTreeHierarchy123';

     if($obCache->StartDataCache(108000, $CACHE_ID)): //3 часа

         $arResult = getCatalogTree();

         //Делаем иерархию массива
         $arParentKeys = []; //массив родительских ключей по глубине
         $arMovedItems = [];
         foreach ($arResult as $key => $arItem) {


             $arLastItem = $arResult[$key - 1];
             if ($key > 0 && $arItem[3]['DEPTH_LEVEL'] > $arLastItem[3]['DEPTH_LEVEL']) {

                 $arParentKeys[$arItem[3]['DEPTH_LEVEL']] = $key - 1;
             }


             if (isset($arParentKeys[$arItem[3]['DEPTH_LEVEL']])) {
                 $iParentKey = $arParentKeys[$arItem[3]['DEPTH_LEVEL']];
                 $arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
                 $arMovedItems[] = $key;
             }

         }
         foreach ($arResult as $key => $arItem) {
             if (in_array($key, $arMovedItems)) {
                 unset($arResult[$key]);
             }
         }

         foreach ($arResult as $key => &$arItem) {

             if (isset($arItem[3]['IMAGE_ID']) && $arItem[3]['IMAGE_ID'] > 0) {

                 $file = CFile::ResizeImageGet(
                     $arItem[3]['IMAGE_ID'],
                     array('width' => 30, 'height' => 30),
                     BX_RESIZE_IMAGE_PROPORTIONAL,
                     true
                 );
                 $arItem[2] = $file['src'];
             }
         }



         $arResult = array_values($arResult);

         $obCache->EndDataCache($arResult);
     else:
         $arResult = $obCache->GetVars();
     endif;
    unset($obCache);
    return $arResult;

}

function getSubSections( $iIblockId, $iSectionId )
{
    $obCache = new CPHPCache;
    $CACHE_ID = 'getSubSections'.$iIblockId.$iSectionId;

    if($obCache->StartDataCache(1080000, $CACHE_ID)): //3 часа

        $arResult = [];
        //получаем список разделов
        WP::sections(array(
            'filter' => array(
                'IBLOCK_ID' => $iIblockId,
                'SECTION_ID' => $iSectionId,
                'ACTIVE'    => "Y"
            ),
            'each' => function($f) use (&$arResult){
                $arResult[] = array(
                    'name' => $f['NAME'],
                    'link' => $f['SECTION_PAGE_URL'],
                );
            },
            'sort' => array(
                'NAME' => 'ASC'
            )
        ));


        $obCache->EndDataCache($arResult);
    else:
        $arResult = $obCache->GetVars();
    endif;
    unset($obCache);
    return $arResult;

}


function true_wordform($num, $form_for_1, $form_for_2, $form_for_5){
    $num = abs($num) % 100; // берем число по модулю и сбрасываем сотни (делим на 100, а остаток присваиваем переменной $num)
    $num_x = $num % 10; // сбрасываем десятки и записываем в новую переменную
    if ($num > 10 && $num < 20) // если число принадлежит отрезку [11;19]
        return $form_for_5;
    if ($num_x > 1 && $num_x < 5) // иначе если число оканчивается на 2,3,4
        return $form_for_2;
    if ($num_x == 1) // иначе если оканчивается на 1
        return $form_for_1;
    return $form_for_5;
}


/**
 * Функция для удобного вывода отладочных данных в html
 *
 * Обычный вывод аргументов по порядку
 * dm(1, 2, 'test', array('one', 'two', 2));
 *
 * Если добавить перед аргументом строку с # вначале,
 * то она будет использоваться как заголовок следующего элемента
 * dm('#Первый', false, '#Второй', array('one', 'two', 2));
 */

function dm()
{
    //проверка на пользователя
  //  if (\CUser::GetID() == '37318') {

    $arInfo = array();
    $arResult = array();
    $arTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $sStyle = "<style>"
        .".debug-message { color: #A9B7C6; background: #232525; border: 1px solid #000; padding: 5px 10px; margin: 0 0 5px 0; }"
        .".debug-message > input { vertical-align: middle; cursor: pointer; margin: 0; font-family: monospace; }"
        .".debug-message > label { vertical-align: middle; cursor: pointer; padding: 0 0 0 10px; margin: 0; font-size: 13px; font-family: monospace; }"
        .".debug-message > input:hover ~ label, .debug-message > label:hover { text-decoration: underline; }"
        .".debug-message > input ~ pre { display: none; }"
        .".debug-message > input:checked ~ pre { display: block; }"
        .".debug-message > pre { margin: 5px 0; white-space: pre-wrap; padding: 0; background: transparent; border: none; color: #A9B7C6; font-size: 12px; font-family: monospace; }"
        .".debug-message > em { font-style: normal; padding: 0 0 0 10px; vertical-align: middle; font-size: 13px; font-family: monospace; }"
        .".debug-message > input:checked ~ em { display: none; }"
        .".debug-message > em > b, .debug-message > pre > b { color: #CC7832; font-weight: normal; font-size: 13px; font-family: monospace; }"
        .".debug-message > pre > i { color: #ea7676; font-style: none; font-size: 13px; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; }"
        ."</style>";

    @$sFile = str_replace(PROJECT_DIR, '', $arTrace[0]['file']);
    $sLine = "<b>{$sFile}</b>, <b>{$arTrace[0]['line']}</b>";

    $sClass = '';
    $arInfo[] = "<b># File:</b> {$sFile}, <b>line:</b> {$arTrace[0]['line']}";

    if (isset($arTrace[1]['class'])) {
        $sClass .= "<b># Method:</b> {$arTrace[1]['class']}";
        $sLine .= ", <b>{$arTrace[1]['class']}</b>";
    }

    if (isset($arTrace[1]['function'])) {
        $sLine .= $sClass ? "::<b>{$arTrace[1]['function']}()</b>" : ", <b>{$arTrace[1]['function']}()</b>";
        $sClass .= $sClass ? "::{$arTrace[1]['function']}()" : "<b># Function:</b> {$arTrace[1]['function']}()";
    }

    $arInfo[] = $sClass;

    $sKeys = '';
    $sNextKey = '';
    $arArguments = func_get_args();
    foreach ($arArguments as $Key => $xValue) {
        unset($arArguments[$Key]);

        $Key = $Key + 1;
        $sNextKey = !empty($sNextKey) ? $sNextKey : "Arg {$Key}";

        // Если строковое значение и начинается с решетки,
        // то принимаем его за заголовок следующей переменной.
        if (is_string($xValue) && strpos($xValue, '#') === 0) {
            $sNextKey = trim(substr($xValue, 1));
            $sKeys .= "#{$sNextKey}, ";
            continue;
        }

        try {
            $xValue = is_resource($xValue) ? print_r($xValue) : var_export($xValue, 1);
        } catch (\Exception $e) {
            $arResult[] = "<b># {$sNextKey}:</b> <i>Operation failed var_export</i>: ".$e->getMessage();
            $xValue = print_r($xValue, 1);
        }

        $arResult[] = "<b># {$sNextKey}:</b> {$xValue}";
        $sNextKey = '';
    }

    $sKeys = trim($sKeys, ', ');
    $sKeys = $sKeys ? "[{$sKeys}]" : "message";

    $sInput = 'dm-'.randString();
    $sResult = implode("\n", $arResult);
    $sChecked = strlen($sResult) > 500 ? '' : 'checked';
    unset($arResult);

    echo implode('', array(
        '<div class="debug-message">',
        $sStyle,
        "<input id=\"{$sInput}\" type=\"checkbox\" {$sChecked} />",
        "<label for=\"{$sInput}\">Show debug {$sKeys}</label>",
        "<em>{$sLine}</em>",
        '<pre>', implode("\n", $arInfo), '</pre>',
        '<pre>', $sResult, '</pre>',
        '</div>',
    ));

 // }
}