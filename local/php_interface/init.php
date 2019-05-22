<?

//use Bitrix\Main\Application;
use Bitrix\Sale;
use Bitrix\Catalog;


define('DEBUG_MODE', 'Y');
include_once("constants.php");
include_once("functions.php");
include_once("events.php");

session_start();

//подключаем доп файлы для модуля unisender.integration
include_once($_SERVER["DOCUMENT_ROOT"].getLocalPath()."/unisender.integration/update.php");




  //КОСТЫЛЬ. убираем редирект для dev площадок
 if (strpos($_SERVER['DOCUMENT_ROOT'],'/ext_www/') === FALSE ) {

//**********Редирект на мобильную версию сайта**********************

     $sUserAgent=$_SERVER['HTTP_USER_AGENT'];

//$request = Application::getInstance()->getContext()->getRequest();
     $sCurPage = $_SERVER['REQUEST_URI']; //$request->getRequestedPage();


//если посетитель нажал ссылку "Полная версия сайта" исключаем редирект и на мобильную версию сайта
     if ($_GET['from_mobile']=='Y'){
         $_SESSION['from_mobile'] = 'Y';
         header('location: '.FULL_SITE_VERSION.str_replace('from_mobile=Y', '', $sCurPage));
         die();
     }

     $sMobileDomen = str_replace('http://','',MOBILE_SITE_VERSION);

     if( strpos($_SERVER['SERVER_NAME'],$sMobileDomen)===false && !strpos($sCurPage, 'applic.php') && !isset($_SESSION['from_mobile']) &&  preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$sUserAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($sUserAgent,0,4))){
         //echo 'Редирект '.$_SERVER['SERVER_NAME'].' '.$sMobileDomen;
         //var_dump(strpos($_SERVER['SERVER_NAME'],$sMobileDomen));

         header('location: '.MOBILE_SITE_VERSION.$sCurPage);
     }

//*********************************************************************

 }











define('PRICE_CODE', "Типовое соглашение Интернет-МХТ");

include("oneclickorder.php"); //заказ в 1 клик, сохранение в базу заказов


	spl_autoload_register(function($class){
		$path = $_SERVER['DOCUMENT_ROOT'].'/local/classes/'.strtr($class, array('\\' => '/')).'.php';
		if(!file_exists($path)){
			return;
		}
		require_once($path);
	});

define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/devlog.txt");

//Региональность
AddEventHandler('main', 'OnPageStart', 'WPGetRegion');
function WPGetRegion(){
	Webprofy\Regional\Main::getInstance();
}

//Отправка СМС
// AddEventHandler('sale', 'OnSaleComponentOrderOneStepFinal', 'WPSendSmsAfterOrderComplete'); закоменчено 19/04/19
function WPSendSmsAfterOrderComplete($orderId, $arOrder){
	MHT\SmsWorker::getInstance()->setOrderId($orderId)->setStatusId($arOrder['STATUS_ID'])->send();
}

// AddEventHandler('sale', 'OnSaleStatusOrder', 'WPSendSmsOnOrderStatusChange'); закоменчено 19/04/19
function WPSendSmsOnOrderStatusChange($orderId, $statusId){
	MHT\SmsWorker::getInstance()->setOrderId($orderId)->setStatusId($statusId)->send();
}

function dump11($var, $vardump = false, $return = false) {
	static $dumpCnt;

	if (is_null($dumpCnt)) {
		$dumpCnt = 0;
	}
	ob_start();

		echo '<b>DUMP #' . $dumpCnt . ':</b> ';
		echo '<p>';
		$style = "
		border: 1px solid #696969;
		background: #eee;
		border-radius: 3px;
		font-size: 14px;
		font-family: calibri, arial, sans-serif;
		padding: 20px;
		";
		echo '<pre style="'.$style.'">';
		if ($vardump) {
			var_dump($var);
		} else {
			print_r($var);
		}
		echo '</pre>';
		echo '</p>';

	$cnt = ob_get_contents();
	ob_end_clean();
	$dumpCnt++;
	if ($return) {
		return $cnt;
	} else {
		echo $cnt;
	}
}

// Не запускайте это на агентах.
// Функция выполняется на кроне, файл /local/php_interface/cron_search_reindex.php
function Reindex_Search(){
	AddMessage2Log('Reindex started '.date("Y-m-d H:i:s"));
	$isIndexTime = !(date('H') >= 2 && date('H') < 7); // Не делаем индексацию с 2 до 7 — в это время идёт полная выгрузка

	if(CModule::IncludeModule("search") && $isIndexTime) {
		$Result= false;
		$Result = CSearch::ReIndexAll(false, 20, array('SITE_ID'=>'el', 'MODULE_ID'=>'iblock'), false);
		while(is_array($Result)) {
			$Result = CSearch::ReIndexAll(false, 20, $Result);
		}

		// Переиндексация корневых разделов
		CModule::IncludeModule('iblock');
		$ob = CIBlock::GetList(array("ID"=>"ASC"), array("TYPE" => "mht_products", "SITE_ID" => "el"));
		while($ar = $ob->GetNext()){

			if($ar['INDEX_SECTION'] == 'Y'){

				$res = CSearch::Index(
				    "iblock", // Название "модуля". Произвольный идентификатор группы контента на самом деле.
				    "SI".$ar['ID'], // ID элемента. В рамках "модуля".
				    Array(
				        "DATE_CHANGE"=>ConvertTimestamp(false, "FULL"), // Дата изменения
				        "TITLE"=>$ar['NAME'], // Заголовок контента, не участвует в индексе
				        "SITE_ID"=>array("el"),
				        "PARAM1"=>"mht_products", // Параметр 1 и
				        "PARAM2"=>"".$ar['ID'], // Параметр 2. Используются для фильтрации результатов
				        "PERMISSIONS"=>array("1", "2"), // Группы, которым доступны результаты.
				        "URL"=>$ar['LIST_PAGE_URL'], // URL контента
				        "BODY"=>str_repeat($ar['NAME']." ", 10), // Тело поискового индекса. Здесь должно быть всё, что должно попасть в индекс
				        "TAGS"=>""
				    ),
				    true // Переиндексировать
				);
			} else {
				CSearch::DeleteIndex("iblock", "SI".$ar['ID']);
			}
		}
	}
}

AddEventHandler("search", "BeforeIndex", Array("ClassFilterSearch", "BeforeIndexHandler"));
AddEventHandler("search", "OnBeforeFullReindexClear", Array("ClassFilterSearch", "OnBeforeFullReindexClearHandler"));
AddEventHandler('main', 'OnEventLogGetAuditTypes', Array("ClassFilterSearch", "AddEventTypes"));

class ClassFilterSearch{
	function BeforeIndexHandler($arFields){


		if(!CModule::IncludeModule("iblock")) // подключаем модуль
			return $arFields;

		if(!CModule::IncludeModule("sale")) // подключаем модуль
			return $arFields;

		if(!CModule::IncludeModule("catalog")) // подключаем модуль
			return $arFields;

		if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM1"] == 'mht_products') {

			$ar_res = CPrice::GetBasePrice($arFields["ITEM_ID"]);

			if ($ar_res['PRICE'] > 0){
				$res = CIBlockElement::GetByID($arFields["ITEM_ID"]);
				$ob = $res->GetNext();

				/*echo '<pre>';
				print_r($ob);
				echo '</pre>';*/
				if($ob && $ob["ACTIVE"] == "Y"){


					//добавляем ид инфоблока в теги. Для фильтрации по разделу.
					if ($ob['IBLOCK_SECTION_ID']>0){
						$arFields['TAGS'] = $ob['IBLOCK_SECTION_ID'];
						/*echo '<pre>';
						print_r($arFields);
						echo '</pre>';
						die();*/
					}

					//Вытаскиваем код товара (CML2_TRAITS) в индекс
					$itemcode = '';
					$ob = CIBlockElement::GetProperty($arFields['PARAM2'], $arFields['ITEM_ID'], array(), array("CODE" => "CML2_TRAITS"));
					while($ar = $ob->GetNext()){
						if($ar['DESCRIPTION'] == 'Код')
							$itemcode = $ar['VALUE'];
					}
					if($itemcode){
						$arFields["BODY"].=' '.$itemcode;
					}

					//Вытаскиваем код товара (CML2_MANUFACTURER) в индекс
					$itemcode = '';
					$ob = CIBlockElement::GetProperty($arFields['PARAM2'], $arFields['ITEM_ID'], array(), array("CODE" => "CML2_MANUFACTURER"));
					while($ar = $ob->GetNext()){
						$itemcode = $ar['VALUE_ENUM'];
					}
					if($itemcode){
						$arFields["BODY"].=' '.$itemcode;
					}

					$arFields["PARAMS"]["SEARCH_PAGE"] = 'Y';
				}else{
					$arFields["BODY"]='';
					$arFields["TITLE"]='';
				}
			} else if(substr($arFields['ITEM_ID'], 0, 1) == 'S'){ // Разделы инфоблока начинаются с S
				// 10 раз название раздела, повышаем ранг
				$arFields["BODY"] = $arFields["BODY"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"]." ".$arFields["TITLE"];
			} else {
				// Товары без цены не индексируем
				$arFields["BODY"]='';
				$arFields["TITLE"]='';
			}
		}

		return $arFields;
	}
	/* function OnReindexHandler($NS){
		CEventLog::Add(array(
			"SEVERITY" => "SECURITY",
			"AUDIT_TYPE_ID" => "SEARCH_REINDEX",
			"MODULE_ID" => "search",
			"ITEM_ID" => "",
			"DESCRIPTION" => "Запущена переиндексация поиска",
      	));
	}
	*/
	function OnBeforeFullReindexClearHandler(){
		CEventLog::Add(array(
			"SEVERITY" => "SECURITY",
			"AUDIT_TYPE_ID" => "SEARCH_FULL_REINDEX",
			"MODULE_ID" => "search",
			"ITEM_ID" => "",
			"DESCRIPTION" => "Запущена полная переиндексация сайта. Поисковый индекс будет удалён и создан заново.",
      	));
	}
	function AddEventTypes(){
		return array(
			'SEARCH_REINDEX' => '[SEARCH_REINDEX] Переиндексация поиска',
			'SEARCH_FULL_REINDEX' => '[SEARCH_FULL_REINDEX] Полная переиндексация поиска'
		);
	}
}

function old_basket_email() {
	global $DB;
	CModule::includeModule("sale");
	$arFilter[">=DATE_INSERT"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), (time()-(48 * 3600)));
	$arFilter["<=DATE_INSERT"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), (time() - (24 * 3600)));
	$arFilter["!USER_ID"] = false;
	$dbResultList = CSaleBasket::GetLeave(
		array(),
		$arFilter,
		false,
		false
	);

	$arBaskets = array();
	while($arBasket = $dbResultList->GetNext()) {
		$arFilterBasket = Array("ORDER_ID" => false, "FUSER_ID" => $arBasket["FUSER_ID"], "LID" => $arBasket["LID"]);
		$dbB = CSaleBasket::GetList(
			array("ID" => "ASC"),
			$arFilterBasket,
			false,
			false,
			array("ID", "PRODUCT_ID", "NAME", "QUANTITY", "PRICE", "CURRENCY", "DETAIL_PAGE_URL", "LID", "CAN_BUY", "SUBSCRIBE", "DELAY", "ORDER_ID")
		);

		while($arB = $dbB->GetNext()) {
			$arBasket["ITEMS"][] = $arB;
		}

		$arBasket["HTML_BASKET"] = getHtmlBasket($arBasket);
		$arBaskets[] = $arBasket;
	}

	if(!empty($arBaskets)) {
		foreach($arBaskets as $arBasket) {
			$arMailFields = array(
				"NAME" => $arBasket["USER_NAME"] . " " . $arBasket["USER_LAST_NAME"],
				"EMAIL" => $arBasket["USER_EMAIL"],
				"BASKET" => $arBasket["HTML_BASKET"],
			);
			CEvent::SendImmediate("WP_LEAVE_BASKET_REMIND", "el", $arMailFields);
		}
	}
	return "old_basket_email();";
}

function getHtmlBasket($arBasket) {
	$html = "";
	$totalPrice = 0;
	if(!empty($arBasket)) {
		foreach ($arBasket["ITEMS"] as $arBasketItem) {
			$totalPrice += $arBasketItem["PRICE"] * $arBasketItem["QUANTITY"];
			$html .= "<a href='http://mht.ru{$arBasketItem["DETAIL_PAGE_URL"]}'>{$arBasketItem["NAME"]}</a> x {$arBasketItem["QUANTITY"]} шт. - {$arBasketItem["PRICE"]} {$arBasketItem["CURRENCY"]} <br/>";
		}
		$html .= "Общая стоимость: {$arBasket["PRICE_ALL"]} {$arBasket["CURRENCY"]}<br/>";
	}
	return $html;
}

function order_interview_email () {
    // Функция рассылки письем по заказам совершенным три дня назад
    if (CModule::IncludeModule("sale")):
        global $DB;
        $startDay = '-3';
        $endDate = '-2'; // переставить на -2 для рабочего режима, установить на 0 для тестирования
        $data_n=date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL ")), mktime(0, 0, 0, date("n", strtotime($startDay.' days')), date("j", strtotime($startDay.' days')), date("Y"), strtotime($startDay.' days'))); // начальная дата -3дня
        $data_k=date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), mktime(23, 59, 59, date("n", strtotime($endDate.' days')), date("j", strtotime($endDate.' days')), date("Y"), strtotime($endDate.' days'))); // конечная дата -2дня

        $arFilter = Array(">=DATE_INSERT" => $data_n,"<=DATE_INSERT" => $data_k);
        $arSelect = Array("ID");
        $rsSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter, false, array("nTopCount"=> 100), $arSelect);
        while ($arSales = $rsSales->Fetch())
        {
            // COption::SetOptionString("sale", "interview_email_" . $arSales['ID'], 'N'); // Снять комментарий при тестировании. Письма будут отправляться, даже если были отправлены ранее
            if(COption::GetOptionString("sale","interview_email_".$arSales['ID']) != 'Y') {
                $arProperties = CSaleOrderPropsValue::GetOrderProps($arSales['ID']);
                while ($arProperty = $arProperties->GetNext()) {
                    if ($arProperty['CODE'] == 'CONTACT_PERSON') {
                        $NAME = $arProperty['VALUE'];
                    } elseif ($arProperty['CODE'] == 'EMAIL') {
                        $EMAIL = $arProperty['VALUE'];
                    }
                }
                $arEventFields = array(
                    "ORDER_ID" => $arSales['ID'],
                    "EMAIL_TO" => $EMAIL,
                    "NAME" => $NAME
                );
                CEvent::Send("WEBPROFY_ORDER_INTERVIEW", 's1', $arEventFields);
                COption::SetOptionString("sale", "interview_email_" . $arSales['ID'], 'Y');
            }
        }
    endif;
    return "order_interview_email();";
}

//Добавление или изменение свойства заказа
function UpdateOrderProperty($prop_code, $value, $order_id) {

	if ($prop_code && $order_id && $value) {

	    $order = Sale\Order::load($order_id);
        $propertyCollection = $order->getPropertyCollection();

        if (CModule::IncludeModule('sale')) {

            $arFilter = array(
                "ORDER_ID"       => $order_id,
                "CODE"           => $prop_code,
                "PERSON_TYPE_ID" => $order->getPersonTypeId()
            );

            $arProps = CSaleOrderProps::GetList(
                array(),
                $arFilter
            )->Fetch();

            if ($arProps['ID']) {

                $somePropValue = $propertyCollection->getItemByOrderPropertyId($arProps['ID']);

                $somePropValue->setValue($value);
                $somePropValue->save();

                return true;
            }
        }

    } else {
        return false;
    }

}

/*
AddEventHandler("search", "OnReindex", Array("ClassFilterReSearch", "BeforeIndexHandler"));
class ClassFilterReSearch{
	function BeforeIndexHandler($NS, $oCallback, $callback_method){

		AddMessage2Log('reindex '.print_r($NS, 1), "main");

		if(!CModule::IncludeModule("iblock")) // подключаем модуль
			return $arFields;

		if(!CModule::IncludeModule("sale")) // подключаем модуль
			return $arFields;

		if(!CModule::IncludeModule("catalog")) // подключаем модуль
			return $arFields;

		if($NS["MODULE"] == "iblock") {
			$ar_res = CPrice::GetBasePrice($NS["ID"]);
			if ($ar_res['PRICE'] <= 0){
				return false;
			}else{
				$result = $NS["ID"];
			}
		}
		return $result;
	}
}
*/


//-- Добавление обработчика события

AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

//-- Собственно обработчик события

function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
  $arOrder = CSaleOrder::GetByID($orderID);

 $_REQUEST["DELIVERY_NAME"] = CSaleDelivery::GetByID($_REQUEST["DELIVERY_ID"]);
 $_REQUEST["DELIVERY_NAME"] = "Способ доставки: \r\n".$_REQUEST["DELIVERY_NAME"]["NAME"];

  if($_REQUEST["DELIVERY_ID"] == 2){
	   $_REQUEST["ORDER_PROP_7"] = "";
  }else{
	$_REQUEST["ORDER_PROP_7"] = "Адрес доставки: \r\n".$_REQUEST["ORDER_PROP_7"];

  }

   $arFields = array_merge($arFields, $_REQUEST);
}

// Предупреждения о последствиях ручной индексации
AddEventHandler("main", "OnAdminTabControlBegin", "SearchIndexWarning");
function SearchIndexWarning(&$form)
{
    if($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/search_reindex.php")
    {
        $lockfile = $_SERVER["DOCUMENT_ROOT"]."/local/cron_search_lock.tmp";
		$locktime = 7200;

		if(file_exists($lockfile) && (time() - filemtime($lockfile)) < $locktime){
			echo CAdminMessage::ShowMessage("Автоматическая переиндексация запущена ".FormatDate("x", filemtime($lockfile)).". Параллельный запуск переиндексации может повредить поисковый индекс!");
		} else {
			echo CAdminMessage::ShowNote("Настроен автоматический запуск переиндексации инфоблоков раз 30 минут. Воздержитесь от ручного запуска.");
		}
    }
}


AddEventHandler("main", "OnAfterUserLogin", Array("AuthUserFavotires", "OnAfterUserLoginHandler"));

class AuthUserFavotires
{
	function OnAfterUserLoginHandler(&$arFields)
	{
        global $APPLICATION, $DB, $USER;
		$user_id = $APPLICATION->get_cookie("USER_FAVORITE_ID");

		if($user_id != '' && $arFields["USER_ID"] > 0) {
			$DB->Query("UPDATE `mht_favorites` SET `USER_ID` = '".$arFields["USER_ID"]."' WHERE `USER_ID` = '".$user_id."';");
			$APPLICATION->set_cookie("USER_FAVORITE_ID", "");
		}
	}
}


AddEventHandler("iblock", "OnAfterIBlockAdd", "OnAfterIBlockAddHandler");

function OnAfterIBlockAddHandler(&$arFields) {

	if($arFields["IBLOCK_TYPE_ID"]=='mht_products' && !isset($arFields["UPDATE_FROM_EVENT"]) ){
		$ib = new CIBlock;
		if ($ib->Update($arFields['ID'], Array("SITE_ID" => Array("el", "mo"),'UPDATE_FROM_EVENT'=>'Y') )) //echo "<p>Инфоблок {$arIblock['ID']} обновлен.</p>";
		unset($ib);
	}
}

/* глючит при создании заказа из админки вручную
AddEventHandler("main", "OnAdminListDisplay", "MyOnAdminListDisplay");
function MyOnAdminListDisplay(&$list)
{
	if($list->table_id = "tbl_sale_order")
		echo $list->sNavText;
}
*/


AddEventHandler("main", "OnBeforeUserAdd", "UserPhoneHandler");
AddEventHandler("main", "OnBeforeUserUpdate", "UserPhoneHandler");
function UserPhoneHandler(&$arFields) {
	if(isset($arFields["PERSONAL_PHONE"])) {
		$phone = $arFields["PERSONAL_PHONE"];
		$firstChar = substr($phone, 0, 1);
		$lastCharNum = strlen($phone) - 1;
 		if($firstChar=="+") {
			$phone = substr($phone, 2, $lastCharNum);
		}

		if($firstChar=="8") {
			$phone = substr($phone, 1, $lastCharNum);
		}

		$phone = preg_replace('/[^0-9]/', '', $phone);
		$arFields["PERSONAL_PHONE"] = $phone;
	}
}


/******************************************
	Авторизация по логину, email и телефону
	Радченко, 26/07/2018
******************************************/

AddEventHandler("main", "OnBeforeUserLogin", "OnBeforeUserLoginHandler");
function OnBeforeUserLoginHandler(&$arFields)
{
	/*print_r ($arFields);
	die();
	*/

	$phoneRegEx = '/\+?[78]?[\(\-\s]{0,2}?\d{3}[\)\-\s]{0,2}?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}/'; //я сам охуел
	$emailRegEx = '/\S{1,30}\@\S{1,30}\.\S{1,10}/';

	if ( preg_match($phoneRegEx, $arFields["LOGIN"]) ):

		$tel = str_replace(array(' ','+','(',')','-'), "", $arFields["LOGIN"]);
		//ищем юзера по вариантам телефона с 7, +7 и 8 в начале

		$stringLength = strlen($tel);
		$arSearch = array();

		switch ($stringLength) {

			case "10":

				$phoneBody = $tel;
				break;

			case "11":

				$phoneBody = substr($tel, 1, 10);
				break;

			case "12":

				$phoneBody = substr($tel, 2, 10);
				break;

			default:

				break;

		}


		$arSearch = array(
			"LOGIC" => "OR",
			$phoneBody,
			'+7'.$phoneBody,
			'7'.$phoneBody,
			'8'.$phoneBody,
		);

		$arUser = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID', 'LOGIN'),
            'filter' => array(
                '=ACTIVE' => 'Y',
				array(
					"LOGIC" => "OR",
					"=PERSONAL_PHONE" => $arSearch,
					"=PERSONAL_MOBILE" => $arSearch,
					"=WORK_PHONE" => $arSearch
				)
            )
        ))->fetch();

		if ( $arUser['LOGIN'] ):
			$arFields["LOGIN"] = $arUser['LOGIN'];
		endif;

	elseif ( preg_match($emailRegEx, $arFields["LOGIN"]) ):

		//ищем юзера по email

		$arUser = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID', 'LOGIN'),
            'filter' => array(
                '=ACTIVE' => 'Y',
				"=EMAIL" => $arFields["LOGIN"]
            )
        ))->fetch();

		if ( $arUser['LOGIN'] ):
			$arFields["LOGIN"] = $arUser['LOGIN'];
		endif;

	endif;

}


AddEventHandler("sale", "OnCondSaleControlBuildList", Array("checkUrlCond", "GetControlDescr"));
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("checkUrlCond", "GetControlDescr"));

AddEventHandler("sale", "OnCondSaleControlBuildList", Array("checkCookieCond", "GetControlDescr"));
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("checkCookieCond", "GetControlDescr"));
