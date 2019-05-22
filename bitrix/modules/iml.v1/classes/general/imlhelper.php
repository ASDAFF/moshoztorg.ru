<?
class imlHelper{
	static $MODULE_ID    = 'iml.v1';

	public static function getAjaxAction($action,$subAction){
		if(method_exists('imlHelper',$action))
			imlHelper::$action($_POST);
		elseif(method_exists('CDeliveryIML',$action))
			CDeliveryIML::$action($_POST);
		elseif(method_exists('imldriver',$action))
			imldriver::$action($_POST);
		elseif($subAction == 'getBarcode'){
			imldriver::getBarcode($_REQUEST);
		}
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Логи
		== toLog ==  == errorLog == 
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	function toLog($wat,$sign='',$noAction=false){
		if($noAction && array_key_exists('action',$_REQUEST) && ($_REQUEST['action']=='cntDelivs' || $_REQUEST['action']=='countDelivery')) return;
		if($sign) $sign.=" ";
		if(!array_key_exists('IPOLIML_LOGFILE',$GLOBALS) || !$GLOBALS['IPOLIML_LOGFILE']){
			$GLOBALS['IPOLIML_LOGFILE'] = fopen($_SERVER['DOCUMENT_ROOT'].'/IMLog.txt','w');
			fwrite($GLOBALS['IPOLIML_LOGFILE'],"\n\n".date('H:i:s d.m')."\n"); 
		}
		fwrite($GLOBALS['IPOLIML_LOGFILE'],$sign.print_r($wat,true)."\n"); 
	}

	static $ERROR_REF = '';
	function errorLog($error){
		if(!COption::GetOptionString(self::$MODULE_ID,'logged',false))
			return;
		self::$ERROR_REF .= $error."\n";
		$file=fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt","a");
		fwrite($file,"\n".date("d.m.Y H:i:s")." ".$error);
		fclose($file);
	}
	function getErrors(){
		return self::$ERROR_REF;
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Кодировки
		== zajsonit ==  == zaDEjsonit ==  == toUpper == 
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	function zajsonit($handle){
		if(LANG_CHARSET !== 'UTF-8'){
			if(is_array($handle))
				foreach($handle as $key => $val){
					unset($handle[$key]);
					$key=self::zajsonit($key);
					$handle[$key]=self::zajsonit($val);
				}
			else
				$handle=$GLOBALS['APPLICATION']->ConvertCharset($handle,LANG_CHARSET,'UTF-8');
		}
		return $handle;
	}
	function zaDEjsonit($handle){
		if(LANG_CHARSET !== 'UTF-8'){
			if(is_array($handle))
				foreach($handle as $key => $val){
					unset($handle[$key]);
					$key=self::zaDEjsonit($key);
					$handle[$key]=self::zaDEjsonit($val);
				}
			else
				$handle=$GLOBALS['APPLICATION']->ConvertCharset($handle,'UTF-8',LANG_CHARSET);
		}
		return $handle;
	}
	function toUpper($str){
		$str = str_replace( //H8 ANSI
			array(
				GetMessage('IPOLIML_LANG_YO_S'),
				GetMessage('IPOLIML_LANG_CH_S'),
				GetMessage('IPOLIML_LANG_YA_S')
			),
			array(
				GetMessage('IPOLIML_LANG_YO_B'),
				GetMessage('IPOLIML_LANG_CH_B'),
				GetMessage('IPOLIML_LANG_YA_B')
			),
			$str
		);
		if(function_exists('mb_strtoupper'))
			return mb_strtoupper($str,LANG_CHARSET);
		else
			return strtoupper($str);
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Доставки
		== getOrderCity ==   == isActive ==  == getNormalCity ==  == isCityAvail ==  == defineDelivery ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/
	

	//получаем город заказа по его id
	static $optCity = false;
	static $arTmpArLocation=false;
	function getOrderCity($id){
		if(!cmodule::includeModule('sale')) return false;
		if(!self::$optCity){
			$optCity = COption::GetOptionString(imldriver::$MODULE_ID,'city',false);
			if($optCity)
				$optCity=substr($optCity,6,strlen($optCity)-7);
			self::$optCity = $optCity;
		}
		if(!is_array(self::$arTmpArLocation)) self::$arTmpArLocation=array();

		$oCity=CSaleOrderPropsValue::GetList(array(),array('ORDER_ID'=>$id,'CODE'=>self::$optCity))->Fetch();
		if($oCity['VALUE']){
			if(is_numeric($oCity['VALUE'])){
				if(in_array($oCity['VALUE'],self::$arTmpArLocation))
					$oCity=self::$arTmpArLocation[$oCity['VALUE']];
				else{
					$tmpCity=CSaleLocation::GetByID($oCity['VALUE']);
					self::$arTmpArLocation[$oCity['VALUE']]=$tmpCity['CITY_NAME_LANG'];
					$oCity=$tmpCity['CITY_NAME_LANG'];
				}
			}
			else
				$oCity=$oCity['VALUE'];
		}
		else
			$oCity=false;

		return $oCity;
	}

	// Проверка активности СД
	function getDelivery($lid=false){
		if(!cmodule::includeModule("sale")) return false;
		$lid = ($lid && $lid !== 'ru') ? $lid : false;
		if(self::isConverted()){
			$dS = Bitrix\Sale\Delivery\Services\Table::getList(array(
				 'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 'filter' => array('CODE' => 'iml')
			))->Fetch();
		}else{
			$dS = false;
			if($lid){
				$deliveries = CSaleDeliveryHandler::GetBySID('iml');
				while($delivery = $deliveries->Fetch()){
					if($delivery['LID'] == $lid){
						$dS = $delivery;
						break;
					}
				}
			}
			if(!$dS){
				$dS = CSaleDeliveryHandler::GetBySID('iml',$lid)->Fetch();
			}
		}
		return $dS;
	}
	function isActive(){
		$dS = self::getDelivery(SITE_ID);
		return ($dS && $dS['ACTIVE'] == 'Y');
	}
	function defineDelivery($id){ // определяет профиль доставки
		if(self::isConverted() && strpos($id,':') === false){
			$dTS = Bitrix\Sale\Delivery\Services\Table::getList(array(
				 'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 'filter' => array('ID' => $id)
			))->Fetch();
			$delivery = $dTS['CODE'];
		}else
			$delivery = $id;
		$position = strpos($delivery,'iml:');
		return ($position === 0) ? substr($delivery,4) : false;
	}
	// местоположения 2.0, получаем id городa
	function getNormalCity($cityId){
		if(method_exists("CSaleLocation","isLocationProMigrated") && CSaleLocation::isLocationProMigrated() && strlen($cityId) == 10)
			$cityId = CSaleLocation::getLocationIDbyCODE($cityId);
		return $cityId;
	}
	
	// Проверка возможности доставки в город
	function isCityAvail($city,$mode=false){
		$arList = self::getListFile();
		if(is_numeric($city)){
			$city = CSaleLocation::GetByID($city);
			$city = self::toUpper($city['CITY_NAME_LANG']);
		}else
			$city = self::toUpper($city);
		$arAvail = array();
		if(array_key_exists($city,$arList['Region'])){
			$arAvail[]='courier';
			if(array_key_exists($city,$arList['SelfDelivery']))
				$arAvail[]='pickup';
		}
		if($mode)
			$return = (in_array($mode,$arAvail));
		else
			$return = $arAvail;
		return $return;
	}

	function checkAvPVZ($cityTo,&$PVZ){
		if(!$cityTo) return false;

		$cityToTime = false;
		foreach($PVZ as $pvzId => $arPVZ){
			if(($arPVZ['OPEN'] || $arPVZ['CLOSE']) && !$cityToTime){
				$cityToTime = CDeliveryIML::countDelivTime($cityTo);
				$cityToTime = (CDeliveryIML::checkToday()) ? $cityToTime[0] : $cityToTime[1];
			}
			if($arPVZ['OPEN']){
				$open = explode(".",$arPVZ['OPEN']);
				$OT = mktime(0,0,0,$open[1],$open[0],$open[2]);		
				if($OT > $cityToTime)
					unset($PVZ[$pvzId]);
			}
			if($arPVZ['CLOSE']){
				$close = explode(".",$arPVZ['CLOSE']);
				$CT = mktime(0,0,0,$close[1],$close[0],$close[2]);
				if($CT < $cityToTime)
					unset($PVZ[$pvzId]);
			}	
		}
		return (count($PVZ));
	}

	protected function getSaleVersion(){
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/install/version.php');
		return $arModuleVersion['VERSION'];
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Справочники
		== getListFile ==  == getReference ==  == checkRefs ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	// все справочники разом
	function getListFile($noEnc=false){
		$arFiles = array('SelfDelivery'=>'PVZ','Region'=>'region','Service'=>'service','exceptionSR'=>'exceptionSR');
		$arList = array();
		foreach($arFiles as $sign => $name)
			$arList[$sign] = self::getReference($name,$noEnc);
		foreach(GetModuleEvents(self::$MODULE_ID,"onPVZListReady",true) as $arEvent)
			ExecuteModuleEventEx($arEvent,Array(&$arList['SelfDelivery']));
		foreach($arList as $result)
			if(!is_array($result) || !count($result))
				return array();
		return $arList;
	}

	function getReference($wat,$noEnc=false){
		$fPath = $_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/references/".$wat.".json";
		if(!file_exists($fPath)) return array();
		$arList = json_decode(file_get_contents($fPath),true);
		if(method_exists('imlHelper','remake'.$wat))
			$arList = self::remakeservice($arList);
		if(!$noEnc)
			$arList = self::zaDEjsonit($arList);
		return $arList;
	}

	// вкл. НАЛ:  COption::SetOptionString('iml.v1',allowNAL,true);
	function remakeservice($content){
		if(!COption::GetOptionString(self::$MODULE_ID,'allowNAL',false))
			foreach($content as $code => $explane)
				if(strpos(self::zaDEjsonit($code),GetMessage('IPOLIML_JSC_SOD_NAL')) !== false)
					unset($content[$code]);
		return $content;
	}
	
	function checkRefs(){
		return (
			file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/references/PVZ.json") &&
			file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/references/region.json") &&
			file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/references/service.json")
		);
	}

	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Прочие
		== findArDif ==  == findAddrInAr ==  == defineProto ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	// сравнение массивов
	function findArDif($arr1,$arr2){
		if(!$arr1) $arr1=array();
		if(!$arr2) $arr2=array();
		$arNewValues=array();
		$arChangedValues=array();
		$arDeleted=array();
		foreach($arr1 as $code => $val)
		{
			if(is_array($val)){
				if(array_key_exists('ADDRESS',$val)){
					$compar1 = $val['ADDRESS'];
					$compar2 = $arr2[$code]['ADDRESS'];
				}else{
					$compar1 = $val['Description'];
					$compar2 = $arr2[$code]['Description'];					
				}
			}
			else{
				$compar1 = $val;
				$compar2 = $arr2[$code];
			}
				
			if($compar2==$compar1)
				unset($arr2[$code]);
			else
			{
				$isNewCode = self::findAddrInAr($compar1,$arr2);
				if(!$arr2[$code]&&!$isNewCode)
					$arDeleted[$code]=$val;
				elseif(!$arr2[$code]&&$isNewCode)
				{
					$arChangedValues['codes'][$code]=array('key'=>$isNewCode,'val'=>$val);
					unset($arr2[$isNewCode]);
				}
				elseif($arr2[$code]&&!$isNewCode)
				{
					$arChangedValues['values'][$code]=array('oldVal'=>$val,'val'=>$arr2[$code]);
					unset($arr2[$code]);
				}
			}
		}
		return (array('new'=>$arr2,'deleted'=>$arDeleted,'changed'=>$arChangedValues));
	}
	function findAddrInAr($hdl,$ar){
		foreach($ar as $code => $val){
			if(is_array($val)){
				if(array_key_exists('ADDRESS',$val))
					$val=$val['ADDRESS'];
				else
					$val=$val['Description'];
			}
			if($val == $hdl)
				return $code;
		}
		return false;
	}

	function defineProto(){
		return (
			!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||
			$_SERVER['SERVER_PORT'] == 443 ||
			isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
			isset($_SERVER['HTTP_X_HTTPS']) && $_SERVER['HTTP_X_HTTPS'] ||
			isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'https'
		) ? 'https' : 'http';
	}

	//авторизация
	function auth($params){
		if(!$params['login'] || !$params['password'])
			die('No auth data');
		if(!class_exists('imldriver'))
			die('No main class founded');
		if(!function_exists('curl_init'))
			die(GetMessage("IPOLIML_AUTH_NOCURL"));

		COption::SetOptionString(self::$MODULE_ID,'logIml',$params['login']);
		COption::SetOptionString(self::$MODULE_ID,'pasIml',$params['password']);

		$testXML = "<Order>
					<number>TEST</number>
					<action>STATUS</action>
				</Order>
				";
		$check = imldriver::sendRequestOrder($strXML);

		if($check['code'] == 200){
			COption::SetOptionString(self::$MODULE_ID,'logged',true);

			RegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "imldriver", "onEpilog");
			RegisterModuleDependences("main", "OnEndBufferContent", self::$MODULE_ID, "CDeliveryIML", "onBufferContent");
			// RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepOrderProps", self::$MODULE_ID, "CDeliveryIML", "pickupLoader",900);
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", self::$MODULE_ID, "CDeliveryIML", "pickupLoader",900);
			// RegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "CDeliveryIML", "onOEPageLoad"); // editing order
			RegisterModuleDependences("main", "OnAdminListDisplay", self::$MODULE_ID, "imldriver", "displayActPrint");
			RegisterModuleDependences("main", "OnBeforeProlog", self::$MODULE_ID, "imldriver", "OnBeforePrologHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", self::$MODULE_ID, "CDeliveryIML", "checkNalD2P"); // проверка платежных систем
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliveryIML", "checkNalP2D"); // проверка платежных систем

			CAgent::AddAgent("imldriver::agentGetOutbox();",self::$MODULE_ID,"N",1200);//сбор ответов на заявки
			CAgent::AddAgent("imldriver::agentUpdateList();",self::$MODULE_ID);//обновление листов
			
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::$MODULE_ID."/install/delivery/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/", true, true);
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://ipolh.com/webService/iml/authLog.php');
			curl_setopt($ch,CURLOPT_POST, TRUE);
			curl_setopt($ch,CURLOPT_POSTFIELDS, "domen=".$_SERVER['HTTP_HOST']."&login=".$params['login']);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_exec($ch);
			curl_close($ch);
			
			echo "G".GetMessage('IPOLIML_AUTH_YES');
		}
		else{
			COption::SetOptionString(self::$MODULE_ID,'logIml',$params['login']);
			COption::SetOptionString(self::$MODULE_ID,'pasIml',$params['password']);
			if($check['code'] == 404)
				echo 404;
			else
				echo GetMessage('IPOLIML_AUTH_NO')." ".$check['code'];
		}
	}
	function logoff(){
		COption::SetOptionString(self::$MODULE_ID,'logIml','');
		COption::SetOptionString(self::$MODULE_ID,'pasIml','');
		COption::SetOptionString(self::$MODULE_ID,'logged',false);
		CAgent::RemoveModuleAgents(self::$MODULE_ID);
		UnRegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "imldriver", "onEpilog");		
		UnRegisterModuleDependences("main", "OnEndBufferContent", self::$MODULE_ID, "CDeliveryIML", "onBufferContent");
		// UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepOrderProps", self::$MODULE_ID, "CDeliveryIML", "pickupLoader");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", self::$MODULE_ID, "CDeliveryIML", "pickupLoader");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", self::$MODULE_ID, "CDeliveryIML", "checkNalD2P"); // проверка платежных систем
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliveryIML", "checkNalP2D"); // проверка платежных систем
		// UnRegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "CDeliveryIML", "onOEPageLoad");
	}

	function isLogged(){
		return COption::GetOptionString(self::$MODULE_ID,"logged",false);
	}

	function isConverted(){
		return (COption::GetOptionString("main","~sale_converted_15",'N') == 'Y');
	}
	
	function clearCache($noFdb=false){//Очистка кэша
		$obCache = new CPHPCache();
		$obCache->CleanDir('/IPOLIML/');
		if(!$noFdb)
			echo "Y";
	}

	function adequateString($string){
		return str_replace(array("\n","\r"),'',nl2br((string)$string));
	}

	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
														LEGACY
			== cntDelivs ==  == cntPVZ ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	//Выдает срок и стоимость доставки для виджета 
	function cntDelivs($arOrder){
		return CDeliveryIML::countDelivery($arOrder);
	}
	
	function cntPVZ($params){
		CDeliveryIML::cntPVZ($params);
	}
}
?>