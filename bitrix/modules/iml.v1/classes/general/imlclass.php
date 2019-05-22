<?
IncludeModuleLangFile(__FILE__);

class imldriver extends imlHelper{


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Формирование запросных XML и их отсылание
		== sendOrder == -> == orderXMLing == / == getOrderStates == -> == sendRequestOrder == 
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	function sendRequestOrder($XML){ // отсылание XML по заказам
		$login=COption::GetOptionString(self::$MODULE_ID,'logIml','');
		$pass=COption::GetOptionString(self::$MODULE_ID,'pasIml','');
		
		$mesId=(int)COption::GetOptionString(self::$MODULE_ID,'schet',0);
		COption::SetOptionString(self::$MODULE_ID,'schet',++$mesId);
		
		if(coption::getoptionstring(self::$MODULE_ID,'isTest','Y')=='Y')
			$test="<test>1</test>";
		else
			$test="";

		$request="<?xml version='1.0' encoding='UTF-8'?>
		<DeliveryRequest xmlns='http://www.imlogistic.ru/schema/request/v1'>
			<Message>
				<sender>".COption::GetOptionString(self::$MODULE_ID,'logIml','')."</sender>
				<recipient>0001</recipient>
				<issue>".date("Y-m-d\TH:i:s")."</issue>
				<reference>".$mesId."</reference>
				<version>3.0</version>
				".$test."
			</Message>
			".$XML."
		</DeliveryRequest>";

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://api.iml.ru/imlapi');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERPWD, $login.":".$pass);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Content-type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, self::zajsonit($request));
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return array(
			'code'   => $code,
			'result' => $result
		);
	}

	function getOrderStates(){//запрос статусов заказов, вызывается при считывании ответов с сервера (getOutbox)
		if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLIML_ERRLOG_NOSALEOML"));return false;}//без модуля sale делать нечего
		COption::SetOptionString(self::$MODULE_ID,'getOutLst',mktime());

		$arOrders=array();
		$arOrdersToSend = array();
		$strXML='';

		$orders=self::select(array(),array("STATUS" => array("OK","STORE","CORIER","PVZ")));
		while($order=$orders->Fetch()){
			if(strpos($order['MESSAGE'],GetMessage('IPOLIML_SIGN_TESTMODE'))===false)
				$arOrdersToSend[] = $order['ORDER_ID'];
			$arOrders[$order['ORDER_ID']]=$order['ATTEMPT'];
		}
		if(count($arOrdersToSend)){
			$bdOrdersToSend = CSaleOrder::GetList(array(),array("ID"=>$arOrdersToSend),false,false,array("ID","ACCOUNT_NUMBER"));
			while($order=$bdOrdersToSend->Fetch()){
				$on = ($order['ACCOUNT_NUMBER'])?$order['ACCOUNT_NUMBER']:$order['ID'];
				if(COption::GetOptionString(self::$MODULE_ID,"orderIdMode",self::defiDefON()) == "Y")
					$on = str_replace(" ","_",$on);
				if($arOrders[$order['ID']] > 1)
					$on .= "_".$arOrders[$order['ID']];
				$strXML.="<Order>
					<number>".$on."</number>
					<action>STATUS</action>
				</Order>
				";
			}
			if($strXML){	
				$answer = self::sendRequestOrder($strXML);
				if($answer['code']!='200')
					self::errorLog(GetMessage('IPOLIML_ERRLOG_UNBLGETSTATE').implode(",",array_keys($arOrders)).GetMessage('IPOLIML_ERRLOG_UNBLSND_2').$get['code']);
				else
					self::onOrderStatus(simplexml_load_string($answer['result']));
			}
		}
	}

	function orderXMLing($orderId,$action='CREATE'){ // формирование XML заказа
		if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLIML_ERRLOG_NOSALEOML"));return false;}//без модуля sale делать нечего

		$doGetGoods = (COption::GetOptionString(self::$MODULE_ID,'loadGoods','Y') == 'Y' && $action != 'DELETE');
		$arFields   = self::getOrderFields($orderId,$doGetGoods);
		$arFlags    = $arFields['FLAGS'];
		$arFields   = $arFields['FIELDS'];

		if($action == 'DELETE')
			$strXML = "<Order>
				<number>".$arFields['number']."</number>
				<action>".$action."</action>";
		else{
			if($arFlags['ATTEMPT'] > 1)
				$arFields['number'] .= '_'.$arFlags['ATTEMPT'];

			if(array_key_exists('ERROR',$arFlags) && count($arFlags['ERROR'])){
				foreach($arFlags['ERROR'] as $error)
					self::errorLog($error);
				return false;
			}

			$arList = self::getListFile();
			$checkParams = self::checkTrueParams($arFields,$arList);
			if($checkParams!==true){
				if(!self::updateStatus($orderId,"WRONG",serialize($checkParams)))
					self::errorLog(GetMessage("IPOLIML_ERRLOG_CRAP_1").$orderId.GetMessage('IPOLIML_ERRLOG_CRAP_2').print_r($checkParams,true));
				return false;
			}

			$strXML="<Order>
				<number>{$arFields['number']}</number>
				<action>{$action}</action>
				<Condition>
					<service>{$arFields['service']}</service>
					<Delivery>
						<issue>{$arFields['issue']}</issue>".(($arFields['timeFrom']) ? "
						<timeFrom>{$arFields['timeFrom']}</timeFrom>" : "").(($arFields['timeTo'])   ? "
						<timeTo>{$arFields['timeTo']}</timeTo>"       : "")."
					</Delivery>
					<comment>{$arFields['comment']}</comment>
				</Condition>
				<Region>
					<departure>{$arFields['departure']}</departure>
					<destination>{$arFields['destination']}</destination>
				</Region>
				<Consignee>
					<Address>".(($arFields['contentType']) ? "
						<contentType>{$arFields['contentType']}</contentType>" : "")."
						<line>{$arFields['line']}</line>
						<Detail><street/><house/><structure/><apartment/></Detail>
						<city>{$arFields['city']}</city>
						<postCode>{$arFields['postCode']}</postCode>
					</Address>
					<RepresentativePerson>
						<name>".htmlspecialchars($arFields['name'],NULL,LANG_CHARSET)."</name>
						<Communication>
							<telephone1>{$arFields['telephone1']}</telephone1>
							<telephone2>{$arFields['telephone2']}</telephone2>
							<telephone3>{$arFields['telephone3']}</telephone3>
							<email>{$arFields['email']}</email>
						</Communication>
					</RepresentativePerson>
				</Consignee>
				<SelfDelivery>
					<deliveryPoint>{$arFields['deliveryPoint']}</deliveryPoint>
				</SelfDelivery>
				<GoodsMeasure>
					<weight>".((intval($arFields['weight'])) ? intval($arFields['weight']) : '')."</weight>".(($arFields['volume']) ? "
					<volume>{$arFields['volume']}</volume>" : "")."
					".((!$arFlags['isBeznal']) ? "<amount>{$arFields['amount']}</amount>" : "<amount/>")."
					<statisticalValue>{$arFields['statisticalValue']}</statisticalValue>
				</GoodsMeasure>";

			if($doGetGoods){
				$doWeight = intval($arFields['weight']);
				$noRefuse = (COption::GetOptionString(self::$MODULE_ID,'noRefuse','N') == 'Y');
				$strXML .= "
				<GoodsItems>";
					foreach($arFields['GoodItems'] as $good){
						if(!$arFlags['isBeznal'] || !(array_key_exists('delivery',$good) && $good['delivery'])){
							$strXML .= "
					<Item>";
							if(array_key_exists('delivery',$good) && $good['delivery'])
								$strXML .= "
						<productName>{$good['name']}</productName>
						<amountLine>{$good['amount']}</amountLine>
						<deliveryService>1</deliveryService>";
							else{
								$strXML .= "
						<productNo>{$good['productNo']}</productNo>
						<productName>".htmlspecialchars($good['name'],NULL,LANG_CHARSET)."</productName>
						<productVariant/>
						<productBarCode>{$good['barcode']}</productBarCode>
						<couponCode/><discount>0</discount>
						".(($good['weight']) ? "<weightLine>{$good['weight']}</weightLine>" : "<weightLine/>")
						.((!$arFlags['isBeznal']) ? "<amountLine>".($good['amount'] * $good['quantity'])."</amountLine>" : "<amountLine/>")."
						<statisticalValueLine>".($good['statAmount'] * $good['quantity'])."</statisticalValueLine>
						<itemQuantity>".intval($good['quantity'])."</itemQuantity>
						<deliveryService>{$noRefuse}</deliveryService>";
								if(!$arFlags['isBeznal'])
								{
									$vatRate = ($good['vat'] !== false) ? $good['vat'] : $arFields['VATRate'];
									if($vatRate !== false && $vatRate != 'NONDS'){
										$strXML .= "
						<VATRate>".$vatRate."</VATRate>
						<VATAmount>".(self::round2($good['amount']  * $good['quantity'] * $vatRate / (100 + $vatRate)))."</VATAmount>";
									}
								}
							}
							$strXML .= "
					</Item>";
						}
					}
				$strXML .= "
				</GoodsItems>";
			}
		}
		$strXML.="
		</Order>
		";

		return $strXML;
	}
	
	function round2($wat){
		return floor($wat * 100) / 100;
	}

	function sendOrder($orderId){
		$return = false;
		$useOld = (COption::GetOptionString(self::$MODULE_ID,'useOldAPI','N') == 'Y') ? true : false;
		$data   = ($useOld) ? self::orderXMLing($orderId) : self::orderJSONing($orderId);

		if($data){
			$answer = ($useOld) ? self::sendRequestOrder($data) : self::sendRequest4($data);
			if($answer['code'] == '200'){
				if(
					($useOld && $orderInfo = simplexml_load_string($answer['result'])) ||
					(!$useOld && !is_null($orderInfo = $answer['result']))
				){
					return self::onOrderRecieved($orderInfo,$orderId);
				}else{
					self::errorLog(GetMessage('IPOLIML_ERRLOG_UNBLSND_1').$orderId."\n".$answer['result']);
					$return = array('result' => false,'text'=>str_replace(array('\n','"'),array(' ',''),$answer['result']));
				}
			}else{
				self::errorLog(GetMessage('IPOLIML_ERRLOG_UNBLSND_1').$orderId.GetMessage('IPOLIML_ERRLOG_UNBLSND_2').$answer['code']);
				$return = array('result' => false,'text'=>GetMessage('IPOLIML_SOD_BADCONFIRM'));
			}
		}else
			$return = array('result' => false,'text'=>GetMessage('IPOLIML_SIGN_NOTSENDED')."\n".self::getErrors());

		return $return;
	}

	function getOrderFields($orderId,$getGoods = false){
		if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLIML_ERRLOG_NOSALEOML"));return false;}//без модуля sale делать нечего

		$arFields = array();
		$arFlags  = array();

		$oP    = self::GetByOI($orderId);
		$order = csaleorder::GetById($orderId);
		$noOc  = (COption::GetOptionString(self::$MODULE_ID,'noVats','N') === 'Y');
		$spOC  = !$noOc;

		if(
			($oP['OK']==1 && $_REQUEST['action']=='saveAndSend') ||
			in_array($oP['STATUS'],array('DELIVD','OTKAZ','STORE','CORIER','PVZ'))
		)
			$arFlags['ERROR'][] = GetMessage("IPOLIML_FILEIPL_NOUPDTREADY_1").$oP['STATUS'].GetMessage("IPOLIML_FILEIPL_NOUPDTREADY_2").$orderId;
		if(!$order){
			$arFlags['ERROR'][] = GetMessage("IPOLIML_ERRLOG_NOORDER").$orderId;
			$order = array();
		}
		if(!self::checkRefs())
			$arFlags['ERROR'][] = GetMessage("IPOLIML_ERRLOG_NOLIST");

		$arFlags['ATTEMPT'] = (array_key_exists('ATTEMPT',$oP) && $oP['ATTEMPT']) ? $oP['ATTEMPT'] : 1;

		$oP = unserialize($oP['PARAMS']);

		$arList = self::getListFile(); // anyway array

		$arFlags['isBeznal'] = false;
		if(
			(!array_key_exists('AmountMAX',$arList['Service'][$oP['service']])) || 
			(
				array_key_exists('AmountMAX',$arList['Service'][$oP['service']]) &&
				$arList['Service'][$oP['service']]['AmountMAX'] <= 0
			)
		)
			$arFlags['isBeznal'] = true;

		$arChecks = array('timeFrom','timeTo','contentType','comment','issue','postCode','telephone1','telephone2','telephone3','email','deliveryPoint','line','city','complectation');
		foreach($arChecks as $field)
			$arFields[$field] = (array_key_exists($field,$oP)) ? $oP[$field] : false;

		$arSpecChecks = array('service','name');
		foreach($arSpecChecks as $field)
			if(!$oP[$field])
				$arFlags['ERROR'][] = GetMessage('IPOLIML_SIGN_NOTFILLED').GetMessage('IPOLIML_JS_SOD_'.$field);
			else
				$arFields[$field] = $oP[$field];

		// amount
		if(array_key_exists('goodsAmount',$oP)){
			$arFields['amount'] = $oP['goodsAmount'] + $oP['deliveryAmount'];
		} else {
			$arFields['amount'] = $order['PRICE'];
			if(array_key_exists("SUM_PAID",$order))
				$arFields['amount'] = $order['PRICE'] - $order['SUM_PAID'];
		}
		if($arFields['amount'] < 0)
			$arFields['amount'] = 0;

		// statisticalValue
		if($noOc){
			if($oP['service'] == GetMessage('IPOLIML_SIGN_POST'))
				$arFields['statisticalValue'] = 1;
			else
				$arFields['statisticalValue'] = 0;
		}elseif(array_key_exists('StatAmount',$oP)){
			$arFields['statisticalValue'] = $oP['StatAmount'];
		}else{
			$spOC = false; // no need in editting stat value
			$arFields['statisticalValue'] = $order['PRICE'] - $order['PRICE_DELIVERY'];
		}

		$arFields['number']      = ($order['ACCOUNT_NUMBER']) ? $order['ACCOUNT_NUMBER'] : $order['ID'];
		$arFields['volume']      = (intval($oP['places'])) ? intval($oP['places']) : 1;
		$arFields['departure']   = self::adequateRegion((($oP['departure']) ? $oP['departure'] : COption::GetOptionString(self::$MODULE_ID,'departure','')),true);
		$arFields['destination'] = self::adequateRegion($oP['destination'],true);
		$arFields['VATRate'] = $oP['VATRate'];
		$arFields['weight']  = $oP['weight'];

		if($getGoods){
			$arFields['GoodItems'] = array();

			$checkPrice  = $arFields['amount'];
			$checkStatis = $arFields['statisticalValue'];
			
			if(array_key_exists('goods',$oP) && !empty($oP['goods'])){
				foreach($oP['goods'] as $arGood){
					$checkPrice -= $arGood['quantity'] * $arGood['price'];
					
					$arFields['GoodItems'][] = array(
						'productNo'  => $arGood['number'],
						'name'		 => $arGood['name'],
						'barcode'    => $arGood['barcode'],
						'amount'	 => $arGood['price'],
						'statAmount' => ($noOc) ? 0 : $arGood['os'],
						'quantity'   => $arGood['quantity'],
						'weight'     => $arGood['weight'],
						'vat'		 => $arGood['vat']
					);
				}
			} else {
				$arGoods = self::getOrderGoods($orderId);
				
				// WEIGHT
				$cntNoW = 0;
				$weightCheck = $arFields['weight'];
				foreach($arGoods as $arGood){
					if(!$arGood['weight']){
						$cntNoW += $arGood['quantity']; 
					} else {
						$weightCheck -= $arGood['quantity'] * $arGood['weight'];
					}
				}
				if($weightCheck < 0){
					$weightCheck = 0;
				} else {
					$weightCheck = floor(($weightCheck/$cntNoW) * 1000) / 1000;
					if($weightCheck < 0.001){
						$weightCheck = 0;
					}
				}
				
				foreach($arGoods as $arGood){
					// PRICE
					$checkVal = $checkPrice - $arGood['price'] * $arGood['quantity'];
					if($checkVal > 0){
						$price = $arGood['price'];
						$checkPrice = $checkVal;
					}else{
						if($checkPrice / $arGood['quantity'] >= 0.01)
							$price = round(($checkPrice / $arGood['quantity']),2,PHP_ROUND_HALF_DOWN);
						else
							$price = 0;
						$checkPrice -= $price * $arGood['quantity'];
					}
					// STATISTICALVALUE
					if($spOC){
						$checkVal = $checkStatis - $arGood['price'] * $arGood['quantity'];
						if($checkVal > 0){
							$statPrice = $arGood['price'];
							$checkStatis = $checkVal;
						}else{
							if($checkStatis / $arGood['quantity'] >= 0.01)
								$statPrice = round(($checkStatis / $arGood['quantity']),2,PHP_ROUND_HALF_DOWN);
							else
								$statPrice = 0;
							$checkStatis -= $statPrice * $arGood['quantity'];
						}
					}else
						$statPrice = $arGood['price'];
					
					$arFields['GoodItems'][] = array(
						'productNo'  => $arGood['number'],
						'name'		 => $arGood['name'],
						'barcode'    => $arGood['barcode'],
						'amount'	 => $price,
						'statAmount' => ($noOc) ? 0 : $statPrice,
						'quantity'   => $arGood['quantity'],
						'weight'     => ($arGood['weight']) ? $arGood['weight'] : $weightCheck,
						'vat'		 => $arGood['vat']
					);
				}
			}


			if($checkPrice >= 0.01 || $oP['deliveryAmount']){
				if($checkPrice < 0) $checkPrice = 0;
				$arFields['GoodItems'][] = array(
					'name'     => GetMessage('IPOLIML_SIGN_deliv'),
					'amount'   => $oP['deliveryAmount'] + round($checkPrice,2,PHP_ROUND_HALF_DOWN),
					'delivery' => true
				);
			}

			$arFields['GoodItems'] = $arFields['GoodItems'];
		}

		return array('FIELDS' => $arFields, 'FLAGS' => $arFlags);
	}

	function getOrderGoods($orderId){
		$goods      = CSaleBasket::GetList(array(),array('ORDER_ID'=>$orderId));
		$hasIblock  = cmodule::includemodule('iblock');
		$hasCatalog = cmodule::includemodule('catalog');
		$optAticul  = COption::GetOptionString(self::$MODULE_ID,'articul',"ARTNUMBER");
		$optBarcode = COption::GetOptionString(self::$MODULE_ID,'barcode',"");
		
		$arGoods = array();

		if($hasIblock){
			$arSelect = array();
			if($optAticul)
				$arSelect[]='PROPERTY_'.$optAticul;
			if($optBarcode)
				$arSelect[]='PROPERTY_'.$optBarcode;
		}
		if(count($arSelect))
			$arSelect[]='ID';
		else
			$hasIblock = false;
		
		$vatAllowed = ($hasCatalog && COption::GetOptionString(self::$MODULE_ID,'NDSUseCatalog','N') == 'Y');

		while($element = $goods->Fetch()){
			$gd = false;
			if($hasIblock)
				$gd = CIBlockElement::GetList(array(),array('ID'=> $element['PRODUCT_ID'],'LID'=>$element['LID']),false,false,$arSelect)->Fetch();
			
			$vatRate = false;
			if($vatAllowed){
				$arAllowedV = array('0.00','0.10','0.18');
				$cGd = CCatalogProduct::GetByID($element['PRODUCT_ID']);
				if($cGd && $cGd['VAT_ID'] && in_array((string)$element['VAT_RATE'],$arAllowedV))
					$vatRate = floatval($element['VAT_RATE']) * 100;
			}
			
			$arGoods []= array(
				'ID'       => $element['PRODUCT_ID'],
				'number'   => (($gd && $gd["PROPERTY_{$optAticul}_VALUE"]) ? $gd["PROPERTY_{$optAticul}_VALUE"] : $element['PRODUCT_ID']),
				'name'     =>  $element['NAME'],
				'barcode'  => (($gd && $gd["PROPERTY_{$optBarcode}_VALUE"]) ? $gd["PROPERTY_{$optBarcode}_VALUE"]."" : "bark".$element['PRODUCT_ID']),
				'price'    => $element['PRICE'],
				'quantity' => $element['QUANTITY'],
				'weight'   => round($element['WEIGHT'] / 1000),
				'vat'      => $vatRate
			);
		}
		
		return $arGoods;
	}

	// JSON

	function sendRequest4($arData,$where='CreateOrder'){ // отсылание JSON по заказам
		$login=COption::GetOptionString(self::$MODULE_ID,'logIml','');
		$pass=COption::GetOptionString(self::$MODULE_ID,'pasIml','');
		
		$mesId=(int)COption::GetOptionString(self::$MODULE_ID,'schet',0);
		COption::SetOptionString(self::$MODULE_ID,'schet',++$mesId);
		
		if(coption::getoptionstring(self::$MODULE_ID,'isTest','Y')=='Y')
			$arData['Test'] = true;
		$ch = curl_init('http://api.iml.ru/Json/'.$where);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(self::zajsonit($arData)));
		curl_setopt($ch, CURLOPT_USERPWD, $login.":".$pass);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		$code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($code == '200')
			$response = json_decode($response,true);
		curl_close($ch);

		return array(
			'code'   => $code,
			'result' => $response
		);
	}

	function orderJSONing($orderId,$action='CREATE'){ // формирование JSON заказа
		if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLIML_ERRLOG_NOSALEOML"));return false;}//без модуля sale делать нечего

		$doGetGoods = (COption::GetOptionString(self::$MODULE_ID,'loadGoods','Y') == 'Y' && $action != 'DELETE');
		$arFields   = self::getOrderFields($orderId,$doGetGoods);
		$arFlags    = $arFields['FLAGS'];
		$arFields   = $arFields['FIELDS'];

		if($arFlags['ATTEMPT'] > 1)
			$arFields['number'] .= '_'.$arFlags['ATTEMPT'];

		if(array_key_exists('ERROR',$arFlags) && count($arFlags['ERROR'])){
			foreach($arFlags['ERROR'] as $error)
				self::errorLog($error);
			return false;
		}

		$arList = self::getListFile();
		$checkParams = self::checkTrueParams($arFields,$arList);
		if($checkParams!==true){
			if(!self::updateStatus($orderId,"WRONG",serialize($checkParams)))
				self::errorLog(GetMessage("IPOLIML_ERRLOG_CRAP_1").$orderId.GetMessage('IPOLIML_ERRLOG_CRAP_2').print_r($checkParams,true));
			return false;
		}

		$arOrder = array(
			'Job'            => $arFields['service'],
			'CustomerOrder'  => $arFields['number'],
			'DeliveryDate'   => $arFields['issue'],
			'Weight'         => intval($arFields['weight']),
			'Phone'          => $arFields['telephone1'],
			'Email'          => $arFields['email'],
			'Contact'        => $arFields['name'],
			'RegionCodeFrom' => $arFields['departure'],
			'RegionCodeTo'   => $arFields['destination'],
			'Amount'         => ($arFlags['isBeznal']) ? 0 : $arFields['amount'],
			'ValuatedAmount' => $arFields['statisticalValue'],
			'Comment' 		 => $arFields['comment'],
			'City' 			 => $arFields['city'], 
			'Address' 		 => $arFields['line']
		);

		$arExists = array('Volume'=>$arFields['volume'],'TimeFrom'=>$arFields['timeFrom'],'TimeTo'=>$arFields['timeTo'],'PostCode'=>$arFields['postCode'],'DeliveryPoint'=>$arFields['deliveryPoint'],'PostContentType'=>$arFields['contentType']);
		foreach($arExists as $key => $val)
			if($val)
				$arOrder[$key] = $val;
// PostRegion – регион, для отправки почтой России - ???
// PostArea – район, для отправки почтой России

		if($doGetGoods){
			$arOrder['GoodItems'] = array();
			$noRefuse = (COption::GetOptionString(self::$MODULE_ID,'noRefuse','N') == 'Y');
			foreach($arFields['GoodItems'] as $good){
				if(array_key_exists('delivery',$good) && $good['delivery'] && !$arFlags['isBeznal'])
					$arOrder['GoodItems'][]=array(
						'productName'     => $good['name'],
						'amountLine'      => $good['amount'],
						'deliveryService' => true,
						'itemType'		  => 3
					);
				elseif(!array_key_exists('delivery',$good) || !$good['delivery']){
					$arGood = array(
						'productNo'			   => $good['productNo'],
						'productName'		   => htmlspecialchars($good['name'],NULL,LANG_CHARSET),
						'productBarCode'	   => $good['barcode'],
						'amountLine'		   => ($arFlags['isBeznal']) ? 0 : $good['amount'] * $good['quantity'],
						'statisticalValueLine' => $good['statAmount']* $good['quantity'],
						'itemQuantity'		   => $good['quantity'],
						'deliveryService'	   => $noRefuse
					);
					
					if(array_key_exists('weight',$good)){
						$arGood['weightLine'] = $good['weight'];
					}
					
					if(!$arFlags['isBeznal']){
						$vatRate = ($good['vat'] !== false) ? $good['vat'] : $arFields['VATRate'];
						if($vatRate !== false && $vatRate != 'NONDS'){
							$arGood['VATRate']   = $vatRate;
							$arGood['VATAmount'] = (self::round2($good['amount']  * $good['quantity'] * $vatRate / (100 + $vatRate)));
						}
					}
					
					$arOrder['GoodItems'][]= $arGood;
				}
			}
			
			if($arFields['complectation']){
				$arOrder['GoodItems'][]= array(
					'itemType'        => 14,
					'itemQuantity'    => 1,
					'deliveryService' => 1
				);
			}
					
			$arOrder['GoodItems'] = $arOrder['GoodItems'];
		}

		return $arOrder;
	}

	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
		                            Прием ответов от Логистикса
		== onOrderRecieved == == onOrderStatus ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	//ответ на добавление заявки к заказу
	function onOrderRecieved($orderInfo,$orderId=false){
		$arStatus=array();
		$arMessages=array();
		$arBarcodes=array();
		$workLinks = !($orderId);
		if(is_array($orderInfo)){
			$number = ($workLinks) ? self::guessOrderId($orderInfo["Order"]["CustomerOrder"]) : $orderId;
			if($orderInfo['Result'] == 'Error'){
				$arStatus[$number] []= 'ERROR';
				if(!empty($orderInfo['Errors'])){
					$arErrCodesCoor = array('Job' => 'service','CustomerOrder' => 'number','DeliveryDate'=>'issue');
					foreach($orderInfo['Errors'] as $arError){
						if(array_key_exists($arError['Code'],$arErrCodesCoor))
							$arError['Code'] = $arErrCodesCoor[$arError['Code']];
						$arMessages[$number][$arError['Code']] = $arError['Message'];
					}
				}
			}else{
				$arStatus[$number] []= 'OK';
				$arBarcodes[$number]['bc'] = $orderInfo["Order"]["BarCode"];
			}
		}else{
			foreach($orderInfo->Order as $orderAnswer){
				$number = ($workLinks) ? self::guessOrderId((string)$orderAnswer->number) : $orderId;
				if((string)$orderAnswer->status == 'Warning' && (string)$orderAnswer->notPassed == 'barCode') continue;
				$arStatus[$number][]=(string)$orderAnswer->status;
				$mess=(string)$orderAnswer->notPassed;
				if(strlen($mess)>0)
					$arMessages[$number][(string)$orderAnswer->notPassed]=(string)$orderAnswer->description;
				elseif((string)$orderAnswer->status == 'Error' && (string)$orderAnswer->comment)
					$arMessages[$number]['number'] = (string)$orderAnswer->comment;
				if((string)$orderAnswer->status=='OK'){
					$arBarcodes[$number]['bc']=(string)$orderAnswer->BarcodeList->Volume->barcode;
					$arBarcodes[$number]['ebc']=(string)$orderAnswer->BarcodeList->Volume->encodedBarcode;
				}
			}
		}

		if(!count($arStatus)) return; // impossibru

		foreach($arStatus as $orderId =>$statuses){
			$curstat=false;
			foreach($statuses as $status)
				if($curstat!='OK' && $curstat!='ERROR')
					$curstat=strtoupper($status);
			$arStatus[$orderId]=$curstat;
		}

		$statusOk   = COption::GetOptionString(self::$MODULE_ID,'statusOK',false);
		$statusFail = COption::GetOptionString(self::$MODULE_ID,'statusFAIL',false);
		$respond    = array('result' => 'OK');

		foreach($arStatus as $orderId => $status){
			$mes='';
			if($arMessages[$orderId])
				$mes=serialize(self::zaDEjsonit($arMessages[$orderId]));
			if(!self::updateStatus($orderId,$status,$mes,$arBarcodes[$orderId]['bc'],$arBarcodes[$orderId]['ebc'])){
				$mess = GetMessage('IPOLIML_ERRLOG_NOORUPDT_1').$orderId.GetMessage('IPOLIML_ERRLOG_NOORUPDT_2').$status.GetMessage('IPOLIML_ERRLOG_NOORUPDT_3').$mes.GetMessage('IPOLIML_ERRLOG_NOORUPDT_4').$arBarcodes[$orderId]['bc'].GetMessage('IPOLIML_ERRLOG_NOORUPDT_5').$arBarcodes[$orderId]['ebc'];
				self::errorLog($mess);
				$respond = array('result' => 'ERROR', 'text' => $mess);
			}else{
				global $USER;
				if(!is_object($USER))
					$USER = new CUser();
				if($status=='OK'){
					if(COption::GetOptionString(self::$MODULE_ID,'setDeliveryId','Y') == 'Y')
						CSaleOrder::Update($orderId,array('TRACKING_NUMBER'=>$arBarcodes[$orderId]['bc']));
					if($statusOk && !CSaleOrder::StatusOrder($orderId,$statusOk))
						self::errorLog(GetMessage('IPOLIML_ERRLOG_NOORSTUPDT_1').$orderId.GetMessage('IPOLIML_ERRLOG_NOORUPDT_2').$statusOk);
				}elseif($status=='ERROR'){
					if($statusFail && !CSaleOrder::StatusOrder($orderId,$statusFail))
						self::errorLog(GetMessage('IPOLIML_ERRLOG_NOORSTUPDT_1').$orderId.GetMessage('IPOLIML_ERRLOG_NOORUPDT_2').$statusFail);
					$respond = array('result' => 'ERROR_SENDED', 'text' => GetMessage('IPOLIML_SOD_NOTCONFIRMED'));
				}
			}
		}

		return $respond;
	}

	function onOrderStatus($xmlAnswer){ //ответ на запрос статуса заказа
		$arStatus=array();
		foreach($xmlAnswer->Order as $orderAnswer){
			$orderId = self::guessOrderId((string)$orderAnswer->number);
			$oSt = (string)$orderAnswer->orderStatus;
			if((string)$orderAnswer->status == 'Error')
				$arStatus[$orderId] = array("ERROR" => (string)$orderAnswer->description);
			else
				switch($oSt){
					case "1" : $arStatus[$orderId] = "DELIVD"; break;
					case "2" :
					case "3" : $arStatus[$orderId] = "OTKAZ"; break;
					default:
						$dSt = (string)$orderAnswer->deliveryStatus;
						switch($dSt){
							case "1" : $arStatus[$orderId] = "STORE";     break;
							case "2" : $arStatus[$orderId] = "CORIER";   break;
							case "3" : $arStatus[$orderId] = "DELIVD"; break;
							case "6" : $arStatus[$orderId] = "OTKAZ";     break;
							case "10": $arStatus[$orderId] = "PVZ";       break;
						}
						break;
				}
		}

		global $USER;
		if(!is_object($USER))
			$USER = new CUser();

		$arOptionStatuses = array();
		foreach(array("DELIVD","OTKAZ","STORE","CORIER","PVZ") as $status)
			$arOptionStatuses[$status] = COption::GetOptionString(self::$MODULE_ID,'status'.$status,false);
		$arOptionStatuses["ERROR"] = COption::GetOptionString(self::$MODULE_ID,'statusFAIL',false);
		foreach($arStatus as $orderId => $status){
			$curState = self::GetByOI($orderId);
			if(is_array($status) || $curState['STATUS'] != $status){
				$mes='';
				if(is_array($status)){ // ошибка
					$mes=serialize(array("number"=>GetMessage("IPOLIML_JS_SOD_BADUPDATESTATUS").$status["ERROR"]));
					$status = "ERROR";
				}
				if(!self::updateStatus($orderId,$status,$mes))
					self::errorLog(GetMessage('IPOLIML_ERRLOG_NOSTUPDT_1').$orderId.GetMessage('IPOLIML_ERRLOG_NOORUPDT_2').$status);
				else{
					$orderSelf = CSaleOrder::GetByID($orderId);
					if($arOptionStatuses[$status] && $orderSelf['STATUS_ID'] != $arOptionStatuses[$status])
						if(!CSaleOrder::StatusOrder($orderId,$arOptionStatuses[$status]))
							self::errorLog(GetMessage('IPOLIML_ERRLOG_NOORSTUPDT_1').$orderId.GetMessage('IPOLIML_ERRLOG_NOORUPDT_2').$statusOk);
					if(
						$status == "DELIVD" && 
						COption::GetOptionString(self::$MODULE_ID,"markPayed",false) == 'Y' &&
						$orderSelf['PAYED'] != 'Y'
					)
						if(!CSaleOrder::PayOrder($orderId,"Y"))
							self::errorLog(GetMessage('IPOLIML_ERRLOG_CANTMARKPAYED').$orderId.". ");
				}
			}
		}
		return true;
	}

	private static $pretends = false;
	function guessOrderId($link){
		cmodule::includeModule('sale');
		$orderId = false;
		$doChangeON = (COption::GetOptionString(self::$MODULE_ID,"orderIdMode",self::defiDefON()) == "Y")?true:false;

		if(strpos($link,'_') !== false){
			$attempt  = substr($link,strrpos($link,'_')+1);
			if(is_numeric($attempt) && $attempt < 100){
				$orderLink = substr($link,0,strrpos($link,'_'));
				if($doChangeON)
					$orderLink = str_replace("_"," ",$orderLink);

				if(!self::$pretends)
					self::$pretends = self::select(array('ORDER_ID'),array('STATUS'=>array('NEW','OK','STORE','COURIER','PVZ'),'ATTEMPT'=>$attempt));
				while($order=self::$pretends->Fetch()){
					$orderSelf = CSaleOrder::GetByID($order['ORDER_ID']);
					if($orderSelf['ACCOUNT_NUMBER'] == $orderLink){
						$orderId  = $orderSelf['ID'];
						break;
					}elseif($orderSelf['ID'] == $orderLink)
						$possible = $orderSelf['ID'];
				}
				if(!$orderId && $possible)
					$orderId = $possible;

				if(!$orderId){
					$orderSelf = CSaleOrder::GetList(array(),array('ACCOUNT_NUMBER' => $orderLink))->Fetch();
					if($orderSelf)
						$orderId = $orderSelf['ID'];
					else{
						$link = ($doChangeON) ? str_replace("_"," ",$link) : $link;
						$orderSelf = CSaleOrder::GetList(array(),array('ACCOUNT_NUMBER' => $link))->Fetch();
						if($orderSelf)
							$orderId = $orderSelf['ID'];
					}
				}
			}
		}

		if(!$orderId){
			if($doChangeON)
				$link = str_replace("_"," ",$link);
			$order = CSaleOrder::GetList(array(),array("ACCOUNT_NUMBER"=>$link),false,false,array("ID","ACCOUNT_NUMBER"))->Fetch();
			if($order)
				$orderId = $order['ID'];
		}

		return $orderId;
	}

	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
												Манипуляции с информацией о заявках
		== updtOrder ==  == saveAndSend ==  == delReq ==  == delReqOD ==  == killReq ==  == checkTrueParams ==  == killUpdt == == onEpilog == == getCityTimeDeliv ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


	function onEpilog(){//Отображение формы
		if(
			(
				strpos($_SERVER['PHP_SELF'], "/bitrix/admin/sale_order_detail.php")===false && 
				strpos($_SERVER['PHP_SELF'], "/bitrix/admin/sale_order_view.php")===false
			) || 
			!cmodule::includeModule('sale')
		)
			return false;

		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/orderDetail.php");
	}
	
	function getCityTimeDeliv(){ // получение информации о макс/мин времени доставки, субботе, вечернем времени
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/city.json'))
			return false;
		
		$arTime = array();
		
		$cityAr=self::zaDEjsonit(json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/'.self::$MODULE_ID.'/city.json'),true));
		
		foreach($cityAr as $city => $val)
			$arTime[$city] = $val['time'];
		return $arTime;
	}

	//База данных
	public static function Add($Data){return sqlimldriver::Add($Data);} // добавление информации о заявке
	public static function Delete($orderId){return sqlimldriver::Delete($orderId);} // удаление информации о заявке
	public static function CheckRecord($orderId){return sqlimldriver::CheckRecord($orderId);} // проверка наличия заявки для заказа
	public static function GetByOI($orderId){return sqlimldriver::GetByOI($orderId);}  // выбрать заявку по id заказа
	public static function updateStatus($order,$status,$mes='',$barcode='',$encBarcode=''){return sqlimldriver::updateStatus($order,$status,$mes,$barcode,$encBarcode);}  // обновление информации о заявке
	public static function select($arOrder=array("ID","DESC"),$arFilter=array(),$arNavStartParams=array()){return sqlimldriver::select($arOrder,$arFilter,$arNavStartParams);}  // обновление информации о заявке
	
	//Обработка и манипуляции
	function updtOrder($params){ // сохраняем информацию о заявке в БД, возвращаем ее ID
		$params=self::zaDEjsonit($params);
		foreach(array('service','issue','destination','name','telephone1','city','line','postCode') as $need)
			if(!$params[$need]&&(!$params['deliveryPoint']&&!in_array($need,array('city','line','postCode')))){echo GetMessage('IPOLIML_JS_SOD_'.$need)." ".GetMessage('IPOLIML_SOD_NOTGET'); return false;}
		if(!$params['orderId']){echo GetMessage('IPOLIML_SOD_ORDERID')." ".GetMessage('IPOLIML_SOD_NOTGET'); return false;}
		if(!$params['status'])
			$status='NEW';
		
		if(!preg_match('/(\d\d).(\d\d).([\d]+)/',$params['issue'],$matches)){echo GetMessage('IPOLIML_JS_SOD_issue')." ".GetMessage('IPOLIML_SOD_WRONGPRM'); return false;}

		$params['issue']=$matches[3].'-'.$matches[2].'-'.$matches[1];
		
		if(!self::checkRefs()){echo GetMessage("IPOLIML_ERRLOG_NOLIST");return false;}
		
		$arList = self::getListFile();
		$checkParams=self::checkTrueParams($params,$arList);
		if(!$checkParams){
			$strAlert='';
			foreach($checkParams as $code => $message)
				$strAlert.=GetMessage('IPOLIML_JS_SOD_'.$code)." : ".$message;
			echo $strAlert;
			return false;
		}
		
		$orderId = $params['orderId'];
		$attempt = ($params['ATTEMPT']) ? $params['ATTEMPT'] : 1;
		unset($params['orderId']);
		unset($params['action']);
		unset($params['ATTEMPT']);

		if($newId=self::Add(array('ORDER_ID'=>$orderId,'PARAMS'=>serialize($params),'STATUS'=>$status,'ATTEMPT'=>$attempt)))
			return $newId;
		else{
			echo "CANTUPDATE";
			return false;
		}
	}

	function saveAndSend($params){ // кнопка "Сохранить и отправить" в редакторе заказа
		if(self::updtOrder($params)){
			$respond = self::sendOrder($params['orderId']);
			echo json_encode(self::zajsonit(array('result'=>$respond['result'],'text'=>$respond['text'])));
		}
	}

	function rebuildAndSend($params){ // кнопка "Сохранить и отправить" в редакторе заказа
		$orderSelf = self::GetByOI($params['orderId']);
		if($orderSelf){
			$params['ATTEMPT'] = (array_key_exists('ATTEMPT',$orderSelf) && $orderSelf['ATTEMPT']) ? $orderSelf['ATTEMPT']+1 : 2;
			if(self::updtOrder($params)){
				$respond = self::sendOrder($params['orderId']);
				echo json_encode(self::zajsonit(array('result'=>$respond['result'],'text'=>$respond['text'])));
			}
		}else
			echo json_encode(self::zajsonit(array('result'=>'ERROR','text'=>GetMessage('IPOLIML_SOD_CANTFIND'))));
	}

	function delReq($params){ // удаление заявки из БД [в опциях модуля]
		if(self::CheckRecord($params['oid'])){
			if(self::Delete($params['oid']))
				echo "GD:".GetMessage('IPOLIML_SOD_REQDLTD');
			else
				echo "ER:".GetMessage('IPOLIML_SOD_UNBLDLREQ');
		}
		else
			echo "ER:".GetMessage('IPOLIML_SOD_NOREQ_1').$params['oid'].GetMessage('IPOLIML_SOD_NOREQ_2');
	}

	function delReqOD($oid){ // удаляет заявку по id заказа. ХЗ зачем оно надо
		if(self::CheckRecord($oid))
			self::Delete($oid);
	}

	function checkTrueParams($params,$setUps){ // проверяет корректность параметров заявки, пока что - только наличие указанной услуги и местоположения
		$arErrors=array();
		if(!$setUps['Service'][$params['service']])
			$arErrors['service']=$params['service'].GetMessage("IPOLIML_XML_ERROR_NOservice");
		
		if(!$setUps['Region'][$params['destination']])
			$arErrors['destination']=$params['destination'].GetMessage("IPOLIML_XML_ERROR_NORR");
		
		if(count($arErrors)>0)
			return $arErrors;
		return true;
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
													Функции для опций и агентов
		== optiondGetOutbox ==  == agentGetOutbox ==  == callUpdateList ==  == agentUpdateList ==  == killSchet ==  == killUpdt == == tableHandler == == defiDefON ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	//агенты
	function agentGetOutbox(){ // проверка статусов
		self::getOrderStates();
		return "imldriver::agentGetOutbox();";
	}
	function agentUpdateList(){ // вызов обновления списка городов, самовывозов и услуг
		if(!self::updateList())
			self::errorLog(GetMessage('IPOLIML_UPDT_ERR'));
		return 'imldriver::agentUpdateList();';
	}

	//опции
	function optiondGetOutbox(){ // проверяем ответы на заявки из опций (надо вернуть дату последней синхронизации)
		self::getOrderStates();
		echo date("d.m.Y H:i:s");
	}

	function callUpdateList($params){ // запрос на синхронизацию из опций
		if(self::updateList())
			echo GetMessage('IPOLIML_UPDT_DONE').date("d.m.Y H:i:s",filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/references/PVZ.json"));
		else
			echo 'bad'.GetMessage('IPOLIML_UPDT_ERR');
	}

	function killSchet(){ // Сбрасываем счетчик заявок в опциях
		echo COption::SetOptionString(self::$MODULE_ID,'schet',0);
	}

	function killUpdt($wat){ // Убираем информацию об обновлении
		if(unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/hint.txt"))
			echo 'done';
		else
			echo 'fail';
	}

	function tableHandler($params){ // отображение таблицы о заявках
		$arSelect[0]=($params['by'])?$params['by']:'ID';
		$arSelect[1]=($params['sort'])?$params['sort']:'DESC';
		
		$arNavStartParams['iNumPage']=($params['page'])?$params['page']:1;
		$arNavStartParams['nPageSize']=($params['pgCnt']!==false)?$params['pgCnt']:1;
		
		foreach($params as $code => $val)
			if(strpos($code,'F')===0)
				$arFilter[substr($code,1)]=$val;
		
		$requests=self::select($arSelect,$arFilter,$arNavStartParams);
		$strHtml='';
		while($request=$requests->Fetch()){
			$reqParams=unserialize($request['PARAMS']);
			$paramsSrt='';
			
			foreach($reqParams as $parCode => $parVal){
				if($parCode == 'contentType')
					$parVal = GetMessage("IPOLIML_JS_SOD_CT_".$parVal);
				$paramsSrt.=GetMessage("IPOLIML_JS_SOD_$parCode").": ".$parVal."<br>";
			}	

			$message=unserialize($request['MESSAGE']);
			if($message)
				$message=implode('<br>',$message);
			
			$addClass='';
			if($request['STATUS']=='OK')
				$addClass='IPOLIML_TblStOk';
			if($request['STATUS']=='ERROR')
				$addClass='IPOLIML_TblStErr';
			if($request['STATUS']=='SENDED')
				$addClass='IPOLIML_TblStSnd';
			if($request['STATUS']=='REJECT')
				$addClass='IPOLIML_TblStRej';		
			if($request['STATUS']=='DELETE')
				$addClass='IPOLIML_TblStDel';
			if($request['STATUS']=='STORE')
				$addClass='IPOLIML_TblStStr';			
			if($request['STATUS']=='CORIER')
				$addClass='IPOLIML_TblStCor';		
			if($request['STATUS']=='PVZ')
				$addClass='IPOLIML_TblStPVZ';			
			if($request['STATUS']=='OTKAZ')
				$addClass='IPOLIML_TblStOtk';			
			if($request['STATUS']=='DELIVD')
				$addClass='IPOLIML_TblStDvd';
			
			$contMenu='<td class="adm-list-table-cell adm-list-table-popup-block" onclick="BX.adminList.ShowMenu(this.firstChild,[{\'DEFAULT\':true,\'GLOBAL_ICON\':\'adm-menu-edit\',\'DEFAULT\':true,\'TEXT\':\''.GetMessage('IPOLIML_STT_CHNG').'\',\'ONCLICK\':\'BX.adminPanel.Redirect([],\\\'sale_order_detail.php?ID='.$request['ORDER_ID'].'&lang=ru\\\', event);\'}';
			if($request['STATUS']!='SENDED')
				$contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-delete\',\'TEXT\':\''.GetMessage('IPOLIML_JSC_SOD_DELETE').'\',\'ONCLICK\':\'IPOLIML_delSign('.$request['ORDER_ID'].')\'}';
			/*if($request['STATUS']=='OK')
				$contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-delete\',\'TEXT\':\''.GetMessage('IPOLIML_JSC_SOD_DESTROY').'\',\'ONCLICK\':\'IPOLIML_killSign('.$request['ORDER_ID'].')\'}';*/
			$contMenu.='])"><div class="adm-list-table-popup"></div></td>';
				
			$strHtml.='<tr class="adm-list-table-row '.$addClass.'">
							'.$contMenu.'
							<td class="adm-list-table-cell"><div>'.$request['ID'].'</div></td>
							<td class="adm-list-table-cell"><div><a href="/bitrix/admin/sale_order_detail.php?ID='.$request['ORDER_ID'].'&lang=ru" target="_blank">'.$request['ORDER_ID'].'</div></td>
							<td class="adm-list-table-cell"><div>'.$request['STATUS'].'</div></td>
							<td class="adm-list-table-cell"><div><a href="javascript:void(0)" onclick="IPOLIML_shwPrms($(this).siblings(\'div\'))">'.GetMessage('IPOLIML_STT_SHOW').'</a><div style="height:0px; overflow:hidden">'.$paramsSrt.'</div></div></td>
							<td class="adm-list-table-cell"><div>'.$message.'</div></td>
							<td class="adm-list-table-cell"><div>'.$request['BARCODE'].'</div></td>
							<td class="adm-list-table-cell"><div>'.date("d.m.y H:i",$request['UPTIME']).'</div></td>
						</tr>';
		}
		echo json_encode(
			self::zajsonit(
				array(
					'ttl'=>$requests->NavRecordCount,
					'mP'=>$requests->NavPageCount,
					'pC'=>$requests->NavPageSize,
					'cP'=>$requests->NavPageNomer,
					'sA'=>$requests->NavShowAll,
					'html'=>$strHtml
				)
			)
		);
	}
	
	function defiDefON(){
		return (COption::getOptionString("sale","account_number_template") == 'DATE') ? "Y" : "N";
	}


	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
													Функции для печати
		== prntShtr ==  == displayActPrint ==  == OnBeforePrologHandler ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	// печать штрихкода для заказа. Если не задан ID - печатается все после последней печати заказа
	function prntShtr($oId=false){
		if(!$oId['action']&&$oId)
			$arFilter=array('ORDER_ID'=>$oId);
		else
			$arFilter=array('STATUS'=>'OK','>UPTIME'=>COption::GetOptionString(self::$MODULE_ID,'lstShtPr'));

		$requests=self::select(array(),$arFilter);
		$retStr='';
		while($request=$requests->Fetch())
			$retStr.=$request['ORDER_ID'].",";
		echo $retStr;
		if($arFilter['STATUS'])
			COption::SetOptionString(self::$MODULE_ID,'lstShtPr',mktime());
	}

	// действие для печати актов
	function displayActPrint(&$list){
		if (!empty($list->arActions))
			CJSCore::Init(array('ipolIml_printOrderActs'));
		if($GLOBALS['APPLICATION']->GetCurPage() == "/bitrix/admin/sale_order.php")
			$list->arActions['ipolIml_printOrderActs'] = GetMessage("IPOLIML_SIGN_PRNTIML");
	}
	function OnBeforePrologHandler(){ // нажатие на печать актов
		$otherway = (COption::GetOptionString(self::$MODULE_ID,'prntActOrdr','O') == 'A')?true:false; // другой способ печати документов, если true, печатаем только акт
		if(!array_key_exists('action', $_REQUEST) || !array_key_exists('ID', $_REQUEST))
			return;
		if($_REQUEST['action'] == 'ipolIml_printOrderActs') {
			$arTemp = array();
			foreach($_REQUEST["ID"] as $value)
				if(intval($value)>0)
					$arTemp[] = intval($value);
			$sIDs = implode(":", $arTemp);
			?>
			<script type="text/javascript">
				window.open('/bitrix/js/<?=self::$MODULE_ID?>/printActs.php?ORDER_ID=<?=$sIDs?><?if(!$otherway):?>&ORDERS=1<?endif;?>', '_blank');
			</script>
		<?}
	}
	function getBarcode($request){ // получает картинку штрихкода
		$wqe = new imlBarcode($request['barcode']);
	}
	
	function getBK($orders){
		$arResult = array(
			'shopName' => COption::GetOptionString(self::$MODULE_ID,'strName',''),
			'module_id' => self::$MODULE_ID,
		);
		$reqs = self::select(array(),array('ORDER_ID'=>$orders,'!ENCBARCODE'=>false));
		$doOn = (COption::GetOptionString('sale','account_number_template',false));
		cmodule::includeModule('sale');

		while($req=$reqs->Fetch()){
			$pars = unserialize($req['PARAMS']);
			if(preg_match('/(\d\d\d\d)-(\d\d)-(\d\d)/',$pars['issue'],$matches))
				$date = $matches['3'].".".$matches['2'].".".$matches['1'];
			if(!array_key_exists('places',$pars))
				$pars['places'] = 1;

			$OS = imlHelper::getOrderCity($req['ORDER_ID']);

			$oId = $req['ORDER_ID'];
			if($doOn){
				$order = CSaleOrder::GetById($oId);
				$oId   = $order['ACCOUNT_NUMBER'];
			}

			for($i = 1; $i < $pars['places']+1; $i++){
				$arResult['ORDERS'][]=array(
					'orderId' => $oId.(($req['ATTEMPT']>1)?'_'.$req['ATTEMPT']:""),
					'city'    => $OS,
					'date'    => $date,
					'barcode' => self::getPlaceBarcode($i,$req['BARCODE']),
					'enbarcd' => $req['ENCBARCODE'],
					'cnt'     => 1, // magic for acts
					'place'   => $i,
					'ttl'     => $pars['places'],
				);
			}
		}
		return $arResult;
	}
	function printBKs($orders,$template=''){
		if(!is_array($orders) || !array_key_exists('ORDERS',$orders))
			$arBKs = self::getBK($orders);
		else
			$arBKs = $orders;
		if(!$template)
			$template = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::$MODULE_ID.'/bkTemplate.php';
		if(count($arBKs['ORDERS']))
			include $template;
	}
	function getPlaceBarcode($i,$barcode){
		if($i != 1){
			$cmn = substr($barcode,0,strlen($barcode)-2);
			$barcode = $cmn.$i;
			$cmn = 0;
			for($j = 0; $j<12; $j++){
				$ttl = substr($barcode,$j,1);
				if($j % 2)
					$ttl *= 3;
				$cmn += $ttl;
			}
			$cmn = 10-substr($cmn,strlen($cmn)-1);
			if($cmn == 10) $cmn = 0;
			$barcode .= $cmn;
		}
		return $barcode;
	}
	/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
													Синхронизация
		== updateList ==  == getListUpdate ==  == updateSV ==  == updateRegion ==  == updateService ==  == updateExceptionServiceRegion ==  == getFullPVZ ==  == getDeliveTime ==  == getWHsRegions ==
	()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

	static $updateInfo  = '';
	static $updateError = false;

	function getListUpdate($wat){
		$url = "http://list.iml.ru/".$wat."?type=json";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array(
			'code'   => $code,
			'rezult' => json_decode($result,true)
		);
	}

	function updateList(){
		self::getDeliveTime(); // обновляем заодно информацию о сроках доставки
		self::ordersNum();
		self::updateSV();
		self::updateRegion();
		self::updateService();
		self::updateExceptionServiceRegion();
		// self::updateResourceLimit();
		self::getTerms();

		if(
			self::$updateInfo && 
			COption::GetOptionString(self::$MODULE_ID,'logged',false) &&
			is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::$MODULE_ID.'/references/')
		)
			file_put_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/hint.txt","<br><br><strong>".date('d.m.Y H:i:s')."</strong><br>".self::$updateInfo,FILE_APPEND);

		return !self::$updateError;
	}

	function updateSV(){
		$respond=self::getListUpdate('sd');
		if($respond['code']=='200'){
			$arList=self::getReference("PVZ",true);
			$fullInfo = self::getFullPVZ();
			foreach($respond['rezult'] as $val){
				foreach($val as $key => $str){
					$val[$key] = str_replace("'",'"',self::adequateString($str));
				}

				$val['RegionCode'] = self::adequateRegion($val['RegionCode']);
				$closed = false;
				if($val['ClosingDate']){ // убираем закрывшиеся ПВЗ
					$dateXml = $val['ClosingDate'];
					preg_match('/([\d]+)-([\d]+)-([\d]+)/',$dateXml,$matches);
					if(mktime(0,0,0,$matches[2],$matches[3],$matches[1]) < mktime(0,0,0,date('m'),date('d'),date('y')))
						continue;
					else
						$closed = $matches[3].".".$matches[2].".".$matches[1];
				}
				$notOpen = false;
				if($val['OpeningDate']){ // убираем еще не открывшиеся ПВЗ
					$dateXml = $val['OpeningDate'];
					preg_match('/([\d]+)-([\d]+)-([\d]+)/',$dateXml,$matches);
					if(mktime(0,0,0,$matches[2],$matches[3],$matches[1]) > mktime(0,0,0,date('m'),date('d'),date('y')))
						$notOpen = $matches[3].".".$matches[2].".".$matches[1];
				}

				if($val['Name'] && strpos($val['Address'],$val['Name'].", ") !== 0)
					$val['Address'] = $val['Name'].", ".$val['Address'];
				$val['Address'] = str_replace(array("\n","\r"),'',nl2br((string)$val['Address']));
				$arNew[$val['RegionCode']][$val['RequestCode']]['ADDRESS']=$val['Address'];

				if($val['Latitude'] && $val['Longitude'])
					$arNew[$val['RegionCode']][$val['RequestCode']]['COORDS'] = array(
						"Latitude"  => $val['Latitude'],
						"Longitude" => $val['Longitude'],
					);
				
				if($val['HowToGet']){
					$arNew[$val['RegionCode']][$val['RequestCode']]['WAY'] = str_replace('&#x0D;','<br>',$val['HowToGet']);
				}
 
				if($val['WorkMode'])
					$arNew[$val['RegionCode']][$val['RequestCode']]['TIME'] = $val['WorkMode'];
				if($val['Phone'])
					$arNew[$val['RegionCode']][$val['RequestCode']]['PHONE'] = $val['Phone'];

				if($fullInfo[$val['RegionCode']] && $fullInfo[$val['RegionCode']][$val['Code']])//грузим полную инфу либо с сервера, либо из старых данных (мало ли, сервер недоступен)
					$fi = $fullInfo[$val['RegionCode']][$val['Code']];
				else
					$fi = $arList[$val['RegionCode']][$val['RequestCode']];

				if(is_array($fi))
					foreach($fi as $key => $value){
						if($key == 'PATH' && strpos($value,'http')!==0)
							$value = "http://ipolh.com/webService/iml/pics/".$value;
						if(!array_key_exists($key,$arNew[$val['RegionCode']][$val['RequestCode']]) || !$arNew[$val['RegionCode']][$val['RequestCode']][$key])
							$arNew[$val['RegionCode']][$val['RequestCode']][$key]=$value;
					}

				if($notOpen)
					$arNew[$val['RegionCode']][$val['RequestCode']]['OPEN'] = $notOpen;
				elseif(array_key_exists('OPEN',$arNew[$val['RegionCode']][$val['RequestCode']]))
					unset($arNew[$val['RegionCode']][$val['RequestCode']]['OPEN']);
				if($closed)
					$arNew[$val['RegionCode']][$val['RequestCode']]['CLOSE'] = $closed;
				elseif(array_key_exists('CLOSE',$arNew[$val['RegionCode']][$val['RequestCode']]))
					unset($arNew[$val['RegionCode']][$val['RequestCode']]['CLOSE']);
			}

			$strChngdRegs = '';
			if(is_array($arList))
				foreach($arList as $city =>$val)
					if(!$arNew[$city])
						$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_DLT_CT').self::zaDEjsonit($city);
			foreach($arNew as $region =>$pvs){
				if(!$arList[$region]) //лист - ANSI, arNew - JSON
					$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_ADD_CT').self::zaDEjsonit($region);
				else{
					$changes=self::zaDEjsonit(self::findArDif($arList[$region],$pvs));//тут и далее собираем информацию, где да что изменилось
					if(count($changes['new'])>0||count($changes['deleted'])>0||count($changes['changed'])>0){
						$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_CITY')." ".self::zaDEjsonit($region);
						if(is_array($changes['new']))
							foreach($changes['new'] as $code => $PVZ)
								$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_ADD').$PVZ['ADDRESS'].', '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
						if(is_array($changes['deleted']))
							foreach($changes['deleted'] as $code => $PVZ)
								$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_DLT').' '.$PVZ['ADDRESS'].', '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
						if(is_array($changes['changed']['values']))
							foreach($changes['changed']['values'] as $code => $val)
								$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_DSRC').' '.$val['val']['ADDRESS'].', '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.". ".GetMessage('IPOLIML_UPDT_OLDo')." ".GetMessage('IPOLIML_UPDT_NAME').': "'.$val['oldVal']['ADDRESS'].'".';
						if(is_array($changes['changed']['codes']))
							foreach($changes['changed']['codes'] as $code => $val)
								$strChngdRegs.="<br>".GetMessage('IPOLIML_UPDT_SF_CODE').' "'.$val['val'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$val['key'].". ".GetMessage('IPOLIML_UPDT_OLD')." ".GetMessage('IPOLIML_UPDT_CODE').': "'.$code.'".';
					}
				}
			}
			if($strChngdRegs)
				self::$updateInfo.="<strong>".GetMessage('IPOLIML_UPDT_PREFIX').GetMessage('IPOLIML_UPDT_SelfDelivery')."</strong>".$strChngdRegs;
			self::writeSunk("PVZ",$arNew);
		}else{
			self::$updateInfo.="<br><span style='color:red'>".GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'sd')."</span>";
			self::errorLog(GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'sd'));
			self::$updateError = true;
		}
	}

	function updateRegion(){
		$respond=self::getListUpdate('region');

		if($respond['code']=='200'){
			$arList=self::getReference("region",true);
			//проверяем регион в складах
			$checkWHs = self::getWHsRegions();

			$arNew = array();
			foreach($respond['rezult'] as $val)
				if(in_array(trim($val['Code']),$checkWHs)){
					$code = self::adequateRegion(trim($val['Code']));
					$arNew[$code]=trim($val['Description']);
				}
			$changes=self::zaDEjsonit(self::findArDif($arList,$arNew));
			if(count($changes['new'])>0||count($changes['deleted'])>0||count($changes['changed'])>0){
				self::$updateInfo.="<br><strong>".GetMessage('IPOLIML_UPDT_PREFIX').GetMessage('IPOLIML_UPDT_'.'Region')."</strong>";
				if(is_array($changes['new']))
					foreach($changes['new'] as $code => $sing)
						self::$updateInfo.="<br>".GetMessage('IPOLIML_UPDT_ADDED_Region').' "'.$sing.'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
				if(is_array($changes['deleted']))
					foreach($changes['deleted'] as $code => $sing)
						self::$updateInfo.="<br>".GetMessage('IPOLIML_UPDT_DLTD_Region').' "'.$sing.'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
				if(is_array($changes['changed']['values']))
					foreach($changes['changed']['values'] as $code => $val)
						self::$updateInfo.="<br>".GetMessage('IPOLIML_UPDT_CHNGD_Region').' "'.$val['val'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.". ".GetMessage('IPOLIML_UPDT_OLDo')." ".GetMessage('IPOLIML_UPDT_NAME').': "'.$val['oldVal'].'".';
				if(is_array($changes['changed']['values']))
					foreach($changes['changed']['values'] as $code => $val)
						self::$updateInfo.="<br>".GetMessage('IPOLIML_UPDT_CHNGD_Region').' "'.$val['val'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$val['key'].". ".GetMessage('IPOLIML_UPDT_OLD')." ".GetMessage('IPOLIML_UPDT_CODE').': "'.$code.'".';
			}
			self::writeSunk("region",$arNew);
		}else{
			self::$updateInfo.="<br><span style='color:red'>".GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'region')."</span>";
			self::errorLog(GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'region'));
			self::$updateError = true;
		}
	}

	function updateService(){
		$respond=self::getListUpdate('service');
		if($respond['code']=='200'){
			$arList=self::getReference("service",true);
			$arNew = array();
			foreach($respond['rezult'] as $val)
				if(
					strpos(self::zaDEjsonit($val['Code']),GetMessage('IPOLIML_JSC_SOD_V')) === false &&
					strpos(self::zaDEjsonit($val['Code']),GetMessage('IPOLIML_JSC_SOD_ZABOR')) === false &&
					strpos(self::zaDEjsonit($val['Code']),GetMessage('IPOLIML_JSC_SOD_POSTDIFF')) === false
				)
				$arNew[$val['Code']]=$val;
			//тут везде надо палить кодировки
			$changes=self::zaDEjsonit(self::findArDif($arList,$arNew));
			if(count($changes['new'])>0||count($changes['deleted'])>0||count($changes['changed'])>0){
				$strInfo.="<br><strong>".GetMessage('IPOLIML_UPDT_PREFIX').GetMessage('IPOLIML_UPDT_Service')."</strong>";
				if(is_array($changes['new']))
					foreach($changes['new'] as $code => $val)
						$strInfo.="<br>".GetMessage('IPOLIML_UPDT_ADDED_Service').' "'.$val['Description'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
				if(is_array($changes['deleted']))
					foreach($changes['deleted'] as $code => $val)
						$strInfo.="<br>".GetMessage('IPOLIML_UPDT_DLTD_Service').' "'.$val['Description'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.".";
				if(is_array($changes['changed']['values']))
					foreach($changes['changed']['values'] as $code => $val)
						$strInfo.="<br>".GetMessage('IPOLIML_UPDT_CHNGD_Service').' "'.$val['val']['Description'].'", '.GetMessage('IPOLIML_UPDT_CODE')." ".$code.". ".GetMessage('IPOLIML_UPDT_OLDo')." ".GetMessage('IPOLIML_UPDT_NAME').': "'.$val['oldVal']['Description'].'".';
			}
			self::writeSunk("service",$arNew);
		}else{
			self::$updateInfo.="<br><span style='color:red'>".GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'service')."</span>";
			self::errorLog(GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'service'));
			self::$updateError = true;
		}
	}

	function updateExceptionServiceRegion(){
		$respond=self::getListUpdate('ExceptionServiceRegion');
		if($respond['code']=='200'){
			$arList=self::getReference("exceptionSR",true);
			$arClosed = array();
			$cd = mktime();
			foreach($respond['rezult'] as $restrict){
				$closed = true;
				if(
					array_key_exists('Open',$restrict) &&
					$restrict['Open'] &&
					preg_match('/([\d]+)-([\d]+)-([\d]+)/',$restrict['Open'],$matches) &&
					$cd < mktime(0,0,0,$matches['2'],$matches['3'],$matches['1'])
				)
					$closed = false;
				if(
					array_key_exists('End',$restrict) &&
					$restrict['End'] &&
					preg_match('/([\d]+)-([\d]+)-([\d]+)/',$restrict['End'],$matches) &&
					$cd > mktime(0,0,0,$matches['2'],$matches['3'],$matches['1'])
				)
					$closed = false;
				$restrict['RegionCode'] = self::adequateRegion($restrict['RegionCode']);
				if(!array_key_exists($restrict['RegionCode'],$arClosed))
					$arClosed[$restrict['RegionCode']] = array();
				$arClosed[$restrict['RegionCode']][]=$restrict['JobNo'];
			}
			self::writeSunk("exceptionSR",$arClosed);
		}else{
			self::$updateInfo.="<br><span style='color:red'>".GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'exceptionSR')."</span>";
			self::errorLog(GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'exceptionSR'));
			self::$updateError = true;
		}
	}

	function updateResourceLimit(){
		$respond=self::getListUpdate('ResourceLimit');
		if($respond['code']=='200'){
			$arList=self::getReference("resourseLimit",true);
			foreach($respond['rezult'] as $restrict){
				$arRL[]=$restrict;
			}
			self::writeSunk("resourseLimit",$arRL);
		}else{
			self::$updateInfo.="<br><span style='color:red'>".GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'resourseLimit')."</span>";
			self::errorLog(GetMessage('IPOLIML_FILE_UNBLUPDT').GetMessage('IPOLIML_FILE_'.'resourseLimit'));
			self::$updateError = true;
		}
	}

	public function adequateRegion($wat,$flip=false){
		$arAdequate = array(
			GetMessage('IPOLIML_BADREGION_PP_1') => GetMessage('IPOLIML_BADREGION_PP_2')
		);
		if($flip)
			$arAdequate = array_flip($arAdequate);
		else
			$arAdequate = self::zajsonit($arAdequate); // если delivery - не надо zajsonit
		return (array_key_exists($wat,$arAdequate)) ? $arAdequate[$wat] : $wat;
	}

	function getTerms(){
		$calendar = self::getListUpdate('Calendar');
		if($calendar['code']!='200'){
			self::errorLog(GetMessage('IPOLIML_FILEIPL_NODELIVDATA').$code);
			return false;
		}
		$arCalendar = array();
		foreach($calendar['rezult'] as $arDate){
			if($arDate['Code'] != GetMessage("IPOLIML_SIGN_DELIVERY") || $arDate['RecurringSystem'] != 0)
				continue;
			if(preg_match('/([\d]+)-([\d]+)-([\d]+)/',$arDate['Date'],$matches))
				if(mktime() <  mktime(0,0,0,$matches[2],$matches[3],$matches[1] + 86400)){
					$date = $matches[3].".".$matches[2].".".$matches[1];
					if($arDate['Nonworking'])
						$arCalendar["days"]["sun"][]=$date;
					else
						$arCalendar["deSun"][]=$date;
				}
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/holidays.json",json_encode($arCalendar));
	}

	function writeSunk($wat,$content){
		$dirPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::$MODULE_ID.'/references/';
		if(!is_dir($dirPath))
			mkdir($dirPath);
		file_put_contents($dirPath.$wat.'.json',json_encode($content));
	}

	function getWHsRegions(){
		$WHs = imldriver::getListUpdate('Location');
		$arWHs = array();
		$arTMPWHs = array();
		$cd = array(date("d"),date("m"),date("Y"));

		foreach($WHs['rezult'] as $warhouseDescr){
			$work = true;
			if(
				$warhouseDescr['ClosingDate'] && 
				preg_match('/([\d]+)-([\d]+)-([\d]+)/',$warhouseDescr['ClosingDate'],$matches) &&
				mktime(0,0,0,$matches[2],$matches[3],$matches[1]) <= mktime(0,0,0,$cd[1],$cd[0],$cd[2])
			)
				$work = false;
			if(
				$warhouseDescr['OpeningDate'] && 
				preg_match('/([\d]+)-([\d]+)-([\d]+)/',$warhouseDescr['OpeningDate'],$matches) &&
				mktime(0,0,0,$matches[2],$matches[3],$matches[1]) > mktime(0,0,0,$cd[1],$cd[0],$cd[2])
			)
				$work = false;

			if($work)
				$arWHs[]=$warhouseDescr['RegionCode'];
		}

		return $arWHs;
	}

	function nativeReq($where,$wat = false){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,'http://ipolh.com/webService/iml/'.$where.'.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if($wat){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => json_encode(self::zajsonit($wat))));
		}
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array(
			'result' => $result,
			'code'   => $code
		);
	}

	function getFullPVZ(){ // получаем полное описание PVZ, которое Логистикс никак не удосужится запихнуть в свое API
		$request = self::nativeReq('PVZ');
		if($request['code']!='200'){
			self::errorLog(GetMessage('IPOLIML_FILEIPL_UNBLUPDT').$code);
			return false;
		}
		$arList = json_decode($request['result'],true);

		return $arList;
	}

	function getDeliveTime(){
		$request = imldriver::nativeReq('delivTime/city'); // поддержка старых версий + все равно надо проверять время работы
		if($request['code'] != 200) return;
		$ourCities = json_decode($request['result'],true);

		$request = imldriver::getListUpdate('zone');
		if($request['code'] != 200) return;

		$arRegions = array();
		foreach($request['rezult'] as $region){
			if($region['DayLimit'])
				$dayGo = $region['DayLimit'];
			elseif(array_key_exists($region['FromRegion'],$ourCities) && array_key_exists($region['ToRegion'],$ourCities[$region['FromRegion']]))
				$dayGo = $ourCities[$region['FromRegion']][$region['ToRegion']];
			else
				$dayGo = 1;
			$arRegions[$region['FromRegion']][$region['ToRegion']] = $dayGo;
		}

		foreach($arRegions as $region => $arRegs)
			if(array_key_exists($region,$ourCities))
				$arRegions[$region]['time'] = $ourCities[$region]['time'];
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/city.json",json_encode($arRegions));
	}

	protected function ordersNum(){
		cmodule::includeModule('sale');
		// требование IML по сбору статистики, сколько заявок сделано через модуль
		$lastId = COption::GetOptionString(self::$MODULE_ID,'lastSuncId',0);
		$arOrders = array();
		$bdReqs = sqlimldriver::select(array("ID","ASC"),array(">ID"=>$lastId,"OK"=>true));
		while($arReq=$bdReqs->Fetch()){
			$year  = date("Y",$arReq['UPTIME']);
			if(!array_key_exists($year,$arOrders))
				$arOrders[$year] = array();

			$month = date("m",$arReq['UPTIME']);
			if(array_key_exists($month,$arOrders[$year]))
				$arOrders[$year][$month]['vis'] += 1;
			else
				$arOrders[$year][$month]['vis'] = 1;
			$arOrders[$year][$month]['id'][] = $arReq['ORDER_ID'];
			if($lastId < $arReq['ID'])
				$lastId = $arReq['ID'];
		}
				
		foreach($arOrders as $year => $arYear)
			foreach($arYear as $month => $arMonth){
				$ttlPrice = 0;
				$orders = CSaleOrder::GetList(array(),array('ID'=>$arMonth['id']),false,false,array('ID','PRICE'));
				while($order=$orders->Fetch())
					$ttlPrice += $order['PRICE'];
				$arOrders[$year][$month]['prc'] = round($ttlPrice);
				unset($arOrders[$year][$month]['id']);
			}

		if(count($arOrders)){
			$arResuest = array(
				'reqs' => $arOrders,
				'acc'  => COption::GetOptionString(self::$MODULE_ID,'logIml',''),
				'host' => $_SERVER['SERVER_NAME'],
				'cms'  => 'bitrix'
			);
			$request = self::nativeReq('imlStat',$arResuest);
			if(
				$request['code']=='200' &&
				strpos($request['result'],'good') !== false
			)
				COption::SetOptionString(self::$MODULE_ID,'lastSuncId',$lastId);
		}
	}
}
?>