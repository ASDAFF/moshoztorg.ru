<?
//define("NO_KEEP_STATISTIC", true);
//define("NOT_CHECK_PERMISSIONS", true);
//require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/log/applic.log");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");
//require_once($_SERVER["DOCUMENT_ROOT"].'/local/vendor/autoload.php');
GLOBAL $APPLICATION, $USER;
$json = true;
$RESULT = array();
//$_SERVER['X-Api-Token'] = "b955cda66e75cb0ace5987a2e042fd50";

//http://moshoztorg.ru/applic.php?mode=objects&category_id=461&subcategory_id=19097

if (!class_exists('itsferaRest')) {
    class itsferaRest
    {
        function authByToken($token)
        {
            $result = false;
            if(strlen($token)>0) {
                $filter = Array("UF_TOKEN" => $token);
                $arSel = array("ID", "ACTIVE");
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
                if ($arUser = $rsUsers->GetNext()) {
                    if ($arUser['ACTIVE'] == "Y") {
                        $auth = new CUser;
                        if ($auth->Authorize($arUser['ID'])) {
                            $result = true;
                            GLOBAL $USER;
                            $userId = $USER->getid();
                        }
                    }
                }
            }else{
                $auth = new CUser;
                if($auth->Authorize(51505)) {
                    $result = true;
                    GLOBAL $USER;
                    $userId = $USER->getid();
                }
            }
            return $result;
        }
        function authBySoc($socId, $token)
        {
            $ALLOW_SOCSERV_AUTHORIZATION = (COption::GetOptionString("main", "allow_socserv_authorization", "Y") != "N" ? "Y" : "N");
            if(!$USER->IsAuthorized() && CModule::IncludeModule("socialservices") && ($ALLOW_SOCSERV_AUTHORIZATION == 'Y'))
            {
                $oAuthManager = new CSocServAuthManager();
                $arServices = $oAuthManager->GetActiveAuthServices(array(
                    'BACKURL' => $arResult['~BACKURL'],
                    'FOR_INTRANET' => $arResult['FOR_INTRANET'],
                ));
                $result = $oAuthManager->Authorize($socId);
                if(!empty($arServices))
                {
                    $arResult["AUTH_SERVICES"] = $arServices;
                    if(isset($socId) && $socId <> '' && isset($arResult["AUTH_SERVICES"][$socId]))
                    {
                        $arResult["CURRENT_SERVICE"] = $socId;
                        if(isset($_REQUEST["auth_service_error"]) && $_REQUEST["auth_service_error"] <> '')
                        {
                            $arResult['ERROR_MESSAGE'] = $oAuthManager->GetError($arResult["CURRENT_SERVICE"], $_REQUEST["auth_service_error"]);
                        }
                        elseif(!$oAuthManager->Authorize($socId))
                        {
                            $ex = $APPLICATION->GetException();
                            if ($ex)
                                $arResult['ERROR_MESSAGE'] = $ex->GetString();
                        }
                    }
                }
            }
            return $result;
        }
        function getUserData($string)
        {
            // поиск по логину
            $rsUser = CUser::GetByLogin($string);
            if (!$arUser = $rsUser->Fetch()) {

                //поиск по email
                $filter = Array("EMAIL" => $string);
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
                if (!$arUser = $rsUsers->GetNext()) {

                    //поиск по телефону
                    $filter = Array("PERSONAL_PHONE" => $string);
                    $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
                    $arUser = $rsUsers->GetNext();
                }
            }
            return $arUser;
        }
        public static function AddOrderProperty($id, $value, $order) {
            if (!strlen($id)) {
                return false;
            }
            if (CModule::IncludeModule('sale')) {
                if ($arProp = CSaleOrderProps::GetList(array(), array('ID' => $id))->Fetch()) {
                    return CSaleOrderPropsValue::Add(array(
                        'NAME' => $arProp['NAME'],
                        'CODE' => $arProp['CODE'],
                        'ORDER_PROPS_ID' => $arProp['ID'],
                        'ORDER_ID' => $order,
                        'VALUE' => $value,
                    ));
                }
            }
        }
    }
}

/*
 * если $_REQUEST['mode'] составной, разбиваем на части
 * пример из ТЗ mode=stocks/:stock_id:
 *
 * множественное раскодирование на случай перенаправления get запроса
 */
$arRequest = explode('/',rawurldecode(rawurldecode(rawurldecode(rawurldecode($_REQUEST['mode'])))));
// в request['mode'] оставляем первую часть + постфикс '/', остальное в params
if ( $arRequest[1] ) {
  $_REQUEST['params'] = $arRequest[1];
  $_REQUEST['mode'] = $arRequest[0].'/';
}



switch ($_REQUEST['mode']) {

    case 'register': // 1. /users
        $userNew = new CUser;
        $token = md5(time() + $_REQUEST['user']['login']);
        $arFields = Array(
            "NAME" => $_REQUEST['user']['first_name'],
            "LAST_NAME" => $_REQUEST['user']['second_name'],
            "EMAIL" => $_REQUEST['user']['email'],
            "LOGIN" => $_REQUEST['user']['login'],
            "LID" => "ru",
            "ACTIVE" => "Y",
            "GROUP_ID" => array(6),
            "PASSWORD" => $_REQUEST['user']['pass'],
            "CONFIRM_PASSWORD" => $_REQUEST['user']['confirm_pass'],
            "PERSONAL_PHONE" => $_REQUEST['user']['phone'],
            "UF_TOKEN" => $token
        );
        $ID = $userNew->Add($arFields);
        if (intval($ID) > 0) {
            $RESULT['error_code'] = 0;
            $RESULT['token'] = $token;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $userNew->LAST_ERROR;
        }
        break;


    case 'login': // 2. /users/login
        if ($_REQUEST['user']['login']) { //авторайз по логину\мылу\телефону
            $rsUser = CUser::GetByLogin($_REQUEST['user']['login']);
            if (!$arUser = $rsUser->Fetch()) {
                $filter = Array("EMAIL" => $_REQUEST['user']['login']);
                $arSel = array("LOGIN", "ACTIVE", "ID");
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
                if ($arUser = $rsUsers->GetNext()) {
                    if ($arUser['ACTIVE'] == "Y")
                        $_REQUEST['user']['login'] = $arUser['LOGIN'];
                } else {
                    $filter = Array("PERSONAL_PHONE" => $_REQUEST['user']['login']);
                    $arSel = array("LOGIN", "ACTIVE");
                    $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
                    if ($arUser = $rsUsers->GetNext()) {
                        $_REQUEST['user']['login'] = $arUser['LOGIN'];
                    }
                }
            } else {
                $_REQUEST['user']['login'] = $arUser['LOGIN'];
            }
            if ($arUser['ACTIVE'] == 'N') {
                $RESULT['error_code'] = 2;
                $RESULT['error_desc'] = "Пользователь деактивирован.";
            }
            $newuser = new CUSER;
            $arAuthResult = $newuser->Login($_REQUEST['user']['login'], $_REQUEST['user']['pass'], "Y");
            $APPLICATION->arAuthResult = $arAuthResult;
            if ($arAuthResult['TYPE'] == 'ERROR') {
                $RESULT['error_code'] = 1;
                $RESULT['error_desc'] = "Не удалось авторизовать. " . $arAuthResult['MESSAGE'];
            } else {
                $token = md5(time() + $_REQUEST['user']['login']);
                $userUpdate = new CUser;
                $userUpdate->Update($arUser['ID'], Array("UF_TOKEN" => $token));

                $RESULT['error_code'] = 0;
                $RESULT['token'] = $token;
                $RESULT['data'] = array(
                    'user[login]' => $arUser['LOGIN'],
                    'user[first_name]' => $arUser['NAME'],
                    'user[second_name]' => $arUser['LAST_NAME'],
                    'user[phone]' => $arUser['PERSONAL_PHONE'],
                    'user[email]' => $arUser['EMAIL'],
                );
            }
        } elseif ($_REQUEST['user']['social_type']) { // авторайз соцсети
            $arAuthResult = itsferaRest::authBySoc($_REQUEST['user']['social_type'], $_REQUEST['user']['social_token']);
            if ($arAuthResult) {
                $RESULT['error_code'] = 0;
                $RESULT['token'] = $token;
                $RESULT['data'] = array(
                    'user[login]' => $arUser['LOGIN'],
                    'user[first_name]' => $arUser['NAME'],
                    'user[second_name]' => $arUser['LAST_NAME'],
                    'user[phone]' => $arUser['PERSONAL_PHONE'],
                    'user[email]' => $arUser['EMAIL'],
                );
            } else {
                $RESULT['error_code'] = 1;
                $RESULT['error_desc'] = "Не удалось авторизовать.";
            }
        }
        break;


    case 'feedback': // 3. /feedback
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["EMAIL"] = $_REQUEST['user']['email'];
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("feedback"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $_REQUEST['user']['name'],
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $_REQUEST['message']['body'],
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['error_code'] = 0;
            $arEventFields = array(
                "ID" => $PRODUCT_ID,
                "USER" => $_REQUEST['user']['name'],
                "EMAIL" => $_REQUEST['user']['email'],
                "MESSAGE" => $_REQUEST['message']['body'],
            );
            CEvent::Send("ITSFERA_FEEDBACK", SITE_ID, $arEventFields);
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'reviews': // 4. /reviews
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        if ((int)$_REQUEST['object_id'] > 0) {
            $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DATE_CREATE_UNIX", "PROPERTY_USER_NAME", "PROPERTY_RATE");
            $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("product_comment"), "ACTIVE" => "Y", "PROPERTY_SKU" => $_REQUEST['object_id']);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $data[] = array(
                    "id" => $arFields['ID'],
                    "name" => $arFields['PROPERTY_USER_NAME_VALUE'],
                    "description" => strip_tags($arFields['~PREVIEW_TEXT']),
                    "rate" => $arFields['PROPERTY_RATE_VALUE'],
                    "date" => $arFields['DATE_CREATE_UNIX']
                );
            }
            $RESULT['error_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не указан id товара.";
        }
        break;


    case 'reviews_add': // 5. /reviews @TODO ошибка адреса?
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["USER_NAME"] = $_REQUEST['user']['name'];
        $PROP["RATE"] = $_REQUEST['review']['rate'];
        $PROP["SKU"] = $_REQUEST['store']['id'];
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("product_comment"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => "Отзыв от " . $_REQUEST['user']['name'],
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $_REQUEST['review']['body'],
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['error_code'] = 0;
            $arEventFields = array(
                "REVIEW_ID" => $PRODUCT_ID,
                "ITEM_ID" => $_REQUEST['store']['id'],
                "USER" => $_REQUEST['user']['name'],
                "RATE" => $_REQUEST['review']['rate'],
                "MESSAGE" => $_REQUEST['review']['body'],
            );
            CEvent::Send("ITSFERA_REVIEW", SITE_ID, $arEventFields);
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'stores': // 7. /stores
    case 'shops': // 6. /shops
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $iblockId = getIBlockIdByCode("shops");
        $arSelect = Array("ID", "NAME",
            "PROPERTY_TIME",
            "PROPERTY_COORDS",
            "PROPERTY_STREET",
            "PROPERTY_HOUSE",
            "PROPERTY_SUBWAY",
            "PROPERTY_SUBWAY_COLOR",
            "PROPERTY_IS_COMING_SOON"
        );
        $arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y", "PROPERTY_IS_COMING_SOON" => "N");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            $PHONES = array();
            $coords = explode(",", $arFields['PROPERTY_COORDS_VALUE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "PHONES"));
            while ($obProp = $resProp->GetNext())
                $PHONES[] = $obProp['VALUE'];
            $data[] = array(
                "id" => $arFields['ID'],
                "name" => $arFields['NAME'],
                "adress" => $arFields['PROPERTY_STREET_VALUE'] . ", " . $arFields['PROPERTY_HOUSE_VALUE'],
                "phone" => implode(", ", $PHONES),
                "time" => $arFields['PROPERTY_TIME_VALUE'],
                "latitude" => $coords[0],
                "longtitude" => $coords[1],
                "subway" => $arFields['PROPERTY_SUBWAY_VALUE'],
                "subway_color" => $arFields['PROPERTY_SUBWAY_COLOR_VALUE']
            );
        }
        if (intval($res->SelectedRowsCount()) > 0) {
            $RESULT['error_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось найти информацию о магазинах.";
        }
        break;

    /*
    case 'stores': // 7. /stores
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $iblockId = getIBlockIdByCode("ourstores");
        $arSelect = Array("ID", "NAME", "PROPERTY_TIME", "PROPERTY_MAP", "PROPERTY_ADDRESS");
        $arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $PHONES = array();
            $coords = explode(",", $arFields['PROPERTY_MAP_VALUE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "PHONE"));
            while ($obProp = $resProp->GetNext())
                $PHONES[] = $obProp['VALUE'];
            $data[] = array(
                "id" => $arFields['ID'],
                "name" => $arFields['NAME'],
                "adress" => $arFields['PROPERTY_ADDRESS_VALUE'],
                "phone" => implode(", ", $PHONES),
                "time" => $arFields['PROPERTY_TIME_VALUE'],
                "latitude" => $coords[0],
                "longtitude" => $coords[1],
            );
        }
        if (intval($res->SelectedRowsCount()) > 0) {
            $RESULT['error_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось найти информацию о пунктах выдачи.";
        }

        break;
        */

    case 'dictionaries': // 8. /dictionaries
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $db_vars = CSaleLocation::GetList(
            array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
            array("LID" => LANGUAGE_ID, "COUNTRY_NAME_ORIG" => "Russia"),
            false,
            false,
            array()
        );
        while ($vars = $db_vars->Fetch()) {
            if ($vars['CITY_ID']) {
                $id = $vars['CITY_ID'];
                $name = $vars['CITY_NAME_LANG'];
                $type = 'city';
            } elseif ($vars['REGION_ID']) {
                $id = $vars['REGION_ID'];
                $name = $vars['REGION_NAME'];
                $type = 'region';
            }
            $location[] = array("ID" => $id, "NAME" => $name, "TYPE" => $type);
        }
        $by = 'sort';
        $order = 'ASC';
        $res = CIBlock::GetList(
            Array($by => $order),
            Array(
                'TYPE' => 'mht_products',
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y',
            ), false
        );
        while ($ar_res = $res->Fetch()) {
            // берем не_торговые предложения
            if (!is_array(CCatalogSKU::GetInfoByOfferIBlock($ar_res['ID']))) {

                $cats_ID[$ar_res['ID']][] = $ar_res;
                $C_ID[] = $ar_res['ID'];

                $cats[0][$ar_res['ID']] = $categories[] = array(
                    "id" => $ar_res['ID'],
                    "picture" => "http://moshoztorg.ru" . CFile::GetPath($ar_res['PICTURE']),
                    "name" => $ar_res['NAME']
                );
                $arFilter = Array('IBLOCK_ID' => $ar_res['ID'], 'GLOBAL_ACTIVE' => 'Y');
                $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter, false);
                while ($ar_result = $db_list->GetNext()) {
                    $parent = ((int)$ar_result['IBLOCK_SECTION_ID'] > 0) ? $ar_result['IBLOCK_SECTION_ID'] : $ar_result['IBLOCK_ID'];
                    $cats_ID[$ar_result['ID']][] = $ar_result;

                    $cats[$parent][$ar_result['ID']] = $categories[] = array(
                        "id" => $ar_result['ID'],
                        "picture" => $ar_result['PICTURE'] ? "http://moshoztorg.ru" . CFile::GetPath($ar_result['PICTURE']) : "",
                        "name" => $ar_result['NAME'],
                        "parent" => $parent
                    );
                }
                $property_enums = CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array("IBLOCK_ID" => $ar_res['ID'], "CODE" => "CML2_MANUFACTURER"));
                while ($enum_fields = $property_enums->GetNext()) {
                    $MANUFACTURER[$enum_fields["ID"]] = $enum_fields["VALUE"];
                }
            }
        }

        function createTree(&$list, $parent)
        {
            $tree = array();
            foreach ($parent as $k => $l) {
                if (isset($list[$l['id']])) {
                    $l['children'] = createTree($list, $list[$l['id']]);
                }
                $tree[] = $l;
            }
            return $tree;
        }

        $tree = createTree($cats, $cats[0]);
/*
        $res = CIBlock::GetList(
            Array(),
            Array(
                'TYPE'=>'mht_products',
                'SITE_ID'=>SITE_ID,
                'ACTIVE'=>'Y',
            ), true
        );
        while($ar_res = $res->Fetch())
        {
            $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$ar_res['ID'], "CODE"=>"TSVET"));
            while($enum_fields = $property_enums->GetNext())
                $TSVET[$enum_fields["ID"]] = $enum_fields["VALUE"];
            $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$ar_res['ID'], "CODE"=>"MATERIALOBSHCHIY"));
            while($enum_fields = $property_enums->GetNext())
                $MATERIALOBSHCHIY[$enum_fields["ID"]] = $enum_fields["VALUE"];
        }
        */
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("banners"), "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arFields['PROPERTIES'] = $ob->GetProperties();

            $pic = $arFields['PROPERTIES']['IMAGE_BG']['VALUE'] ? "http://moshoztorg.ru" . CFile::GetPath($arFields['PROPERTIES']['IMAGE_BG']['VALUE']) : "";
            $banners[] = array(
                'banner_id' => $arFields['ID'],
                'picture_url' => $pic,
                'subcategory_id' => $arFields['PROPERTIES']['SECT_ID']['VALUE'],
                'link' => $arFields['PROPERTIES']['LINK']['VALUE']
            );
        }

        $RESULT['data']['sort_type'] = array(
            'priceup' => 'по цене, возрастание',
            'pricedown' => 'по цене, убывание',
            'popular' => 'популярность',
            'name' => 'по названию'
        );
        $RESULT['error_code'] = 0;
        $RESULT['data']['filter']['color'] = $TSVET;
        $RESULT['data']['filter']['material'] = $MATERIALOBSHCHIY;
        $RESULT['data']['locations'] = $location;
        $RESULT['data']['categories'] = $tree; //$categories;
        //$RESULT['data']['subcategories'] = $subcategories;
        $RESULT['data']['manufacturers'] = $MANUFACTURER;
		$RESULT['data']['banners'] = $banners;
        break;


    case 'objects': // 9. /objects
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating");
        $arFilter = Array("ACTIVE" => "Y", 'IBLOCK_TYPE' => 'mht_products');
        if($_REQUEST['sales'])
            $arFilter['PROPERTY_SAYT_AKTSIONNYY_TOVAR_VALUE'] = 'Да';
        if($_REQUEST['category_id'])
            $arFilter['IBLOCK_ID'] = $iblockId = $_REQUEST['category_id'];
        if($_REQUEST['subcategory_id'])
            $arFilter['SECTION_ID'] = $_REQUEST['subcategory_id'];
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => $_REQUEST['pagesize'] ? $_REQUEST['pagesize'] : 50, "iNumPage" => $_REQUEST['page'] ? $_REQUEST['page'] : 1), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $PHOTOS = array();
            if($arFields['DETAIL_PICTURE'])
                $PHOTOS[] = "http://moshoztorg.ru".cfile::getpath($arFields['DETAIL_PICTURE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
            while ($obProp = $resProp->GetNext())
                if($obProp['VALUE'])
                    $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "CML2_TRAITS"));
            while ($obProp = $resProp->GetNext())
                if ($obProp['DESCRIPTION'] == 'Код')
                    $code = $obProp['VALUE'];

            $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");

            $stores = $store = array();
            $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' =>$arFields['ID']), false, false);
            while ($arStore = $rsStore->getNext()){
                $quantity += $arStore['AMOUNT'];
                /*foreach ($arStore as $k=>$v) {
                    if(strpos($k,"~") === false)
                        $store[$k] = $v;
                }
                $stores[] = $store;*/
            }
            $data[$arFields['ID']] = array(
                'object_id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'desc' => $arFields['PREVIEW_TEXT'],
                'price' => $arPrice['DISCOUNT_PRICE'],
                'quantity' => ($quantity>0)?$quantity:0,
                'article' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                'code' => $code,
                'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'],
                'picture' => $PHOTOS,
                'stores' => $_REQUEST['sales']?"":$stores,
                'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                'rate_average' => $arFields['PROPERTY_RATING_VALUE']
            );
        }
        //echo'<pre>';print_r($data);echo"</pre>";
        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;

    case 'objects_search': // 9.2 /objects_search
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        CModule::IncludeModule("search");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $res = CIBlock::GetList(
            Array(),
            Array(
                'TYPE' => 'mht_products',
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y',
            ), false
        );
        while ($ar_res = $res->Fetch()) {
            if (!is_array(CCatalogSKU::GetInfoByOfferIBlock($ar_res['ID']))) { // берем не торговые предложения
                $iblock_ids[] = $ar_res['ID'];
            }
        }

        $_REQUEST['object_search_pattern'] = rawurldecode(rawurldecode(rawurldecode(rawurldecode($_REQUEST['object_search_pattern']))));

        $i=0;
        $obSearch = new CSearch;
        $obSearch->Search(array(
            "QUERY" => $_REQUEST['object_search_pattern'],
            "SITE_ID" => LANG,
            "!ITEM_ID" => "S%",
            "MODULE_ID" => "iblock",
            "=PARAM2" => $iblock_ids,
        ), array(
            //"TITLE_RANK" => "ASC"
        ));
        while($arResult = $obSearch->GetNext()) {
            $item_ids[] = $arResult['ITEM_ID'];
            $itemSortOrder[$i] = $arResult['ITEM_ID'];
            $i++;
        }
        // поиск заменен на фильтр по названию 24.07.17
        // "По запросу Чашка в мобильное приложение отдает почему-то первым делом прихватки... можете посмотреть/исправить?"
        if(is_array($item_ids)) {
            $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating");
            $arFilter = Array("ACTIVE" => "Y", 'IBLOCK_TYPE' => 'mht_products', "%NAME"=>$_REQUEST['object_search_pattern']); // , 'ID' => $item_ids
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => $_REQUEST['pagesize'] ? $_REQUEST['pagesize'] : 50, "iNumPage" => $_REQUEST['page'] ? $_REQUEST['page'] : 1), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $PHOTOS = array();
                if ($arFields['DETAIL_PICTURE'])
                    $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($arFields['DETAIL_PICTURE']);
                $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
                while ($obProp = $resProp->GetNext())
                    if ($obProp['VALUE'])
                        $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
                $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "CML2_TRAITS"));
                while ($obProp = $resProp->GetNext())
                    if ($obProp['DESCRIPTION'] == 'Код')
                        $code = $obProp['VALUE'];
                $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");

                $stores = $store = array();
                $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arFields['ID']), false, false);
                while ($arStore = $rsStore->getNext()) {
                    $quantity += $arStore['AMOUNT'];
                    /*foreach ($arStore as $k => $v) {
                        if (strpos($k, "~") === false)
                            $store[$k] = $v;
                    }
                    $stores[] = $store;*/
                }
                $data[$arFields['ID']] = array(
                    'object_id' => $arFields['ID'],
                    'name' => $arFields['NAME'],
                    'desc' => $arFields['PREVIEW_TEXT'],
                    'price' => $arPrice['DISCOUNT_PRICE'],
                    'quantity' => ($quantity>0)?$quantity:0,
                    'article' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                    'code' => $code,
                    'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'],
                    'picture' => $PHOTOS,
                    'stores' => $stores,
                    'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                    'rate_average' => $arFields['PROPERTY_RATING_VALUE']
                );
            }
        }else{
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось найти товары";
        }

        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;



    case 'stocks': // 10. /stocks
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "ACTIVE_FROM", "ACTIVE_TO");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("actions"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $objects_id = array();
            $resProp = CIBlockElement::GetProperty(getIBlockIdByCode("actions"), $arFields['ID'], "sort", "asc", array("CODE" => "ELEMENT_ID"));
            while ($obProp = $resProp->GetNext())
                $objects_id[] = $obProp['VALUE'];

            $data = array(
                'id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'picture' => "http://moshoztorg.ru" . cfile::getpath($arFields['PREVIEW_PICTURE']),
                'time' => $arFields['ACTIVE_FROM'] . " - " . $arFields['ACTIVE_TO']
            );
            if($objects_id) {
                $objects = array();
                $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating", "PROPERTY_CML2_MANUFACTURER");
                $arFilter = Array("ID" => $objects_id, "ACTIVE" => "Y");
                $resObj = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while ($obObj = $resObj->GetNextElement()) {
                    $arFields = $obObj->GetFields();
                    $PHOTOS = array();
                    if ($arFields['DETAIL_PICTURE'])
                        $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($arFields['DETAIL_PICTURE']);
                    $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
                    while ($obProp = $resProp->GetNext())
                        if($obProp['VALUE'])
                            $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
                    $product = CCatalogProduct::GetByID($arFields['ID']);
                    $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");
                    $objects[$arFields['ID']] = array(
                        'object_id' => $arFields['ID'],
                        'name' => $arFields['NAME'],
                        'desc' => $arFields['PREVIEW_TEXT'],
                        'quantity' => $product['QUANTITY']-$product['QUANTITY_RESERVED'],
                        'price' => $arPrice['DISCOUNT_PRICE'],
                        'code' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                        'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'], // @TODO коды откуда?
                        'picture' => $PHOTOS,
                        'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                        'rate_average' => $arFields['PROPERTY_RATING_VALUE']
                    );
                }
            }
            $data['objects'] = $objects;
            $RESULT['data'][] = $data;
        }
        $RESULT['error_code'] = 0;
        break;


    /**
     *  Список объектов, которые относятся к конкретной акции.
     *  GET http://moshoztorg.ru/applic.php?mode=stocks/:stock_id:
     */
    case 'stocks/':
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $id = $_REQUEST['params'];

        $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "ACTIVE_FROM", "ACTIVE_TO");
        $arFilter = Array("ID" => $id,"IBLOCK_ID" => getIBlockIdByCode("actions"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $objects_id = array();
            $resProp = CIBlockElement::GetProperty(getIBlockIdByCode("actions"), $arFields['ID'], "sort", "asc", array("CODE" => "ELEMENT_ID"));
            while ($obProp = $resProp->GetNext())
                $objects_id[] = $obProp['VALUE'];

            $data = array(
                'stoke_id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'picture' => "https://moshoztorg.ru" . cfile::getpath($arFields['PREVIEW_PICTURE']),
                'time' => $arFields['ACTIVE_FROM'] . " - " . $arFields['ACTIVE_TO']
            );
            if($objects_id) {
                $objects = array();
                $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating", "PROPERTY_CML2_MANUFACTURER");
                $arFilter = Array("ID" => $objects_id, "ACTIVE" => "Y");
                $resObj = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while ($obObj = $resObj->GetNextElement()) {
                    $arFields = $obObj->GetFields();
                    $PHOTOS = array();
                    if ($arFields['DETAIL_PICTURE'])
                        $PHOTOS[] = "http://moshoztorg.ru" . cfile::getpath($arFields['DETAIL_PICTURE']);
                    $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
                    while ($obProp = $resProp->GetNext())
                        if($obProp['VALUE'])
                            $PHOTOS[] = "https://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
                    $product = CCatalogProduct::GetByID($arFields['ID']);
                    $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");
                    $objects[$arFields['ID']] = array(
                        'name' => $arFields['NAME'],
                        'desc' => $arFields['PREVIEW_TEXT'],
                        'price' => $arPrice['DISCOUNT_PRICE'],
                        'code' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                        'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'], // @TODO коды откуда?
                        'picture' => $PHOTOS,
                        'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                        'rate_average' => $arFields['PROPERTY_RATING_VALUE']
                    );
                }
            }
            $data['objects'] = $objects;
            $RESULT['data'][] = $data;
        }
        $RESULT['error_code'] = 0;
        break;


    case 'passwords': // 11. /passwords
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        if ($arUser = itsferaRest::getUserData($_REQUEST['user']['auth'])) {
            $arResult = $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL']);
            if ($arResult["TYPE"] == "OK")
                $RESULT['error_code'] = 0;
            else {
                $RESULT['error_code'] = 2;
                $RESULT['error_desc'] = "Пользователь с введенным логином/email/телефоном не найден.";
            }
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось авторизоваться";
        }
        break;


    case 'questions': // 12. /questions
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["NAME"] = $_REQUEST['user']['name'];
        $PROP["EMAIL"] = $_REQUEST['user']['email'];
        $PROP["QUESTION"][0] = Array("VALUE" => Array("TEXT" => $_REQUEST['message']['body'], "TYPE" => "text"));
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("faq"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => "Вопрос от " . $_REQUEST['user']['name'],
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $_REQUEST['message']['body'],
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['error_code'] = 0;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'faq': // 13. /faq
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $arSelect = Array("ID", "NAME", "PROPERTY_QUESTION", "PROPERTY_ANSWER", "PROPERTY_NAME", "PROPERTY_EMAIL");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("faq"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $data[] = array(
                "question" => strip_tags($arFields['~PROPERTY_QUESTION_VALUE']['TEXT']),
                "answer" => strip_tags($arFields['~PROPERTY_ANSWER_VALUE']['TEXT']),
                "name" => $arFields['PROPERTY_NAME_VALUE'],
                "email" => $arFields['PROPERTY_EMAIL_VALUE'],
            );
        }
        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'orders': // 14. /orders
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $arFilter = Array("USER_ID" => CUser::GetID());
        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
        while ($arOrder = $db_sales->Fetch()) {
            $db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
            while ($arProps = $db_props->Fetch())
                if ($arProps['CODE'] == 'ADDRESS')
                    $deliveryAddress = $arProps['VALUE'];
            $items = array();
            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "ORDER_ID" => $arOrder['ID']
                ),
                false,
                false,
                array("NAME", "QUANTITY", "PRODUCT_ID")
            );
            while ($arItems = $dbBasketItems->Fetch()) {
                $res = CIBlockElement::GetByID($arItems["PRODUCT_ID"]);
                $product = $res->GetNext();
                $items[] = array(
                    "name" => $arItems['NAME'],
                    "desc" => $product['PREVIEW_TEXT'],
                    "count" => $arItems["QUANTITY"],
                );
            }
            $data[] = array(
                "id" => $arOrder["ID"],
                "number" => $arOrder["ID"],
                "date" => MakeTimeStamp($arOrder["DATE_INSERT"], "DD.MM.YYYY HH:MI:SS"),
                "price" => $arOrder["PRICE"],
                "store" => $deliveryAddress,
                "items" => $items,
            );
        }
        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'loyality': // 15. /loyality
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        // TODO нет готового функционала
        break;


    case 'buy_with_call': // 16. /buy_with_call
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["PRODUCT"] = $_REQUEST['object_id'];
        $PROP["PHONE"] = $_REQUEST['user']['phone'];
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("one-click"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $_REQUEST['user']['name'],
            "ACTIVE" => "N",
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['error_code'] = 0;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'user_edit': // 17. /users/edit
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $arUser = itsferaRest::getUserData($_REQUEST['user']['login']);

        $userEdit = new CUser;
        $fields = Array(
            "LOGIN" => $_REQUEST['user']['login'],
            "NAME" => $_REQUEST['user']['first_name'],
            "LAST_NAME" => $_REQUEST['user']['second_name'],
            "EMAIL" => $_REQUEST['user']['email'],
            "PERSONAL_PHONE" => $_REQUEST['user']['phone'],
            "PASSWORD" => $_REQUEST['user']['pass'],
            "CONFIRM_PASSWORD" => $_REQUEST['user']['confirm_pass'],
        );
        if ($arUser['ID']) {
            if ($userEdit->Update($arUser['ID'], $fields)) {
                $RESULT['error_code'] = 0;
                $RESULT['api-token'] = $arUser['UF_TOKEN'];
            } else {
                $RESULT['error_code'] = 1;
                $RESULT['error_desc'] = $userEdit->LAST_ERROR;
            }
        } else {
            $RESULT['error_code'] = 2;
            $RESULT['error_desc'] = "Пользователь с логином \"" . $_REQUEST['user']['login'] . "\" не найден";
        }
        break;


    case 'order_reject': // 18. /orders/reject
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");

        if(CSaleOrder::CancelOrder($_REQUEST['order']['id'], "Y", "Отмена заказа из приложения")) {
            $RESULT['error_code'] = 0;
        }else{
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Ошибка отмены заказа";
        }

        break;


    case 'subscribe': // 19. /subscribe
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("subscribe");

        $arFields = Array(
            "USER_ID" => CUSER::GetID(),
            "FORMAT" => "html",
            "EMAIL" => $_REQUEST['user']['email'],
            "ACTIVE" => "Y",
            "RUB_ID" => array(1)
        );
        $subscr = new CSubscription;
        $ID = $subscr->Add($arFields);
        if ($ID > 0) {
            CSubscription::Authorize($ID);
            CSubscription::ConfirmEvent($ID);
            $RESULT['error_code'] = 0;
        } else {
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = $subscr->LAST_ERROR;
        }
        break;


    case 'order_add': // 20. /orders
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");



        if($_REQUEST['test']) {
            $_SERVER['X-Api-Token'] = "b3cb4ce5d89358492bfc99c843bf50f4";
            // данные для теста
            $_REQUEST['objects'] = '660134:1';
            // $_REQUEST['user']['country'] //TODO нет такого свойства
            // $_REQUEST['user']['region'] //TODO нет такого свойства
            // $_REQUEST['loyality']['number'] //TODO нет такого свойства
            $_REQUEST['user']['city'] = "москва";
            $_REQUEST['user']['phone'] = "111 11 11";
            $_REQUEST['user']['email'] = "lolbotan1@yandex.ru";
            $_REQUEST['user']['index'] = "141200";
            $_REQUEST['user']['address'] = "пушкино";
            $_REQUEST['message']['body'] = "Тестовый заказ для разработки приложения, обрабатывать не нужно";
            $_REQUEST['location_id'] = 3;
        }


        if(itsferaRest::authByToken($_SERVER['X-Api-Token']))
        {
            GLOBAL $USER;
            $userId = $USER->getid();
            $fUserID = CSaleBasket::GetBasketUserID();
            // чистим корзину
            CSaleBasket::DeleteAll($fUserID);
        }


        //формируем корзину
        $items = explode(";",$_REQUEST['objects']);
        foreach ($items as $item)
        {
            $param = explode(":",$item);
            if($param[0] > 0 && $param[1] > 0)
                Add2BasketByProductID( $param[0], $param[1]);
        }

        // физ лицо?
        if(!empty($_REQUEST['user']))
            $PERSON_TYPE_ID = 1;
        else
            $PERSON_TYPE_ID = 2;
        // плат. система
        if($_REQUEST['price_type'] === 0)
            $PAY_SYSTEM_ID = 1; //наличными
        else
            $PAY_SYSTEM_ID = 20; //Картой курьеру
        //доставка
        if($_REQUEST['order_type'] === 0)
            $DELIVERY_ID = 1; //Доставка курьером
        else
            $DELIVERY_ID = 2; //Самовывоз

        $arBasketItems = array();
        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY",
                "CAN_BUY", "PRICE", "WEIGHT")
        );
        while ($arItems = $dbBasketItems->Fetch())
        {
            if (strlen($arItems["CALLBACK_FUNC"]) > 0)
            {
                CSaleBasket::UpdatePrice($arItems["ID"],
                    $arItems["CALLBACK_FUNC"],
                    $arItems["MODULE"],
                    $arItems["PRODUCT_ID"],
                    $arItems["QUANTITY"]);
                $arItems = CSaleBasket::GetByID($arItems["ID"]);
            }
            $totalPrice += $arItems['PRICE']*$arItems["QUANTITY"];
        }
        $arFields = array(
            "LID" => SITE_ID,
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID,
            "PAYED" => "N",
            "CANCELED" => "N",
            "STATUS_ID" => "N",
            "PRICE" => $totalPrice,
            "CURRENCY" => "RUB",
            "USER_ID" => $userId?$userId:51505,
            "PAY_SYSTEM_ID" => $PAY_SYSTEM_ID,
            "PRICE_DELIVERY" => 0,
            "DELIVERY_ID" => $DELIVERY_ID,
            "DISCOUNT_VALUE" => 0,
            "TAX_VALUE" => 0.0,
            "USER_DESCRIPTION" => $_REQUEST['message']['body']
        );
        if (CModule::IncludeModule("statistic"))
            $arFields["STAT_GID"] = CStatistic::GetEventParam();

        $ORDER_ID = CSaleOrder::Add($arFields);
        $ORDER_ID = IntVal($ORDER_ID);
        if($ORDER_ID>0){
            CSaleBasket::OrderBasket($ORDER_ID, $fUserID, SITE_ID);
            if($PERSON_TYPE_ID == 1) {
                // itsferaRest::AddOrderProperty($code, $_REQUEST['user']['country'], $ORDER_ID); // нет такого свойства
                // itsferaRest::AddOrderProperty($code, $_REQUEST['user']['region'], $ORDER_ID); // нет такого свойства
                itsferaRest::AddOrderProperty(6, $_REQUEST['location_id'], $ORDER_ID);
                itsferaRest::AddOrderProperty(5, $_REQUEST['user']['city'], $ORDER_ID);
                itsferaRest::AddOrderProperty(3, $_REQUEST['user']['phone'], $ORDER_ID);
                itsferaRest::AddOrderProperty(2, $_REQUEST['user']['email'], $ORDER_ID);
                itsferaRest::AddOrderProperty(4, $_REQUEST['user']['index'], $ORDER_ID);
                itsferaRest::AddOrderProperty(7, $_REQUEST['user']['address'], $ORDER_ID);
                // itsferaRest::AddOrderProperty($code, $_REQUEST['loyality']['number'], $ORDER_ID); // нет такого свойства
            }else{
                itsferaRest::AddOrderProperty(18, $_REQUEST['location_id'], $ORDER_ID);
                itsferaRest::AddOrderProperty(8, $_REQUEST['company']['name'], $ORDER_ID);
                itsferaRest::AddOrderProperty(12, $_REQUEST['company']['user'], $ORDER_ID);
                itsferaRest::AddOrderProperty(10, $_REQUEST['company']['inn'], $ORDER_ID);
                itsferaRest::AddOrderProperty(11, $_REQUEST['company']['kpp'], $ORDER_ID);
                itsferaRest::AddOrderProperty(14, $_REQUEST['company']['phone'], $ORDER_ID);
                itsferaRest::AddOrderProperty(13, $_REQUEST['company']['email'], $ORDER_ID);
                itsferaRest::AddOrderProperty(9, $_REQUEST['company']['address'], $ORDER_ID);
                itsferaRest::AddOrderProperty(15, $_REQUEST['company']['fax'], $ORDER_ID);
                itsferaRest::AddOrderProperty(20, $_REQUEST['company']['checking_account'], $ORDER_ID);
                itsferaRest::AddOrderProperty(21, $_REQUEST['company']['correspondent_account'], $ORDER_ID);
                itsferaRest::AddOrderProperty(22, $_REQUEST['company']['bik'], $ORDER_ID);
                // itsferaRest::AddOrderProperty($code, $_REQUEST['loyality']['number'], $ORDER_ID); //TODO нет функционала
                itsferaRest::AddOrderProperty(23, $_REQUEST['bank']['name'], $ORDER_ID);
                itsferaRest::AddOrderProperty(17, $_REQUEST['bank']['city'], $ORDER_ID);
                itsferaRest::AddOrderProperty(19, $_REQUEST['address'], $ORDER_ID);
            }
            $RESULT['error_code'] = 0;
        }else{
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Ошибка добавления заказа";
        }
        if($_REQUEST['test']) {
            echo'<pre>';print_r($_REQUEST);echo"</pre>";
            echo'<pre>';print_r($arFields);echo"</pre>";
            echo'<pre>';print_r($ORDER_ID);echo"</pre>";
        }
        break;


    case 'videos': // 21. /videos
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME", "PROPERTY_SHORT_URL");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("videos"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $data[] = array(
                "name" => $arFields['NAME'],
                "video_url" => "https://www.youtube.com/watch?v=" . $arFields['PROPERTY_SHORT_URL_VALUE'],
            );
        }
        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;

        break;


    case 'recall': // 22. /recall
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $_REQUEST['user']['phone'];
        //TODO чо делаем то?
        $RESULT['error_code'] = 0;

        break;


    case 'articles': // 23. /articles
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "DATE_CREATE_UNIX", "ACTIVE_FROM");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("statii"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            $data[] = array(
                "article_name" => $arFields['NAME'],
                "article_date" => $arFields['ACTIVE_FROM'] ? MakeTimeStamp($arFields['ACTIVE_FROM'], "DD.MM.YYYY HH:MI:SS") : $arFields['DATE_CREATE_UNIX'],
                "article_body" => $arFields['PREVIEW_TEXT'],
                "article_body_full" => $arFields['DETAIL_TEXT'],
            );
        }
        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'objects_filter': // 24. /objects
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("price");

        if($_REQUEST['filter']['sort_type'] == 'priceup'){
            $sort = "CATALOG_PRICE_SCALE_0";
            $order = "ASC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'pricedown'){
            $sort = "CATALOG_PRICE_SCALE_0";
            $order = "DESC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'popular'){
            $sort = "show_counter";
            $order = "DESC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'name'){
            $sort = "NAME";
            $order = "ASC";
        }
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating", "PROPERTY_CML2_MANUFACTURER");
        $arFilter = Array("IBLOCK_TYPE" => "mht_products", "ACTIVE"=>"Y");
        if($_REQUEST['filter']['color_id'])
            $arFilter["PROPERTY_TSVET"] = $_REQUEST['filter']['color_id'];
        if($_REQUEST['filter']['manufacturer_id'])
            $arFilter["PROPERTY_CML2_MANUFACTURER"] = $_REQUEST['filter']['manufacturer_id'];
        if($_REQUEST['filter']['material_id'])
            $arFilter[] = array(
                "LOGIC" => "OR",
                array("PROPERTY_MATERIALOBSHCHIY" => $_REQUEST['filter']['material_id']),
                array("PROPERTY_MATERIAL" => $_REQUEST['filter']['material_id']),
            );
        if($_REQUEST['filter']['barcode'])
            $arFilter["PROPERTY_CML2_BAR_CODE"] = $_REQUEST['filter']['barcode'];
        $res = CIBlockElement::GetList(Array($sort,$order), $arFilter, false, Array("nPageSize"=>50, "iNumPage"=>$_REQUEST['page']?$_REQUEST['page']:1), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $PHOTOS = array();
            if($arFields['DETAIL_PICTURE'])
                $PHOTOS[] = "http://moshoztorg.ru".cfile::getpath($arFields['DETAIL_PICTURE']);
            $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
            while ($obProp = $resProp->GetNext())
                if($obProp['VALUE'])
                    $PHOTOS[] = "http://moshoztorg.ru".cfile::getpath($obProp['VALUE']);
            $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "CML2_TRAITS"));
            while ($obProp = $resProp->GetNext())
                if ($obProp['DESCRIPTION'] == 'Код')
                    $code = $obProp['VALUE'];
            $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, CUSER::GetUserGroupArray(), "N");

            $stores = $store = array();
            $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' =>$arFields['ID']), false, false);
            while ($arStore = $rsStore->getNext()){
                $quantity += $arStore['AMOUNT'];
                /*foreach ($arStore as $k=>$v) {
                    if(strpos($k,"~") === false)
                        $store[$k] = $v;
                }
                $stores[] = $store;
                */
            }
            $data[] = array(
                'object_id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'desc' => $arFields['PREVIEW_TEXT'],
                'price' => $arPrice['DISCOUNT_PRICE'],
                'quantity' => ($quantity>0)?$quantity:0,
                'article' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                'code' => $code,
                'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'],
                'picture' => $PHOTOS,
                'stores' => $stores,
                'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                'rate_average' => $arFields['PROPERTY_RATING_VALUE']
            );
        }
        if (intval($res->SelectedRowsCount()) > 0){
            $RESULT['error_code'] = 0;
            $RESULT['data'] = $data;
        }else{
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось найти товаров.";
        }
        break;

    case 'idbybarcode': // 25. /idbybarcode
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");

        $arSelect = array("ID","NAME");
        $arFilter = array("PROPERTY_CML2_BAR_CODE" => $_REQUEST['barcode']);
        $res = CIBlockElement::GetList(Array($sort,$order), $arFilter, false, false, $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $items[] = $arFields['ID'];
        }
        if (count($items) > 0){
            $RESULT['error_code'] = 0;
            $RESULT['data'] = $items;
        }else{
            $RESULT['error_code'] = 1;
            $RESULT['error_desc'] = "Не удалось найти товаров с указаным шрихкодом.";
        }

        break;


    /*
     * 3. [New request] История заказов пользователя
     * GET http://moshoztorg.ru/applic.php?mode=orders_history
     */
    case 'orders_history':
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $arFilter = Array("USER_ID" => CUser::GetID());

        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
        while ($arOrder = $db_sales->Fetch()) {
            $db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
            while ($arProps = $db_props->Fetch())
                if ($arProps['CODE'] == 'ADDRESS')
                    $deliveryAddress = $arProps['VALUE'];
            $items = array();
            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "ORDER_ID" => $arOrder['ID']
                ),
                false,
                false,
                array("NAME", "QUANTITY", "PRODUCT_ID")
            );

            $objects = array();
            while ($arItems = $dbBasketItems->Fetch()) {

                if($objects_id = $arItems["PRODUCT_ID"]) {

                    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating", "PROPERTY_CML2_MANUFACTURER");
                    $arFilter = Array("ID" => $objects_id);
                    $resObj = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                    while ($obObj = $resObj->GetNextElement()) {
                        $arFields = $obObj->GetFields();
                        $PHOTOS = array();
                        if ($arFields['DETAIL_PICTURE'])
                            $PHOTOS[] = "https://moshoztorg.ru" . cfile::getpath($arFields['DETAIL_PICTURE']);
                        $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTO"));
                        while ($obProp = $resProp->GetNext())
                            if($obProp['VALUE'])
                                $PHOTOS[] = "https://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
                        $product = CCatalogProduct::GetByID($arFields['ID']);
                        $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");
                        $objects[$arFields['ID']] = array(
                            'object_id' => $arFields['ID'],
                            'name' => $arFields['NAME'],
                            'desc' => $arFields['PREVIEW_TEXT'],
                            'price' => $arPrice['DISCOUNT_PRICE'],
                            'code' => $arFields['PROPERTY_CML2_ARTICLE_VALUE'],
                            'vendor_code' => $arFields['PROPERTY_CML2_MANUFACTURER_ENUM_ID'], // @TODO коды откуда?
                            'picture' => $PHOTOS,
                            'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                            'rate_average' => $arFields['PROPERTY_RATING_VALUE']
                        );
                    }
                }
            }

            $data[] = array(
                "order_id" => $arOrder["ID"],
                "date" => MakeTimeStamp($arOrder["DATE_INSERT"], "DD.MM.YYYY HH:MI:SS"),
                "objects" => $objects,
            );
        }

        $RESULT['error_code'] = 0;
        $RESULT['data'] = $data;
        break;






}

    if($ex = $APPLICATION->GetException() && $RESULT['error_code']) {
        $RESULT['error_desc'] = $ex->GetString();
    }

AddMessage2Log('новый запрос', "applic","",0);
AddMessage2Log($_SERVER['X-Api-Token'], "applic_X-Api-Token","",0);
AddMessage2Log($_REQUEST, "applic_REQUEST","",0);
AddMessage2Log($userId, "applic_userId","",0);

if($_REQUEST['test']){
    echo'<pre>';print_r($USER->getid());echo"</pre>";
    echo'<pre>';print_r($RESULT);echo"</pre>";
    echo'<pre>';print_r(count($RESULT['data']));echo"</pre>";
}
if($json)
    echo json_encode($RESULT);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");