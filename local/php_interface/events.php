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
	global $USER;
    if (isset($arParams['FIELDS']['USER_ID']) && $arParams['FIELDS']['USER_ID']>0
    && isset($arParams['FIELDS']['CHECKWORD']) && !empty($arParams['FIELDS']['CHECKWORD'])
    ){

        $password = WP::randomString();

        $USER->Update($arParams['FIELDS']['USER_ID'], array(
            'PASSWORD' => $password
        ));

        $arParams['FIELDS']['NEW_PASSWORD'] = $password;

    }

}


AddEventHandler("main", "OnAfterUserRegister", "sendCouponForRegistration");
function sendCouponForRegistration(&$arFields)
{

	AddMessage2Log(print_r ($arFields, 1), "~~~****%%%");
	if ($arFields["USER_ID"]) {
		//добавляем купон за регистрацию
		$coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);

		$addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add(array(
			'DISCOUNT_ID' => 4,
			'ACTIVE' => 'Y',
			'COUPON' => $coupon,
			'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
			'MAX_USE' => 1,
			'USER_ID' => $arFields["USER_ID"],
			'DESCRIPTION' => 'Скидка за регистрацию на сайте',
		));


		if($addDb->isSuccess()) {
			$arEventFields = array( "COUPON"=>$coupon, "EMAIL"=>$arFields["EMAIL"], "USER_ID"=>$arFields["USER_ID"] );
			if ( ! \CEvent::Send("ITSFERA_COUPON_FOR_REGISTRATION", 'el', $arEventFields, 'Y', 170)) {
				\CEventLog::Add(array(
					"SEVERITY"      => "INFO",
					"AUDIT_TYPE_ID" => "DEBUG",
					"MODULE_ID"     => "main",
					"ITEM_ID"       => 123,
					"DESCRIPTION"   => "Ошибка отправки сообщения. Скидка за регистрацию на сайте, шаблон 170",
				));
			}
		}

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


AddEventHandler('catalog', 'OnGetDiscountResult', Array("itsferaEvents", "OnGetDiscountResultHandler"));
AddEventHandler("catalog", "OnGetDiscount", array("itsferaEvents", "OnGetDiscountHandler"));


//\Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible('catalog', 'OnSaleOrderSumm',
//    function ($arFilter, $strCurrency = 'RUB') {
//
//        GLOBAL $USER;
//        $mxResult = false;
//        if ($USER->IsAuthorized()) {
//            CModule::IncludeModule("iblock");
//            $IBLOCK_ID = getIBlockIdByCode("discount_cards");
//            $arSelect = Array("ID", "NAME", "PROPERTY_TOTAL");
//            $arFilter = Array(
//                "IBLOCK_ID" => $IBLOCK_ID,
//                "PROPERTY_USER_ID" => cuser::getid(),
//                "PROPERTY_CARDTYPE" => 317086,
//            );
//            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
//            if ($ob = $res->GetNextElement()) {
//                $arFields = $ob->GetFields();
//                // подменим сумму по заказам для накопительной скидки на ту что выгрузили в картах из 1С
//                $intTimeStamp = time();
//                $mxLastOrderDate = ConvertTimeStamp($intTimeStamp, "FULL", "ru");
//                $mxResult = array(
//                    'PRICE' => $arFields['PROPERTY_TOTAL_VALUE'],
//                    'CURRENCY' => $strCurrency,
//                    'LAST_ORDER_DATE' => $mxLastOrderDate,
//                    'TIMESTAMP' => $intTimeStamp,
//                );
//
//            }
//        }
//        return $mxResult;
//    }, false, 10);


//меняем цену товара на СтараяЦена и выполнении условий
\Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible('catalog', 'OnGetOptimalPriceResult',
    function (&$arResult) {
        GLOBAL $USER;
        if ($USER->IsAuthorized() && isset( $arResult['DISCOUNT']['VALUE'] )) {
            $arSelect = Array("ID", "NAME", "PROPERTY_SAYT_AKTSIONNYY_TOVAR", "PROPERTY_OLD_PRICE_1");
            $arFilter = Array("ID" => $arResult['PRODUCT_ID'], "ACTIVE" => "Y");
            $res      = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();

                if (($arFields["PROPERTY_SAYT_AKTSIONNYY_TOVAR_VALUE"] == 'Да') &&
                    ($arFields['PROPERTY_OLD_PRICE_1_VALUE'])) {

                    $discount_price = $arResult['PRICE']['PRICE'];          //цена со скидкой
                    $base_price = $arFields['PROPERTY_OLD_PRICE_1_VALUE'];  //цена без скидки


                    $arResult['PRICE']['PRICE'] = $base_price;
                    $arResult['RESULT_PRICE']['BASE_PRICE']         = $base_price;
                    $arResult['RESULT_PRICE']['DISCOUNT_PRICE']         = $discount_price;
                    $arResult['RESULT_PRICE']['UNROUND_BASE_PRICE']         = $base_price;
                    $arResult['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'] = $discount_price;

                    $arResult['RESULT_PRICE']['DISCOUNT'] = $base_price - $discount_price;

                    $arResult['DISCOUNT_PRICE'] = $discount_price;


// что должно получится на выходе
//array (
//  'PRICE' =>
//  array (
//    'ID' => '444945',
//    'CATALOG_GROUP_ID' => '1',
//    'PRICE' => '2499.00',
//    'CURRENCY' => 'RUB',
//    'QUANTITY_FROM' => NULL,
//    'QUANTITY_TO' => NULL,
//    'PRODUCT_ID' => '664507',
//    'ELEMENT_IBLOCK_ID' => 461,
//    'QUANTITY' => 1,
//    'BASKET_CODE' => 1006983,
//    'VAT_RATE' => 0,
//    'VAT_INCLUDED' => 'N',
//  ),
//  'RESULT_PRICE' =>
//  array (
//    'PRICE_TYPE_ID' => '1',
//    'BASE_PRICE' => 2499,
//    'DISCOUNT_PRICE' => 1250,
//    'CURRENCY' => 'RUB',
//    'DISCOUNT' => 1249,
//    'PERCENT' => 49.979999999999997,
//    'VAT_RATE' => 0,
//    'VAT_INCLUDED' => 'Y',
//    'UNROUND_BASE_PRICE' => 2499,
//    'UNROUND_DISCOUNT_PRICE' => 1249.5,
//    'ROUND_RULE' =>
//    array (
//      'PRICE' => '0.0000',
//      'ROUND_TYPE' => '2',
//      'ROUND_PRECISION' => '1.0000',
//      'CATALOG_GROUP_ID' => '1',
//    ),
//  ),
//  'DISCOUNT_PRICE' => 1250,
//  'DISCOUNT' =>
//  array (
//  ),
//  'DISCOUNT_LIST' =>
//  array (
//
//  ),
//  'PRODUCT_ID' => '664507',
//)
//

                }
            }
        }
    }, false, 10);

/**
 * Удаляем изменение раздела при обновлении элемента из 1с
 */
\Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible('iblock', 'OnBeforeIBlockElementUpdate',
    function (&$arFields) {
        if (isset($_GET['type'], $_GET['mode']) && $_GET['type'] === 'catalog' && $_GET['mode'] === 'import') {
            unset($arFields['IBLOCK_SECTION_ID']);
            unset($arFields['IBLOCK_SECTION']);
        }
    });


//Добавляем новую дату доставки заказа для Москвы
\Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible("main", "OnBeforeEventAdd",
    function (&$event, &$lid, &$arFields)
    {

        if (isset($arFields['ORDER_ID'],$arFields['ORDER_REAL_ID'],$arFields['ORDER_DATE'])){
            $order = Bitrix\Sale\Order::load($arFields['ORDER_ID']);
            $paymentIds = $order->getPaymentSystemId();

            if (count(array_intersect($paymentIds,array(1,2,7,8))) > 0) {
                $objDateTime = new \Bitrix\Main\Type\DateTime($arFields['ORDER_DATE']);
                $arFields['NEW_ORDER_DATE'] = 'Ориентировочная дата доставки - '.$objDateTime->add("4 day")->format("d.m.Y");
            }
        }
});