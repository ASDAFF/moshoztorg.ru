<?
define('PRICE_ID', 1);
define('DEFAULT_ONECLICK_USER_ID', 40492); //ID юзера, от имени которого идет заказ в 1 клик 


AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("OneClick", "OnAfterIBlockElementAddHandler"));	
class OneClick
{
    function OnAfterIBlockElementAddHandler( $arFields )
    {
		GLOBAL $APPLICATION;

        if ( getIBlockIdByCode( "one-click" )==$arFields['IBLOCK_ID']
            && CModule::IncludeModule("sale")
            && CModule::IncludeModule("catalog")
        ){

			$arOneclickFields = array(
                "NAME"=>filter_var( $arFields['NAME'], FILTER_SANITIZE_STRING),
                "PHONE"=>filter_var( $arFields['PROPERTY_VALUES']["PHONE"], FILTER_SANITIZE_STRING),
                "OFFER_ID"=>intval($arFields['PROPERTY_VALUES']["PRODUCT"])                
            );
            
			$iCatalogProduct = \CCatalogProduct::GetByID( $arOneclickFields['OFFER_ID'] );
                if ( !$iCatalogProduct )
                    \CCatalogProduct::Add($arFields);

			//Попытка создать нового пользователя
			$user = new \CUser;
			$hash = "oneclick_".randString(8);

			$arUserFields = Array(
				"NAME"              => filter_var( $arFields['NAME'], FILTER_SANITIZE_STRING),
				"LAST_NAME"         => "oneclick",
				"EMAIL"             => $hash . "@example.com",
				"LOGIN"             => $hash,
				"PASSWORD" => $hash,
				"LID"               => SITE_ID,
				"ACTIVE"            => "N",
				"PERSONAL_PHONE"    => filter_var($arFields['PROPERTY_VALUES']["PHONE"], FILTER_SANITIZE_STRING)
			);

			$userID = $user->Add($arUserFields);
			//Если не создан, то поставить из константы
			if(!$userID){
				$userID = DEFAULT_ONECLICK_USER_ID;
			}
			
			//данные товара
			$arSelect = Array();
			$arFilter = Array("ID"=>$arOneclickFields['OFFER_ID']);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
			if($ob = $res->GetNextElement())
			{
				$arFieldsProd = $ob->GetFields();	
				$arFieldsProd['PROPERTY_VALUES'] = $ob->GetProperties();
				$arFieldsProd['FIELD_VALUES'] = $ob->GetFields();
			}
			
			$arCatalogProductInfo = GetCatalogProductPrice($arOneclickFields['OFFER_ID'], PRICE_ID);
						
			if (!$ORDER_ID = CSaleOrder::Add(array(
						"LID"              => SITE_ID,
						"PERSON_TYPE_ID"   => 1,
						"PAYED"            => "N",
						"CANCELED"         => "N",
						"STATUS_ID"        => "N",
						"PRICE"            => $arCatalogProductInfo['PRICE'],
						"CURRENCY"         => "RUB",
						"USER_ID"          => $userID,
						"PAY_SYSTEM_ID"    => 1,
						"PRICE_DELIVERY"   => 0,
						"DELIVERY_ID"      => 3,
						"DISCOUNT_VALUE"   => 0,
						"TAX_VALUE"        => 0,
						"USER_DESCRIPTION" => "",
						"ADDITIONAL_INFO"=>"
						Имя: {$arOneclickFields['NAME']}\n
						Телефон: {$arOneclickFields['PHONE']}\n
						"
					))) 
			{
				if($ex = $APPLICATION->GetException()) {
                    $strError = $ex->GetString();
					AddMessage2Log('Ошибка добавления заказа в 1 клик '.$strError);							
				}
			}
		
			CSaleOrderPropsValue::Add(array("ORDER_ID" => $ORDER_ID, "ORDER_PROPS_ID"=>1, "NAME"=>"Ф.И.О.", "VALUE" => $arOneclickFields["NAME"]));
			CSaleOrderPropsValue::Add(array("ORDER_ID" => $ORDER_ID, "ORDER_PROPS_ID"=>3, "NAME"=>"Телефон", "VALUE" => $arOneclickFields["PHONE"]));
			
			//откладываем товары из корзины
			$dbBasketItems = CSaleBasket::GetList(
			   array(),
			   array( 
			   "FUSER_ID" => CSaleBasket::GetBasketUserID(),
			   "LID" => SITE_ID,
			   "ORDER_ID" => "NULL",
			   "DELAY" => "N"
			   ), 
				  false,
				  false,
			   array("ID", "DELAY")
			);
			
			while ($arBasketItems = $dbBasketItems->Fetch())
			{
			   $tmpBasketIDs[] = $arBasketItems["ID"];
			   $arFields2["DELAY"] = "Y";
			   CSaleBasket::Update($arBasketItems["ID"], $arFields2);
			}
			
			$basket_props = array(
				array("NAME" => "ШтрихКод", "CODE" => "CML2_BAR_CODE", "VALUE" => $arFieldsProd["PROPERTY_VALUES"]["CML2_BAR_CODE"]["VALUE"]),
				array("NAME" => "Артикул", "CODE" => "CML2_ARTICLE", "VALUE" => $arFieldsProd["PROPERTY_VALUES"]["CML2_ARTICLE"]["VALUE"]),
				array("NAME" => "Элемент каталога", "CODE" => "CML2_LINK", "VALUE" => $arFieldsProd["PROPERTY_VALUES"]["CML2_LINK"]["VALUE"])										
			);
			
			//добавляем корзину к заказу
			
			$arFieldsQty = array('QUANTITY' => 1);
			CCatalogProduct::Update($arOneclickFields['OFFER_ID'], $arFieldsQty);
		
			if (!Add2BasketByProductID($arOneclickFields['OFFER_ID'], 1, array(
				'ORDER_ID' => $ORDER_ID				
				), $basket_props)) 
			{
				
				if($ex = $GLOBALS['APPLICATION']->GetException())
					AddMessage2Log('Ошибка добавления в корзину '.$ex->GetString());
			}
			
			$arFieldsQty = array('QUANTITY' => 0);
			CCatalogProduct::Update($arOneclickFields['OFFER_ID'], $arFieldsQty);
			
			foreach($tmpBasketIDs as $tmpBasketID) {
			   $arFields2["DELAY"] = "N";
			   // возвращаем товары к заказу
			   CSaleBasket::Update($tmpBasketID, $arFields2);
			}			
        }
    }
}
?>