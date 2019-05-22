<?
/*
	IPOLIML_NOCACHE    - если задан - не использовать кэш
*/
cmodule::includeModule('sale');
IncludeModuleLangFile(__FILE__);

class CDeliveryIML extends imlHelper{
	static $courierPrice = 250;
	static $pickupPrice  = 120;
	static $profiles     = false;
	static $hasPVZ       = false;//грузим ли ПВЗ
	
	static $date         = false;
	static $price        = false;
	
	static $orderWeight  = false;
	static $orderPrice   = false;
	
	static $nalPayChosen = false; // флаг из проверки возможности оплаты (checkNal)
	static $psChecks     = false; // флаг из проверки выбранной платежной системы (checkPS)

	function Init(){
		return array(
			/* Basic description */
			"SID" => "iml",
			"NAME" => "IML",
			"DESCRIPTION" => GetMessage('IPOLIML_DELIV_DESCR'),
			"DESCRIPTION_INNER" => GetMessage('IPOLIML_DELIV_DESCRINNER'),
			"BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),
			"HANDLER" => __FILE__,

			/* Handler methods */
			"DBGETSETTINGS" => array("CDeliveryIML", "GetSettings"),
			"DBSETSETTINGS" => array("CDeliveryIML", "SetSettings"),
			"GETCONFIG" => array("CDeliveryIML", "GetConfig"),

			"COMPABILITY" => array("CDeliveryIML", "Compability"),      
			"CALCULATOR" => array("CDeliveryIML", "Calculate"),      

			/* List of delivery profiles */
			"PROFILES" => array(
				"courier" => array(
					"TITLE" => GetMessage('IPOLIML_DELIV_COURIER_TITLE'),
					"DESCRIPTION" => GetMessage('IPOLIML_DELIV_COURIER_DESCR'),

					"RESTRICTIONS_WEIGHT" => array(0,25000),
					"RESTRICTIONS_SUM" => array(0),
					
					"RESTRICTIONS_MAX_SIZE" => "1000",
					"RESTRICTIONS_DIMENSIONS_SUM" => "1500",
				),
				"pickup" => array(
					"TITLE" => GetMessage('IPOLIML_DELIV_PICKUP_TITLE'),
					"DESCRIPTION" => GetMessage('IPOLIML_DELIV_PICKUP_DESCR'),

					"RESTRICTIONS_WEIGHT" => array(0,20000),
					"RESTRICTIONS_SUM" => array(0),
					"RESTRICTIONS_MAX_SIZE" => "1000",
					"RESTRICTIONS_DIMENSIONS_SUM" => "1500"
				)
			)
		);
	}

	function GetConfig(){
		$arConfig = array(
			"CONFIG_GROUPS" => array(
				"price" => GetMessage('IPOLIML_DELIV_CONFGROUP_PAY'),
			),

			"CONFIG" => array(
				"courier_price_native" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => self::$courierPrice,
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_CPN')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),
				"courier_free_native" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => "",
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_CFN')." (".COption::GetOptionString("sale", "default_currency", "RUB").') ',
					"GROUP"   => "price",
				),
				"pickup_price_native" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => self::$pickupPrice,
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_PPN')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),
				"pickup_free_native" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => "",
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_PFN')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),
				"courier_price_other" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => self::$courierPrice,
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_CPO')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),
				"courier_free_other" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => "",
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_CFO')." (".COption::GetOptionString("sale", "default_currency", "RUB").') ',
					"GROUP"   => "price",
				),
				"pickup_price_other" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => self::$pickupPrice,
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_PPO')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),
				"pickup_free_other" => array(
					"TYPE"    => "STRING",
					"DEFAULT" => "",
					"TITLE"   => GetMessage('IPOLIML_DELIV_CONF_PFO')." (".COption::GetOptionString("sale", "default_currency", "RUB").')',
					"GROUP"   => "price",
				),						
			),
		);

		return $arConfig; 
	}

	function SetSettings($arSettings){
		if(!is_numeric($arSettings['courier_price_native']))
			$arSettings['courier_price_native'] = self::$courierPrice;
		if(!is_numeric($arSettings['pickup_price_native']))
			$arSettings['pickup_price_native'] = self::$pickupPrice;
		if(!is_numeric($arSettings['courier_price_other']))
			$arSettings['courier_price_other'] = self::$courierPrice;
		if(!is_numeric($arSettings['pickup_price_other']))
			$arSettings['pickup_price_other'] = self::$pickupPrice;
		
		foreach(array('courier_free_native','pickup_free_native','courier_free_other','pickup_free_other') as $name)
			if(!is_numeric($arSettings[$name]))
				$arSettings[$name] = '';

		return serialize($arSettings);
	}

	function GetSettings($strSettings){
		return unserialize($strSettings);
	}

	function Compability($arOrder, $arConfig){
		if(!self::isLogged())
			return false;

		$arKeys = array();

		self::$orderWeight = $arOrder['WEIGHT'];
		self::$orderPrice  = $arOrder['PRICE'];

		self::getIMLCity($arOrder['LOCATION_TO']);

		if(self::$city){
			self::defineProfiles();
			if(!count(self::$profiles))
				self::reCheckProfiles(); // если город не найден из-за названия
			$arKeys = array_keys(self::$profiles);
		}
	
		if((COption::GetOptionString(self::$MODULE_ID,'countType','T') != 'T')){
			self::formService($arOrder);
			$countFromTable = (COption::GetOptionString(self::$MODULE_ID,'serverToTable','Y') == 'Y');
			foreach($arKeys as $key => $profile){
				self::$serviceData['PROFILE'] = $profile;
				$price = self::getServerPrice();
				if(
					array_key_exists('ERROR',$price) &&
					(!$countFromTable || !array_key_exists('CASE',$price) || $price['CASE'] != 'DEADSERV')
				)
					unset($arKeys[$key]);
			}
			unset(self::$serviceData['PROFILE']);
		}

		$ifPrevent=true;

		foreach(GetModuleEvents(self::$MODULE_ID, "onCompabilityBefore", true) as $arEvent)
			$ifPrevent = ExecuteModuleEventEx($arEvent,Array($arOrder,$arConfig,&$arKeys));

		if(is_array($ifPrevent)) { 
			$newKeys = array();
			foreach($ifPrevent as $val) {
				if(in_array($val, $arKeys))
					$newKeys[] = $val;
			}
			$arKeys = $newKeys;
		}

		if(!$ifPrevent) return array();
		
		if(!COption::GetOptionString(self::$MODULE_ID,'pvzPicker',false) && in_array('pickup',$arKeys))
			unset($arKeys['pickup']);

		// Подключение FrontEnd (для многостраничного компонента)
		if($_POST['CurrentStep'] > 1 && $_POST['CurrentStep'] < 4 && in_array('pickup',$arKeys))
			self::pickupLoader();

		return $arKeys; 
	}

	function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false){//рассчет стоимости
		if(!self::$city)
			self::getIMLCity($arOrder['LOCATION_TO']);

		$deliveryTerms = false;

		$countTable = (COption::GetOptionString(self::$MODULE_ID,'countType','T') == 'T');
		$countTableServerDead = (COption::GetOptionString(self::$MODULE_ID,'serverToTable','Y') == 'Y');

		if(!$countTable){
			self::formService($arOrder);
			if(!array_key_exists('SERVICE',self::$serviceData) || !self::$serviceData['SERVICE'])
				self::$serviceData['PROFILE'] = $profile;
			$deliveryTerms = self::getServerPrice();
			if(!array_key_exists('ERROR',$deliveryTerms)){
				$dT = strtotime($deliveryTerms['term']);
				$deliveryTerms['term'] = ceil((strtotime($deliveryTerms['term']) - mktime())/86400);
			}
		}
		
		if(
			$countTable || 
			(
				$countTableServerDead && array_key_exists('ERROR',$deliveryTerms) && array_key_exists('CASE',$deliveryTerms) && $deliveryTerms['CASE'] == 'DEADSERV'
			)
		){
			$price = self::getTablePrice($profile,$arConfig,$arOrder);
			$dT    = self::countDelivTime($arOrder['LOCATION_TO']);
			$dT    = (self::checkToday()) ? $dT[0] : $dT[1];
			
			$time = date('d',$dT-mktime())+31*(date('m',$dT-mktime())-1);
			
			$deliveryTerms = array('price' => $price, 'term' => $time);
		}

		if(!$deliveryTerms['price'])
			$deliveryTerms['price']=0;

		if(!array_key_exists('ERROR',$deliveryTerms)){
			if(COption::GetOptionString(self::$MODULE_ID,'labelDays','N') != 'N'){
				if($deliveryTerms['term'] > 4 && $deliveryTerms['term'] < 21 || $deliveryTerms['term'] == 0)
					$deliveryTerms['term'] .= ' '.GetMessage('IPOLIML_LD_days');
				else{
					$lst = $deliveryTerms['term'] % 10;
					if($lst == 1)
						$deliveryTerms['term'] .= ' '.GetMessage('IPOLIML_LD_day');
					elseif($lst < 5)
						$deliveryTerms['term'] .= ' '.GetMessage('IPOLIML_LD_daya');
					else
						$deliveryTerms['term'] .= ' '.GetMessage('IPOLIML_LD_days');
				}
				if(COption::GetOptionString(self::$MODULE_ID,'labelDays','N') == 'A')
					$deliveryTerms['term'] = GetMessage('IPOLIML_LD_term').": ".$deliveryTerms['term'];
			}

			self::$date = date('d.m.Y',$dT);

			if(
				!self::$nalPayChosen ||
				self::$profiles[$profile]
			)
				$arReturn = array(
					"RESULT"  => "OK",
					"VALUE"   => $deliveryTerms['price'],
					"TRANSIT" => (string)$deliveryTerms['term']
				);
			else
				$arReturn = array(
					"RESULT" => "ERROR",
					"TEXT"   => GetMessage("IPOLIML_DELIV_ERROR_NONAL"),
				);
		}else
			$arReturn = array(
				"RESULT" => "ERROR",
				"TEXT"   => $deliveryTerms['ERROR'],
			);

		foreach(GetModuleEvents(self::$MODULE_ID, "onCalculate", true) as $arEvent){
			ExecuteModuleEventEx($arEvent,Array(&$arReturn,$profile,$arConfig,$arOrder));
		}

		self::$price[$profile] = $arReturn['VALUE'];
		return $arReturn;
	}

	function isNative($to,$from){
		$return = true;
		if($to != $from){
			if(
				method_exists('CSaleLocation','isLocationProEnabled') &&
				CSaleLocation::isLocationProEnabled()
			){
				if(strlen($from) == 10)
					$from = CSaleLocation::getLocationIDbyCODE($from);
				$fromCity = Bitrix\Sale\Location\LocationTable::getList(array('filter'=>array('=ID'=>$from)))->fetch();
				if($fromCity['TYPE_ID'] == 7 && $fromCity['PARENT_ID'])
					$fromCity = Bitrix\Sale\Location\LocationTable::getList(array('filter'=>array('=ID'=>$fromCity['PARENT_ID'])))->fetch();
				
				if(strlen($to) == 10)
					$to = CSaleLocation::getLocationIDbyCODE($to);
				$toCity = Bitrix\Sale\Location\LocationTable::getList(array('filter'=>array('=ID'=>$to)))->fetch();
				if($toCity['TYPE_ID'] == 7 && $toCity['PARENT_ID'])
					$toCity = Bitrix\Sale\Location\LocationTable::getList(array('filter'=>array('=ID'=>$toCity['PARENT_ID'])))->fetch();

				if($fromCity['ID'] != $toCity['ID'])
					$return = false;
			}
			else
				$return = false;
		}
		return $return;
	}

	// расчет стоимости доставки
	function getTablePrice($profile,$arConfig,$arOrder){
		$region = 'native';
		if(!self::isNative($arOrder['LOCATION_TO'],$arOrder['LOCATION_FROM']))
			$region = 'other';
		if(is_numeric($arConfig[$profile.'_free_'.$region]['VALUE']) && $arOrder['PRICE'] >= $arConfig[$profile.'_free_'.$region]['VALUE'])
			$price = 0;
		else
			$price = (is_numeric($arConfig[$profile.'_price_'.$region]['VALUE']))?$arConfig[$profile.'_price_'.$region]['VALUE']:$arConfig[$profile.'_price_'.$region]['DEFAULT'];
		return $price;
	}

	static $serviceData = false;
	
	function formService($arOrder = array()){
		if(!self::$serviceData)
			self::$serviceData = array();
		self::$serviceData['LOCATION_TO'] = $arOrder['LOCATION_TO'];
		self::$serviceData['WEIGHT']      = round($arOrder['WEIGHT'] / 1000);
	}

	function getServerPrice(){
		if(!self::$serviceData)
			return array('ERROR' => 'No data for counting.');

		if(!array_key_exists('SERVICE',self::$serviceData) && !array_key_exists('PROFILE',self::$serviceData))
			return array('ERROR' => 'No service or profile to count delivery price.');

		$accurate = true;

		if(!array_key_exists('SERVICE',self::$serviceData) || !self::$serviceData['SERVICE']){
			$ps = self::checkPS();
			if(count($ps)>1)
				$accurate = false;
			$val = array_pop($ps);
			switch($val."%".self::$serviceData['PROFILE']){
				case 'nal%courier'  : $service = '24KO'; break;
				case 'nal%pickup'   : $service = 'C24KO'; break;
				case 'bnal%courier' : $service = '24'; break;
				case 'bnal%pickup'  : $service = 'C24'; break;
			}
		}else{
			$service = self::$serviceData['SERVICE'];
			$val = (strpos($service,'KO')) ? 'nal' : 'bnal';
		}
		self::$psChecks = $val;

		if(!$service)
			return array('ERROR' => 'No service or profile to count delivery price.');

		$regionTo = (self::$city) ? self::$city : self::getIMLCity(self::$serviceData['LOCATION_TO'],true);
		$regionTo = self::toUpper($regionTo);

		$content =array(
			'Job'        => $service,
			'RegionFrom' => imldriver::adequateRegion(self::toUpper(COption::GetOptionString(self::$MODULE_ID,'departure',false))),
			'RegionTo'   => imldriver::adequateRegion($regionTo,true),
			'Volume'     => 1,
			'Weigth'     => (self::$serviceData['WEIGHT']) ? self::$serviceData['WEIGHT'] : COption::GetOptionString(self::$MODULE_ID,'defaultWeight',1)
		);

		if(strpos($service,'C24')===0){
			if(!array_key_exists('PVZ',self::$serviceData) || !self::$serviceData['PVZ'] || self::$serviceData['PVZ'] == 'false'){
				$oId = false;
				switch(true){
					case array_key_exists('ID',$_REQUEST)       : $oId = $_REQUEST['ID']; break;
					case array_key_exists('id',$_REQUEST)       : $oId = $_REQUEST['id']; break;
					case array_key_exists('order_id',$_REQUEST) : $oId = $_REQUEST['order_id']; break;
					case (array_key_exists('action',$_REQUEST) && $_REQUEST['action'] == 'changeDeliveryService') : 
						$oId = $_REQUEST['formData']['order_id']; 
						break;
				}
				$predict = self::getChosenPVZ($oId,$regionTo);
				switch($predict['RESULT']){
					case 'APROX' : $accurate = false;
					case 'OK'	 : self::$serviceData['PVZ'] = $predict['VALUE']; break;
					case 'ERROR' : return $predict;
				}
			}
			$content['SpecialCode'] = self::$serviceData['PVZ'];
		}
		
		// ReceiveDate
		$ReceiveDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
		
		$arDelays = array(
			"delay" => unserialize(COption::GetOptionString(self::$MODULE_ID,'addHold','a:0:{}')),
			"commonDelay" => intval(COption::GetOptionString(self::$MODULE_ID,'commonHold',''))
		);
		
		if(!self::checkToday()){
			$ReceiveDate += 86400;
		}
		if($arDelays["commonDelay"]){
			$ReceiveDate += $arDelays["commonDelay"] * 86400;
		}
		if(array_key_exists($regionTo,$arDelays["delay"])){
			$ReceiveDate += $arDelays["delay"][$regionTo] * 86400;
		}
		$content['ReceiveDate'] = date('Y-m-d\TH:i',$ReceiveDate);

		$obCache = new CPHPCache();
		if($obCache->InitCache(86400,"IPOLIML|".serialize($content),"/IPOLIML/") && !defined("IPOLIML_NOCACHE")){
			$code = 200;
			$result = $obCache->GetVars();	
		}else{
			// $ch = curl_init("http://api.iml.ru/Json/GetPrice");
			$ch = curl_init("http://api.iml.ru/v5/GetPrice");
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(self::zajsonit($content)));
			curl_setopt($ch, CURLOPT_USERPWD, COption::GetOptionString(self::$MODULE_ID,'logIml').":".COption::GetOptionString(self::$MODULE_ID,'pasIml'));
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($code == 200){
				$result = json_decode($response, true); // результат запроса
				if(!array_key_exists('Code',$result)){
					$obCache->StartDataCache();
					$obCache->EndDataCache($result);
				}
			}
		}

		if($code != 200)
			return array('ERROR' => GetMessage('IPOLIML_DELIV_ERROR_SERVERDOWN')." ($code)",'CASE' => 'DEADSERV');
		elseif(array_key_exists('Code',$result))
			return array('ERROR' => self::zaDEjsonit($result['Mess']." (".$result['Code'].")"));
		else
			return array('price'=>$result['Price'],'term' => $result['DeliveryDate']);
	}

	function getChosenPVZ($oId=false,$regionTo=false){
		cmodule::includeModule('sale');
		$chosen = false;
		$propVal = false;
		$arList = CDeliveryIML::getListFile();
		if(array_key_exists($regionTo,$arList['SelfDelivery'])){
			if($oId){
				$order = CSaleOrder::GetById($oId);
				$personType = $order['PERSON_TYPE_ID'];
			}else
				$personType = $_REQUEST['PERSON_TYPE'];
			$prop = COption::GetOptionString(self::$MODULE_ID,'pvzPicker','ADDRESS');

			if($prop){
				if($oId){
					$propVal = CSaleOrderPropsValue::GetList(array(),array('ORDER_ID'=>$oId,'CODE'=>$prop))->Fetch();
					if($propVal)
						$propVal = $propVal['VALUE'];
				}else{
					$prop = CSaleOrderProps::GetList(array(),array('CODE'=>$prop,'PERSON_TYPE'=>$personType))->Fetch();
					if(
						array_key_exists('ORDER_PROP_'.$prop['ID'],$_REQUEST) &&
						$_REQUEST['ORDER_PROP_'.$prop['ID']]						
					)
						$propVal = $_REQUEST['ORDER_PROP_'.$prop['ID']];
					elseif(
						array_key_exists('order',$_REQUEST) &&
						array_key_exists('ORDER_PROP_'.$prop['ID'],$_REQUEST['order']) &&
						$_REQUEST['order']['ORDER_PROP_'.$prop['ID']]
					)
						$propVal = $_REQUEST['order']['ORDER_PROP_'.$prop['ID']];
				}
				if($propVal && strpos($propVal,'#L')){
					$propVal = trim(substr($propVal,strpos($propVal,'#L')+2));
					if(array_key_exists($propVal,$arList['SelfDelivery'][$regionTo]))
						$chosen = $propVal;
				}
			}
			if($chosen)
				return array('RESULT' => 'OK','VALUE'=>$chosen);
			else{
				ksort($arList['SelfDelivery'][$regionTo]);
				$chosen = array_keys($arList['SelfDelivery'][$regionTo]);
				return array('RESULT' => 'APROX','VALUE'=>$chosen[0]);
			}
		}else
			return array('RESULT' => 'ERROR', 'ERROR' => GetMessage('IPOLIML_DELIV_ERROR_NOPVZINREG'));
	}
	
	// проверки на возможность оплаты
	function checkNalD2P(&$arResult,$arUserResult,$arParams){
		if(
			$arParams['DELIVERY_TO_PAYSYSTEM'] == 'd2p' && 
			strpos($arUserResult['DELIVERY_ID'],'iml:')!==false &&
			COption::GetOptionString(self::$MODULE_ID,"hideNal","Y") == 'Y'
		){
			$arBesnalPaySys = unserialize(COption::GetOptionString(self::$MODULE_ID,'paySystems','a:{}'));
			if(!self::$profiles[substr($arUserResult['DELIVERY_ID'],4)])
				foreach($arResult['PAY_SYSTEM'] as $id => $payDescr)
					if(!in_array($payDescr['ID'],$arBesnalPaySys))
						unset($arResult['PAY_SYSTEM'][$id]);
		}
	}
	function checkNalP2D(&$arResult,$arUserResult,$arParams){
		if(
			$arParams['DELIVERY_TO_PAYSYSTEM'] == 'p2d' && 
			COption::GetOptionString(self::$MODULE_ID,"hideNal","Y") == 'Y'
		){
			$arBesnalPaySys = unserialize(COption::GetOptionString(self::$MODULE_ID,'paySystems','a:{}'));
			if(!in_array($arUserResult['PAY_SYSTEM_ID'],$arBesnalPaySys))
				self::$nalPayChosen = true;
		}
	}
	// END проверки на возможность оплаты

	function checkPS($CPS=false){ // проверка платежных систем: если безнал - делаем запрос безналом
		if(!$CPS){
			if(array_key_exists('PAY_SYSTEM_ID',$_REQUEST)){
				$CPS = $_REQUEST['PAY_SYSTEM_ID'];
			} elseif(array_key_exists('order',$_REQUEST) && is_array($_REQUEST['order']) && array_key_exists('PAY_SYSTEM_ID',$_REQUEST['order'])){
				$CPS = $_REQUEST['order']['PAY_SYSTEM_ID'];
			} else{
				$CPS = false;
			}
		}

		$bNalPSyS = unserialize(COption::GetOptionString(self::$MODULE_ID,'paySystems','a:{}'));

		if($CPS)
			$arRet[] = (in_array($CPS,$bNalPSyS)) ? 'bnal' : 'nal';
		else
			$arRet = array('bnal','nal');

		return $arRet;
	}

	//сервисные
	static $city = '';
	static $selDeliv = '';

	function pickupLoader($arResult,$arUR,$arParams=array()){//подключаем файл с ПВЗ
		if(!self::isActive()) return;
		self::$orderWeight = $arResult['ORDER_WEIGHT'];
		self::$orderPrice  = $arResult['ORDER_PRICE'];

		$city = CSaleLocation::GetByID($arUR['DELIVERY_LOCATION']);
		if($city){
			$city = $city['CITY_NAME'];
			self::$city = $city;
		}
		self::$selDeliv = $arUR['DELIVERY_ID'];
		if(!is_array($arParams))
			$arParams = array();
		if($_REQUEST['is_ajax_post'] != 'Y' && $_REQUEST["AJAX_CALL"] != 'Y' && !$_REQUEST["ORDER_AJAX"]){
			if(defined('BX_YMAP_SCRIPT_LOADED') || defined('IPOL_YMAPS_LOADED'))
				$arParams['NOMAPS'] = 'Y';
			elseif(!array_key_exists('NOMAPS',$arParams) || $arParams['NOMAPS'] != 'Y')
				define('IPOL_YMAPS_LOADED',true);
			$GLOBALS['APPLICATION']->IncludeComponent("ipol:ipol.imlPickup", "order", array_merge($arParams,array("LOAD_ACTUAL_PVZ"=>'Y')),false);
		}
	}

	function defineProfiles($city=false){
		if(!self::$city)
			self::$city = $city;
		if(!self::$orderPrice)
			self::$orderPrice = 1000;
		if(!self::$orderWeight)
			self::$orderWeight = 1000;
		$servises = self::getReference('service');
		$regions  = self::getReference('region');
		$PVZ      = self::getReference('PVZ');
		$blockedServices = self::getCityRestricts(self::$city);
		$killedServices  = unserialize(COption::GetOptionString(self::$MODULE_ID,'blockedServices','a:{}'));
		$arProfServises = array();
		$sW = round(self::$orderWeight/1000);
		$arAllowed = array();
		$arPrepared = array('courier'=>false,'pickup'=>false);
		if(array_key_exists(self::toUpper(self::$city),$regions)){
			$arPrepared['courier'] = true;
			if(array_key_exists(self::toUpper(self::$city),$PVZ))
				$arPrepared['pickup'] = true;
		}
		foreach($servises as $sCode => $descr){
			if(
				in_array($sCode,$blockedServices) ||
				array_key_exists($sCode,$killedServices) ||
				$sW < $descr['WeightMIN'] ||
				$sW > $descr['WeightMAX'] ||
				self::$orderPrice  < $descr['ValuatedAmountMIN'] ||
				self::$orderPrice  > $descr['ValuatedAmountMAX'] ||
				$sCode == GetMessage('IPOLIML_POST')
			)
				continue;
			$mode = 'courier';
			if(strpos(trim($sCode),GetMessage('IPOLIML_S')) === 0)
				$mode = 'pickup';
			if($arPrepared[$mode]){
				if(!array_key_exists($mode,$arProfServises))
					$arProfServises[$mode] = false;
				if($descr['AmountMAX'] > 0)
					$arProfServises[$mode] = true;
			}
			$arAllowed[]=$sCode;
		}
		self::$profiles = $arProfServises;
	}

	function reCheckProfiles(){
		if(strpos(self::$city,GetMessage('IPOLIML_LANG_YO_S')) !== false){
			self::$city = str_replace(GetMessage('IPOLIML_LANG_YO_S'),GetMessage('IPOLIML_LANG_E_S'),self::$city);
			self::defineProfiles();
		}
	}
	
	function checkCity($city,$showProfs=false){
		if(!self::$orderWeight)
			self::$orderWeight = 1000;
		if(!self::$orderPrice)
			self::$orderPrice = 0;
		self::defineProfiles($city);
		if(!count(self::$profiles))
			return false;
		elseif($showProfs)
			return self::$profiles;
		else
			return true;
	}

	function getCityRestricts($city=false){
		$restricts = self::getReference('exceptionSR');
		if(
			// COption::GetOptionString(self::$MODULE_ID,'turnOffRestrictsOS','N') == 'N' &&
			array_key_exists(self::toUpper($city),$restricts)
		)
			return $restricts[self::toUpper($city)];
		else
			return array();
	}

	function countDelivTime($city,$orDate=false){
		$obCache = new CPHPCache();
		if($obCache->InitCache(86400,"IPOLIML|$city|".date("d.m.Y")."|".$orDate,"/IPOLIML/") && !defined("IPOLIML_NOCACHE"))
			$arDelivs = $obCache->GetVars();
		else{
			if(
				!file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/city.json') ||
				!file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/holidays.json')
			)
				return false;

			if(is_numeric($city)){
				$city = CSaleLocation::GetByID($city);
				$city = $city['CITY_NAME_LANG'];
			}
			$city = self::toUpper($city);

			if(!$orDate)
				$orDate = mktime();

			$cityAr=self::zaDEjsonit(json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/city.json'),true));
			$holidays=self::zaDEjsonit(json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/holidays.json'),true));

			$depCity=COption::GetOptionString(self::$MODULE_ID,'departure',GetMessage('IPOLIML_FRNT_MOSCOWCAPITAL'));
			if(!$depCity)
				return false;

			$mas=array_merge(array('time' => $cityAr[$depCity]),$holidays);
			$mas['settings'] = array(
				"time"  => COption::GetOptionString(self::$MODULE_ID,'timeSend','18').':00',
				"delay" => unserialize(COption::GetOptionString(self::$MODULE_ID,'addHold','a:0:{}')),
				"commonDelay" => intval(COption::GetOptionString(self::$MODULE_ID,'commonHold',''))
			);

			if (!array_key_exists($city,$mas["time"]))
				$mas["time"][$city]=1;

			$dayOfDelive = mktime(0,0,0,date('m',$orDate),date('d',$orDate),date('Y',$orDate));// дата доставки

		//рассчитываем "субботы" и "воскресенья"
			$holydays=array();

			$startDate = strtotime(date('Y-m-d').", 0:00")-(24*3600*(date('N')-6));
			$one_day = 24*3600;
			$days_to_plus = 7*24*3600;
			for($i=1;$i<=5;$i++){
				if(is_array($mas['deSat']) && !in_array(date('d.m.Y',$startDate),$mas['deSat']))//не заносим запретные
					$holydays['sat'][] = $startDate;
				if(is_array($mas['deSun']) && !in_array(date('d.m.Y',$startDate+$one_day),$mas['deSun'])){//не заносим запретные
					$holydays['sat'][] = $startDate+$one_day;
					$holydays['sun'][] = $startDate+$one_day;
				}
				$startDate += $days_to_plus;
			}
			//добавленные праздники (действующие по правилам суббот и воскресений)
			if(array_key_exists('days',$mas) && is_array($mas['days']))
			{
				if(array_key_exists('sat',$mas['days']) && is_array($mas['days']['sat']) && count($mas['days']['sat'])){
					foreach($mas['days']['sat'] as $datstr)
						$holydays['sat'][]=strtotime($datstr);
				}
				if(array_key_exists('sun',$mas['days']) && is_array($mas['days']['sun']) && count($mas['days']['sun'])){
					foreach($mas['days']['sun'] as $datstr){
						$holydays['sat'][]=strtotime($datstr);
						$holydays['sun'][]=strtotime($datstr);
					}
				}
			}
	// echo "Старт ".date("d.m.Y D",$dayOfDelive)."<br>";
		//Дата отправки заказа

			$arDelivs = array($dayOfDelive);

			//если не отправляем сегодня - когда отправляем?
			$dayOfDelive+=$one_day;
			While(1){
				if(in_array($dayOfDelive, $holydays['sat']))//проверяем праздник ли это
					$dayOfDelive+=$one_day;//надо смотреть следущий день
				else
					break;
			}
			
			$arDelivs[] = $dayOfDelive;
			if(in_array($dayOfDelive,$holydays['sat']))
				$dayOfDelive[0] = $dayOfDelive[1];
			
			
			foreach($arDelivs as $key => $dayOfDelive){
				$startDeliv = $dayOfDelive;
		// echo "Отправка ".date("d.m.Y D",$dayOfDelive)."<br>";
			//Дата доезда заказа
				$dayOfDelive+=$mas["time"][$city]*$one_day;

				// воскресенья
				foreach($holydays['sun'] as $day)
					if($startDeliv < $day && $dayOfDelive > $day)
						$dayOfDelive+=$one_day;
				if (array_key_exists($city,$mas["settings"]["delay"]))  //если есть еще сдвиг [дополнительный сдвиг]
					$dayOfDelive+=$mas["settings"]["delay"][$city]*$one_day;
				// если есть общий сдвиг
				if($mas['settings']["commonDelay"] > 0)
					$dayOfDelive+=$mas['settings']["commonDelay"]*$one_day;
		// echo "Доезд ".date("d.m.Y D",$dayOfDelive)."<br>";
			//будет ли заказ принят сегодня?
				While(1){
					if(in_array($dayOfDelive, $holydays['sun'])) 
						$dayOfDelive+=$one_day;
					else
						break;
				}
				$arDelivs[$key] = $dayOfDelive;
		// echo "Итог ".date("d.m.Y D",$dayOfDelive)."<br>";
			}
			$obCache->StartDataCache();
			$obCache->EndDataCache($arDelivs);
		}
		// return $dayOfDelive; //mkTime-енный
		return $arDelivs; //mkTime-енный
	}

	function checkToday($orDate = false){ // можем ли отправить заявку сегодня до время_когда_заявки_отосланы
		$time = COption::GetOptionString(self::$MODULE_ID,'timeSend','18').':00:00';
		if($time==':00:00') 
			$time='18:00:00'; 
		$timeToSend['H']=date('H', strtotime($time));
		$timeToSend['i']=date('i', strtotime($time));

		if(!$orDate)
			$orDate = mktime();
		//по времени отправить нельзя 
		return (mktime($timeToSend['H'],$timeToSend['i']) > mktime(date('H',$orDate),date('i',$orDate)));
	}

	// Событие вызывается в самом конце перед отправкой HTML в браузер для передачи города и выбранной доставки
	function onBufferContent(&$content) {
		if(self::$city && self::isActive()){
			$noJson = self::no_json($content);
			if(($_REQUEST['is_ajax_post'] == 'Y' || $_REQUEST["AJAX_CALL"] == 'Y' || $_REQUEST["ORDER_AJAX"]) && $noJson){
				$content .= '<input type="hidden" id="iml_city"   name="iml_city"   value=\''.self::$city.'\' />';//вписываем город
				$content .= '<input type="hidden" id="iml_dostav"   name="iml_dostav"   value=\''.self::$selDeliv.'\' />';//вписываем выбранный вариант доставки
				$content .= '<input type="hidden" id="iml_checkPS"   name="iml_checkPS"   value=\''.self::$psChecks.'\' />';//вписываем тип платежной системы
			}elseif(($_REQUEST['soa-action'] == 'refreshOrderAjax' || $_REQUEST['action'] == 'refreshOrderAjax') && !$noJson)
				$content = substr($content,0,strlen($content)-1).',"iml":{"city":"'.self::zajsonit(self::$city).'","dostav":"'.self::$selDeliv.'","checkPS":"'.self::$psChecks.'"}}';
		}
	}
	function no_json($wat){
		return is_null(json_decode(self::zajsonit($wat),true));
	}

	function getDeliveryId($profile,$sep=":"){
		$profiles = array();
		if(self::isConverted()){
			$dTS = Bitrix\Sale\Delivery\Services\Table::getList(array(
				 'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 'filter' => array('CODE' => 'iml:'.$profile)
			));
			while($dPS = $dTS->Fetch())
				$profiles[]=$dPS['ID'];
		}else
			$profiles = array('iml'.$sep.'pickup');
		return $profiles;
	}

	function getIMLCity($id,$noSetup = false){
		$cityId = self::getNormalCity($id);
		$cityId = ($cityId) ? $cityId : $id;
		$city = CSaleLocation::GetByID($cityId);//не вернет город, если нет его
		if($city)
			$city = $city['CITY_NAME'];
		if($noSetup)
			return $city;
		else
			self::$city = $city;
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
												расчет стороннего заказа
		== setOrder ==  == countDelivery ==  == cntDelivsOld ==  == cntDelivsConverted ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	function countDelivery($arOrder){
		cmodule::includeModule('sale');
		if($arOrder['action']) $arOrder['cityTo'] = self::zaDEjsonit($arOrder['cityTo']);
		$arOrder['cityTo'] = CSaleLocation::getList(array(),array('CITY_NAME'=>$arOrder['cityTo']))->Fetch();
		if($arOrder['cityTo']){
			$_SESSION['IPOLIML_city'] = $arOrder['cityTo']['ID'];
			$arOrder['cityTo'] = $arOrder['cityTo']['ID'];
		}
		$arOrder['cityFrom'] = CSaleLocation::getList(array(),array("CITY_NAME" => COption::getOptionString(self::$MODULE_ID,'departure')))->Fetch();
		if($arOrder['cityFrom'])
			$arOrder['cityFrom'] = $arOrder['cityFrom']['ID'];

		$arProfiles = (self::isConverted()) ? self::cntDelivsConverted($arOrder) : self::cntDelivsOld($arOrder);

		$arReturn = array(
				'courier' => ($arProfiles['courier']['calc']) ? $arProfiles['courier']['calc'] : 'no',
				'pickup'  => ($arProfiles['pickup']['calc'])  ? $arProfiles['pickup']['calc']  : 'no',
				'date'    => CDeliveryIML::$date
			);

		if($arOrder['action'])
			echo json_encode(self::zajsonit($arReturn));
		else
			return $arReturn;
	}

	function cntDelivsOld($arOrder){//Выдает срок и стоимость доставки для виджета
		$pseudoOrder = array(
			"LOCATION_TO"   => $arOrder['cityTo'],
			"LOCATION_FROM" => $arOrder['cityFrom'],
			"PRICE"         => $arOrder['price'],
			"WEIGHT"        => $arOrder['weight']
		);

		$arHandler = CSaleDeliveryHandler::GetBySID('iml')->Fetch();
		$arProfiles = CSaleDeliveryHandler::GetHandlerCompability($pseudoOrder,$arHandler);
		foreach($arProfiles as $profName => $someArray){
			CDeliveryIML::$serviceData['SERVICE'] = ($profName == 'pickup') ? 'C24' : '24';
			if(!array_key_exists('pay',$arOrder) || $arOrder['pay'] != 'bnal')
				CDeliveryIML::$serviceData['SERVICE'] .= 'KO';
			$calc = CSaleDeliveryHandler::CalculateFull('iml',$profName,$pseudoOrder,"RUB");
			if($calc['RESULT'] != 'ERROR')
				$arProfiles[$profName]['calc'] = ($calc['VALUE'])?CCurrencyLang::CurrencyFormat($calc['VALUE'],'RUB',true):GetMessage("IPOLIML_FREEDELIV");	
		}

		return $arProfiles;
	}

	function cntDelivsConverted($arOrder){
		$basket = Bitrix\Sale\Basket::create(SITE_ID);
		$basketItem = Bitrix\Sale\BasketItem::create($basket,self::$MODULE_ID,1);
		$arGood = array(
			"QUANTITY"   => 1,
			"PRICE"      => ($arOrder['price'])  ? $arOrder['price']  : self::$orderPrice,
			"WEIGHT"     => ($arOrder['weight']) ? $arOrder['weight'] : self::$orderWeight,
			"DIMENSIONS" => 'a:3:{s:5:"WIDTH";i:0;s:6:"HEIGHT";i:0;s:6:"LENGTH";i:0;}',
			'DELAY'=>'N','CAN_BUY'=>'Y','CURRENCY'=>'RUB','RESERVED'=>'N','NAME'=>'testGood','SUBSCRIBE'=>'N'
		);
		$basketItem->initFields($arGood);
		$basket->addItem($basketItem);

		$order = Bitrix\Sale\Order::create(SITE_ID);
		$order->setBasket($basket);
		$propertyCollection = $order->getPropertyCollection();
		$locVal = CSaleLocation::getLocationCODEbyID($arOrder['cityTo']);
		$arProps = array();
		foreach($propertyCollection as $property){
			$arProperty = $property->getProperty();
			if($arProperty["TYPE"] == 'LOCATION')
				$arProps[$arProperty["ID"]] = $locVal;
		}
		$propertyCollection->setValuesFromPost(array('PROPERTIES'=>$arProps),array());

		$shipmentCollection = $order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		$shipment->setField('CURRENCY', $order->getCurrency());
		foreach ($order->getBasket() as $item){
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}

		$arShipments = array();
		$arDeliveryServiceAll = Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);
		foreach($arDeliveryServiceAll as $id => $deliveryObj){
			if(
				$deliveryObj->isProfile()  &&
				method_exists($deliveryObj->getParentService(),'getSid') &&
				$deliveryObj->getParentService()->getSid() == 'iml'
			){
				$profName = self::defineDelivery($id);
				$resCalc = Bitrix\Sale\Delivery\Services\Manager::calculateDeliveryPrice($shipment,$id);
				if($resCalc->isSuccess())
					$arShipments[$profName]['calc'] = ($resCalc->getDeliveryPrice()) ? CCurrencyLang::CurrencyFormat($resCalc->getDeliveryPrice(),'RUB',true):GetMessage("IPOLIML_FREEDELIV");
			}
		}

		return $arShipments;
	}

	function cntPVZ($params){
		unset($params['action']);
		$params = self::zaDEjsonit($params);
		$result = self::countDelivery($params);
		echo json_encode(self::zajsonit(array(
			'city'  => $params['cityTo'],
			'pvz'   => $params['pvz'],
			'price' => $result['pickup']
		)));
	}
	
	

	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
												блокировка оформления заказа без ПВЗ
		== noPVZOldTemplate ==  == noPVZNewTemplate ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	function noPVZOldTemplate(&$arResult,&$arUserResult){
		if(
			$arUserResult['CONFIRM_ORDER'] == 'Y' && 
			COption::GetOptionString(self::$MODULE_ID,'noPVZnoOrder','N') == 'Y' &&
			self::defineDelivery($arUserResult['DELIVERY_ID']) == 'pickup' &&
			self::isActive()
		){
			if($propAddr = COption::GetOptionString(self::$MODULE_ID,'pvzPicker','')){
				$checked = 1;
				$props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));
				while($prop=$props->Fetch()){
					if(array_key_exists($prop['ID'],$arUserResult['ORDER_PROP'])){
						if(strpos($arUserResult['ORDER_PROP'][$prop['ID']],'#L') === false && $checked != 2)
							$checked = 0;
						else
							$checked = 2;
					}
				}
				if($checked === 0)
				{
					$arResult['ERROR'] []= GetMessage('IPOLIML_DELIV_ERROR_NOPVZ');
				}
			}
		}
	}
	
	function noPVZNewTemplate($entity,$values){
		if(
            (!defined('ADMIN_SECTION') || ADMIN_SECTION === false) &&
            self::isActive() &&
			COption::GetOptionString(self::$MODULE_ID,'noPVZnoOrder','N') == 'Y' &&
			cmodule::includeModule('sale')
        ) {
			if($propAddr = COption::GetOptionString(self::$MODULE_ID,'pvzPicker','')){
				$props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));
				$arPVZPropsIds = array();
				while($element=$props->Fetch()){
					$arPVZPropsIds []= $element['ID'];
				}
				if(!empty($arPVZPropsIds)){
					$orderProps = $entity->getPropertyCollection()->getArray();
					$checked = 1;
					foreach($orderProps['properties'] as $propVals){
						if(in_array($propVals['ID'],$arPVZPropsIds)){
							if(strpos($propVals['VALUE'][0],'#L') === false && $checked != 2)
								$checked = 0;
							else
								$checked = 2;
						}
					}
					if($checked == 0){
						$shipmentCollection = $entity->getShipmentCollection();
						foreach ($shipmentCollection as $something => $shipment) {
							if ($shipment->isSystem())
								continue;

							$delivery = self::defineDelivery($shipment->getField('DELIVERY_ID'));
							if ($delivery === 'pickup') {
								return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError(GetMessage('IPOLIML_DELIV_ERROR_NOPVZ'), 'code'), 'sale');
							}
						}
					}
				}
            }
		}
	}
}
?>