<?


// файл /bitrix/php_interface/init.php
// регистрируем обработчик
/*AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateHandler" );

// создаем обработчик события "OnBeforeIBlockElementUpdate"
function OnBeforeIBlockElementUpdateHandler(&$arFields)
{
    if (CUser::GetId()==37318){//пользователь it-sfera
        myPrintR( $arFields  , __FILE__, __LINE__ );
        die();
    }
}*/


/*
 * ГЛАВНАЯ ЛИЧНЫЙ КАБИНЕТ ЗАКАЗ

 Array
(
    [FIELDS] => Array
        (
            [USER_ID] => 43057
            [STATUS] => активен
            [MESSAGE] => Вы запросили ваши регистрационные данные.
            [LOGIN] => 4f.yodor
            [URL_LOGIN] => 4f.yodor
            [CHECKWORD] => 01c3bb36ad054404fe62db7c3d90c9eb
            [NAME] => Сидоров
            [LAST_NAME] => Игнат
            [EMAIL] => 4f.yodor@gmail.com
        )

    [USER_FIELDS] => Array
        (
            [ID] => 43057
            [TIMESTAMP_X] => 2016-09-29 12:09:19
            [LOGIN] => 4f.yodor
            [PASSWORD] => V51Oo5TJ9c748627a3c875cc1c3a4887dd8e2287
            [CHECKWORD] => VOjzqGcV4b8a948c16b2d21d09620d335c4e82fe
            [ACTIVE] => Y
            [NAME] => Сидоров
            [LAST_NAME] => Игнат
            [EMAIL] => 4f.yodor@gmail.com
            [LAST_LOGIN] => 2016-09-29 12:09:19
            [DATE_REGISTER] => 2016-09-29 12:09:19
            [LID] => el
            [PERSONAL_PROFESSION] =>
            [PERSONAL_WWW] =>
            [PERSONAL_ICQ] =>
            [PERSONAL_GENDER] =>
            [PERSONAL_BIRTHDATE] =>
            [PERSONAL_PHOTO] =>
            [PERSONAL_PHONE] =>
            [PERSONAL_FAX] =>
            [PERSONAL_MOBILE] =>
            [PERSONAL_PAGER] =>
            [PERSONAL_STREET] =>
            [PERSONAL_MAILBOX] =>
            [PERSONAL_CITY] =>
            [PERSONAL_STATE] =>
            [PERSONAL_ZIP] =>
            [PERSONAL_COUNTRY] =>
            [PERSONAL_NOTES] =>
            [WORK_COMPANY] =>
            [WORK_DEPARTMENT] =>
            [WORK_POSITION] =>
            [WORK_WWW] =>
            [WORK_PHONE] =>
            [WORK_FAX] =>
            [WORK_PAGER] =>
            [WORK_STREET] =>
            [WORK_MAILBOX] =>
            [WORK_CITY] =>
            [WORK_STATE] =>
            [WORK_ZIP] =>
            [WORK_COUNTRY] =>
            [WORK_PROFILE] =>
            [WORK_LOGO] =>
            [WORK_NOTES] =>
            [ADMIN_NOTES] =>
            [STORED_HASH] =>
            [XML_ID] =>
            [PERSONAL_BIRTHDAY] =>
            [EXTERNAL_AUTH_ID] =>
            [CHECKWORD_TIME] => 2016-09-29 12:09:19
            [SECOND_NAME] =>
            [CONFIRM_CODE] =>
            [LOGIN_ATTEMPTS] => 0
            [LAST_ACTIVITY_DATE] =>
            [AUTO_TIME_ZONE] =>
            [TIME_ZONE] =>
            [TIME_ZONE_OFFSET] =>
            [TITLE] =>
            [BX_USER_ID] => 8fa227d90a83bf6fe29521b995b14333
            [LANGUAGE_ID] =>
        )

    [SITE_ID] => el
    [EVENT_NAME] => USER_INFO
)
 */


//при отправке регистрационной информации, генерируем пароль и вставляем его в тело письма
AddEventHandler("main", "OnSendUserInfo", "MyOnSendUserInfoHandler");
function MyOnSendUserInfoHandler(&$arParams)
{
    if (isset($arParams['FIELDS']['USER_ID']) && $arParams['FIELDS']['USER_ID']>0
    && isset($arParams['FIELDS']['CHECKWORD']) && !empty($arParams['FIELDS']['CHECKWORD'])
    ){

        $password = WP::randomString();

        global $USER;
        $USER->Update($arParams['FIELDS']['USER_ID'], array(
            'PASSWORD' => $password
        ));
        $arParams['FIELDS']['NEW_PASSWORD'] = $password;

    }

}

AddEventHandler("sale", "OnBeforeOrderAdd", "OnBeforeOrderAdd");
/**
 * @param $arFields
 * @return bool
 * Вызывается перед добавлением заказа
 * Если заказ создан через яндекс-маркет то по-умолчанию заказ добавляется для анонимного пользователя (один и тот же, создается один раз)
 * при этом нам необходим новый пользователь
 */
function OnBeforeOrderAdd(&$arFields)
{
	$defaultUserID = CSaleUser::GetAnonymousUserID();
	if($arFields['USER_ID'] == $defaultUserID){
		$user = new \CUser;
		$hash = "yamarket_".randString(8);
		$arUserFields = Array(
			"NAME"              => 'yamarket',
			"LAST_NAME"         => "",
			"EMAIL"             => $hash . "@example.com",
			"LOGIN"             => $hash,
			"PASSWORD" => $hash,
			"LID"               => SITE_ID,
			"ACTIVE"            => "N",
		);

		if($newUserID = $user->Add($arUserFields)){
			$arFields['USER_ID'] = $newUserID;
		}
	}
	return true;
}

//AddEventHandler('catalog', 'OnGetDiscount', Array("itsfera", "OnGetDiscountHandler"));
AddEventHandler('catalog', 'OnGetDiscountResult', Array("itsfera", "OnGetDiscountResultHandler"));
class itsfera
{
    static private $pId = false;
    protected static $productPrice = null;

    public static function OnGetDiscountHandler($intProductID, $intIBlockID, $arCatalogGroups = array(), $arUserGroups = array(), $strRenewal = "N", $siteID = false, $arDiscountCoupons = false, $boolSKU = true, $boolGetIDS = false)
    {
        self::$pId = $intProductID;
        $curProductPrice = CPrice::GetBasePrice($intProductID, false, false, false);
        if($curProductPrice != false){
            self::$productPrice = $curProductPrice;
        }
        return true;
    }
    public function OnGetDiscountResultHandler(&$arResult){
        GLOBAL $USER;
        /*
        if($USER->isadmin()){
        	echo'<pre>';print_r($arResult);echo"</pre>";
            CModule::IncludeModule("iblock");
            $IBLOCK_ID = getIBlockIdByCode("discount_cards");
            $arSelect = Array("ID", "NAME", "PROPERTY_PERCENT");
            $arFilter = Array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "PROPERTY_USER_ID" => cuser::getid(),
                "PROPERTY_CARDTYPE" => 317085,
            );
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();

                $arResult = array(array(
                    'VALUE' =>  ($arFields['PROPERTY_PERCENT_VALUE']/100) * self::$productPrice['PRICE']
                ));
                echo'<pre>';print_r($arResult);echo"</pre>";
            }
        }
        /*
            $newAr = array();
            foreach($arResult as $key=>$val){
                if(strpos($val["NAME"],"#KMP#")===false || isset($_SESSION['KMP'][$intProductID]) || $_SESSION['KMPBASKET'][$intProductID]) {
                    $newAr[] = $val;
                }
            }
            $arResult = $newAr;
        */

    }
}

\Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible('catalog', 'OnSaleOrderSumm',
    function ($arFilter, $strCurrency = 'RUB') {

        CModule::IncludeModule("iblock");
        $mxResult = false;
        $IBLOCK_ID = getIBlockIdByCode("discount_cards");
        $arSelect = Array("ID", "NAME", "PROPERTY_TOTAL");
        $arFilter = Array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_USER_ID" => cuser::getid(),
            "PROPERTY_CARDTYPE" => 317086,
        );
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            $intTimeStamp = time();
            $mxLastOrderDate = ConvertTimeStamp($intTimeStamp, "FULL", "ru");
            $mxResult = array(
                'PRICE' => $arFields['PROPERTY_TOTAL_VALUE'],
                'CURRENCY' => $strCurrency,
                'LAST_ORDER_DATE' => $mxLastOrderDate,
                'TIMESTAMP' => $intTimeStamp,
            );

        }
        return $mxResult;
    }, false, 10);

