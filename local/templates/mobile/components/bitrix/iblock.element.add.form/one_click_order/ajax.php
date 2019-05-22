<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$iIblockId=getIBlockIdByCode("one_click_order");
$APPLICATION->IncludeComponent(
    "bitrix:iblock.element.add.form",
    "one_click_order_ajax",
    Array(
        "HIDDEN_PROPERTIES"=>array(
            getPropertyIdByCode("ELEMENT_ID",$iIblockId)=>'',
            getPropertyIdByCode("ART",$iIblockId)=>'',
            getPropertyIdByCode("PRICE",$iIblockId)=>'',
            "NAME"=>"Заказ от ".date("d.m.Y")." ".CUser::GetId()
        ),
        "PLACEHOLDERS"=>array(
            "PHONE"=>"+7 ( ___ ) ___ - __ - __",
        ),
        "COMPONENT_TEMPLATE" => ".default",
        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
        "CUSTOM_TITLE_DETAIL_PICTURE" => "",
        "CUSTOM_TITLE_DETAIL_TEXT" => "",
        "CUSTOM_TITLE_IBLOCK_SECTION" => "",
        "CUSTOM_TITLE_NAME" => "",
        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
        "CUSTOM_TITLE_PREVIEW_TEXT" => "",
        "CUSTOM_TITLE_TAGS" => "",
        "DEFAULT_INPUT_SIZE" => "30",
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
        "ELEMENT_ASSOC" => "CREATED_BY",
        "GROUPS" => array("2"),
        "IBLOCK_ID" => $iIblockId,
        "IBLOCK_TYPE" => "orders",
        "LEVEL_LAST" => "Y",
        "LIST_URL" => "",
        "MAX_FILE_SIZE" => "0",
        "MAX_LEVELS" => "100000",
        "MAX_USER_ENTRIES" => "100000",
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
        "PROPERTY_CODES" => array(
            'USER_NAME'=>getPropertyIdByCode("USER_NAME",$iIblockId),
            'PHONE'=>getPropertyIdByCode("PHONE",$iIblockId),
            'ELEMENT_ID'=>getPropertyIdByCode("ELEMENT_ID",$iIblockId),
            'ART'=>getPropertyIdByCode("ART",$iIblockId),
            'PRICE'=>getPropertyIdByCode("PRICE",$iIblockId),
            'QUANTITY'=>getPropertyIdByCode("QUANTITY",$iIblockId),
            "NAME"=>"NAME"
        ),
        "PROPERTY_CODES_REQUIRED" => array(
            'PHONE'=>getPropertyIdByCode("PHONE",$iIblockId),
        ),
        "RESIZE_IMAGES" => "N",
        "SEF_MODE" => "N",
        "STATUS" => "ANY",
        "STATUS_NEW" => "N",
        "USER_MESSAGE_ADD" => 'Спасибо, Ваш заказ успешно добавлен.',
        "USER_MESSAGE_EDIT" => 'Спасибо, Ваш заказ успешно добавлен.',
        "USE_CAPTCHA" => "N"
    )
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>