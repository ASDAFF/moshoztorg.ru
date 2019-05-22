<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
ini_set('default_socket_timeout', 10000);
ini_set("soap.wsdl_cache_enabled", 0);
set_time_limit(0);

//$_SERVER['DOCUMENT_ROOT'] = "/home/f/fabao1/zhangguang.ru/public_html";
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Sale;

use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;

Loader::includeModule("iblock");
Loader::includeModule("catalog");
Loader::includeModule("sale");

$i = 0;

$db_sales = CSaleOrder::GetList( array('ID' => 'DESC'), array("ID"=>39542) );
while ($ar_sales = $db_sales->Fetch())
{
	
	$saleOrder = Bitrix\Sale\Order::load($ar_sales["ID"]);
	$shipmentId = 65568;
	$shipmentCollection = $saleOrder->getShipmentCollection();
	$shipment = $shipmentCollection->getItemById($shipmentId);
	$store_id=$shipment->getStoreId();
	echo ($store_id);
	die();
		
	$new_email = false;
	$order = Sale\Order::load($ar_sales["ID"]);
	
	if ($order->getId()) $i++;
	
	
	$shipmentCollection = $order->getShipmentCollection();
		//$basket = $order->getBasket();
		/*УДАЛЯЕМ ОТГРУЗКУ, при удалении этим методом отгрузка исчезает*/
		//\Bitrix\Sale\Delivery\ExtraServices\Store
		
		

		foreach ($shipmentCollection as $shipment)
		{
			$shipmentId = $shipment->getId();
			echo '<br/>$shipmentId '.$shipmentId;
			if ($shipment->isSystem()) continue;
			echo '<br/>Found '.$shipment->getField('PRICE_DELIVERY');
			$id = $shipment->getField('DELIVERY_ID');
			echo '<br/>ID '.$shipment->getField('DELIVERY_ID');
			$name = $shipment->getField('DELIVERY_NAME');
			echo '<br/>NAME '.$shipment->getField('DELIVERY_NAME');
			
			$originalDeliveryId = $shipment->getDeliveryId();
			echo '<br/>originalDeliveryId '.$originalDeliveryId;
			
			$customPriceDelivery = $shipment->getField('CUSTOM_PRICE_DELIVERY');
			echo '<br/>customPriceDelivery '.$customPriceDelivery;
			
			$basePrice = $shipment->getField('BASE_PRICE_DELIVERY');
			echo '<br/>basePrice '.$basePrice;
			
			$store_id = $shipment->getStoreId();
			echo '<br/>store_id '.$store_id;


			//echo ('<Br/>setting '.intval($arOldOrder["delivery_price"]));
			
			/*
			if ($id && strlen ($name) < 1 ) {
				$service = Delivery\Services\Manager::getById($shipment->getField('DELIVERY_ID'));
				$shipment->setFields(array(
					'DELIVERY_ID' => $service['ID'],
					'DELIVERY_NAME' => $service['NAME'],
				));


				$shipment->save();		
				$order->save();
				
			}
			*/
			
			//$shipment->setField('PRICE_DELIVERY', intval($arOldOrder["delivery_price"]));
			//$shipment->setField('BASE_PRICE_DELIVERY', intval($arOldOrder["delivery_price"]));
			//$shipment->setField('DELIVERY_ID', $newDeliveryID);
			
			/*
			$shipmentId = $shipment->getId();		
			echo ('<br> now sid = '.$shipmentId);		
			$shipment2 = $order->getShipmentCollection()->getItemById($shipmentId);						
			$shipment2->setField('PRICE_DELIVERY', intval($arOldOrder["delivery_price"]));			
			$shipment2->save();
			*/
			
			
			
			//$shipment->save();			
			//$order->save();
			//echo '<Br/>new='.$shipment->getField('PRICE_DELIVERY');
			
			
			/*
			$shipment->disallowDelivery();
			$shipment->setField("DEDUCTED", "N");
			$shipment->setField("STATUS_ID", "DN");
			$shipment->delete();
			$shipment->save();			
			*/
		}
		
		
		
	/*
	$propertyCollection = $order->getPropertyCollection();
	$emailPropValue = $propertyCollection->getUserEmail();
	
	$user_id = $order->getUserId();	
	$rsUser = CUser::GetByID($user_id);
	$arUser = $rsUser->Fetch();
	$new_email = $arUser["EMAIL"];

	$email = $emailPropValue->getValue();
	if ( strlen($email) > 1 ) {
		continue;
	} elseif ($new_email) {
		$emailPropValue->setValue($new_email);
		echo ('<Br/>Set email '.$new_email.' for order '.$order->getId());
		$emailPropValue->save(); 	
		
	}
	*/
	
		
	/*
	$arProp  = $somePropValue->getProperty(); // массив данных о самом свойстве
	$propId  = $somePropValue->getPropertyId(); // ID свойства
	$propName = $somePropValue->getName(); // Название
	$isRequired = $somePropValue->isRequired(); // true, если свойство обязательное
	$propPerson = $somePropValue->getPersonTypeId(); // Тип плательщика
	$propGroup  = $somePropValue->getGroupId(); // ID группы

	Чтобы изменить значение свойства следует вызвать метод setValue и сохранить сущность

	$somePropValue->setValue("value");
	$order->save(); 
	// можно $somePropValue->save(), но пересчета заказа не произойдёт
	*/

	//echo ($order->getId().'<br/>');
} 

//echo ($i);