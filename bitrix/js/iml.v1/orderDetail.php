<?
	$orderinfo=CSaleOrder::getById($_REQUEST['ID']);//параметры заказа		

	if(
		COption::GetOptionString(self::$MODULE_ID,'showInOrders','Y') == 'N' &&
		strpos($orderinfo['DELIVERY_ID'],'iml:') !== 0
	)
		return;
	
	$isLoaded = false;
	if($ordrVals=self::GetByOI($_REQUEST['ID'])){ // получаем параметры заявки из БД, если они есть
		$status=$ordrVals['STATUS'];
		$message=array();
		if($ordrVals['MESSAGE'])
			$message=unserialize($ordrVals['MESSAGE']);
		foreach($message as $key => $sign)
			if(in_array($key,array('service','issue','timeFrom','timeTo','departure','destination','city','line','postCode','deliveryPoint','deliveryPoint','name','telephone1','telephone2','telephone3','email','comment','number','VATRate','VATAmount')))
				$message[$key]='<br><span style="color:#FF4040">'.$sign.'</span>';
			else
				$message['number']='<br><span style="color:#FF4040">'.$sign.'</span>';
		$ordrVals=unserialize($ordrVals['PARAMS']); // массив значений заявки, если не задан - заполняется по умолчанию из параметров, указанных в опциях и покупателем
		$isLoaded = true;
	}
	if(!$ordrVals['VATRate'])
		$ordrVals['VATRate']    = COption::getOptionString(self::$MODULE_ID,'VATRate','NONDS');
	if(!$ordrVals['StatAmount'])
		$ordrVals['StatAmount'] = $orderinfo['PRICE'] - $orderinfo['PRICE_DELIVERY'];

	if(!$status)
		$status='NEW';
	
	//собираем из свойств заказа параметры для заявки
	$arChk=array();
	$PVZaddr = false;//определяем пвз
	$PVZprop = COption::GetOptionString(self::$MODULE_ID,'pvzPicker',false);
	foreach(array('name','city','line','postCode','telephone1','email','comment') as $prop){
		$arDV[$prop]=COption::GetOptionString(self::$MODULE_ID,$prop,'');
		if(preg_match_all('/#([^#]+)#/',$arDV[$prop],$matches))
			foreach($matches[1] as $code)
				if(strpos($code,'PROP_')===0)
					$arChk['props'][substr($code,5)][]=$prop;
				else
					$arChk['params'][$code][]=$prop;
	}

	if($arChk['params']){
		foreach($arChk['params'] as $code => $links)
			foreach($links as $prop)
				if(!$ordrVals[$prop])
					$ordrVals[$prop]=str_replace('#'.$code.'#',$orderinfo[$code],$arDV[$prop]);
	}
	$arTemplates=array();
	if($PVZprop && !array_key_exists($PVZprop,$arChk['props']))//заносим свойство с ПВЗ в выборку
		$arChk['props'][$PVZprop] = array('none');

	if(!$isLoaded && IsModuleInstalled('ipol.kladr')){ // ибо кладр = хорошо
		$propCode = COption::GetOptionString(self::$MODULE_ID,"line",'');
		if(strpos($propCode,"#PROP_")===0){
			$propCode = substr($propCode,6,strlen($propCode)-7);
			if($propCode){
				$propValue = CSaleOrderPropsValue::getList(array(),array('ORDER_ID'=>$_REQUEST['ID'],"CODE"=>$propCode))->Fetch();
				if($propValue['VALUE']){
					$containment = explode(",",$propValue['VALUE']);
					if($containment[0] && is_numeric($containment[0])){ 
						$ordrVals['postCode'] = trim($containment[0]);
						unset($containment[0]);
						unset($containment[1]);
					}else
						unset($containment[0]);
					$ordrVals['line'] = trim(implode(',',$containment)); 
				}
			}
		}
	}
	if($arChk['props']){//свойства заказа
		$orderProps=CSaleOrderPropsValue::getList(array(),array('ORDER_ID'=>$_REQUEST['ID']));
		while($prop=$orderProps->Fetch()){
			if(array_key_exists($prop['CODE'],$arChk['props']))				
				foreach($arChk['props'][$prop['CODE']] as $option){
					$chkLocation=CSaleOrderProps::GetByID($prop['ORDER_PROPS_ID']);
					if($chkLocation['TYPE']=='LOCATION')
					{
						$prop['VALUE']=imlHelper::getNormalCity($prop['VALUE']);
						$prop['VALUE']=CSaleLocation::GetByID($prop['VALUE']);
						$prop['VALUE']=$prop['VALUE']['CITY_NAME'];
					}
					if(!$ordrVals[$option])
					{
						$ordrVals[$option]=str_replace('#PROP_'.$prop['CODE'].'#',$prop['VALUE'],$arDV[$option]);
						$arTemplates[]=$option;
					}
					elseif(in_array($option,$arTemplates))
						$ordrVals[$option]=str_replace('#PROP_'.$prop['CODE'].'#',$prop['VALUE'],$ordrVals[$option]);
				}
			if($orderinfo['DELIVERY_ID']!='iml:courier' && !$PVZaddr && $PVZprop && $prop['CODE'] == $PVZprop){//определяем адрес ПВЗ
				if(strpos($prop['VALUE'],'#L')!==false)
					$PVZaddr = intval(substr($prop['VALUE'],strpos($prop['VALUE'],'#L')+2));
				else
					$PVZprop = false;
			}
		}
	}

	foreach($ordrVals as $opt => $val)//заменяем значения макросов на нужные
		if(in_array($opt,$arTemplates))
			$ordrVals[$opt]=preg_replace('/#[^#]*#/','',$val);

	if($ordrVals['issue']){ // дата доставки
		if(preg_match('/([\d]{4})-([\d]{2})-([\d]{2})/',$ordrVals['issue'],$matches))
			$ordrVals['issue']=$matches[3].".".$matches[2].".".$matches[1];
		else
			$ordrVals['issue']=false;
	}
	
	//собрали свойства
	$arList=self::getListFile();
	$strOfCodes='';
	$arChzn['flag'] = false;//флаг: нашли доставку
	$arChzn = array(
		'flag'  => false,
		'noPay' => unserialize(coption::getoptionstring(self::$MODULE_ID,'paySystems','a:0:{}')),
	);
	$showLists = unserialize(coption::getOptionString(self::$MODULE_ID,'services',GetMessage("IPOLIML_OPTION_DEFSERVICES")));
	$destCode = ($ordrVals['destination']) ? $ordrVals['destination'] : self::toUpper($ordrVals['city']);
	foreach($arList['Service'] as $code => $descr){//услуга
		$sign = (is_array($descr)) ? $descr['Description'] : $descr; // переходный
		if(strpos(self::toUpper($sign),GetMessage('IPOLIML_JS_SOD_VOZVRAT'))===false && strpos(self::toUpper($sign),GetMessage('IPOLIML_JS_SOD_ZABOR'))===false && strpos(self::toUpper($code),GetMessage('IPOLIML_JS_SOD_VOZVRAT'))===false && strpos(self::toUpper($code),GetMessage('IPOLIML_JS_SOD_ZABOR'))===false && !array_key_exists($code,$showLists)){
			$selected = '';
			if(!$arChzn['flag']){
				if($code===$ordrVals['service']){
					$arChzn['flag'] = true;
					$selected='selected';
				}
				if(
					!$ordrVals['service'] && 
					(
						!array_key_exists($destCode,$arList['exceptionSR']) ||
						!in_array($code,$arList['exceptionSR'][$destCode])
					)
				){//если не задана услуга - пытаемся найти наиболее подходящую
					if($PVZaddr && strpos($code,GetMessage('IPOLIML_JSC_SOD_S'))===0){//ПВЗ
						if(
							( in_array($orderinfo['PAY_SYSTEM_ID'],$arChzn['noPay']) && strpos($code,GetMessage('IPOLIML_JSC_SOD_KO'))===false)//безнал
							||
							(!in_array($orderinfo['PAY_SYSTEM_ID'],$arChzn['noPay']) && strpos($code,GetMessage('IPOLIML_JSC_SOD_KO'))!==false)//кассовое обслуживание
						){
							$selected='selected';
							$arChzn['flag'] = true;
						}
					}elseif(!$PVZaddr && strpos($code,GetMessage('IPOLIML_JSC_SOD_S'))===false){//Не ПВЗ
						if(
							( in_array($orderinfo['PAY_SYSTEM_ID'],$arChzn['noPay']) && strpos($code,GetMessage('IPOLIML_JSC_SOD_KO'))===false)//безнал
							||
							(!in_array($orderinfo['PAY_SYSTEM_ID'],$arChzn['noPay']) && strpos($code,GetMessage('IPOLIML_JSC_SOD_KO'))!==false)//кассовое обслуживание
						){
							$selected='selected';
							$arChzn['flag'] = true;
						}
					}
				}
			}
			$strOfCodes.="<option value='$code' $selected>$sign ($code)</option>";
		}
	}

	$regionTo = false;
	foreach($arList['Region'] as $code => $sign){
		if($code==$ordrVals['destination'])
			$regionTo = $code;
		elseif(
			!$ordrVals['destination'] && 
			str_replace(GetMessage('IPOLIML_LANG_YO_B'),GetMessage('IPOLIML_LANG_E_B'),self::toUpper($ordrVals['city'])) == str_replace(GetMessage('IPOLIML_LANG_YO_B'),GetMessage('IPOLIML_LANG_E_B'),self::toUpper($code))
		)
			$regionTo = $code;
		if($regionTo)
			break;
	}
		
	$strOfPSV='';
	$arOCPVZ = array('OPEN' => array(),'CLOSE' => array());
	foreach($arList['SelfDelivery'] as $city => $punkts){
		asort($punkts);
		$strOfPSV.="<select id='IPOLIML_deliveryPoint_".str_replace(" ","_",$city)."' onchange='IPOLIML_oExport.onPVZChange()'>";
		foreach($punkts as $code => $PVZdesrc){
			if($city==$ordrVals['destination']&&$code==$ordrVals['deliveryPoint'])
				$strOfPSV.="<option selected value='".$code."'>".$PVZdesrc['ADDRESS']."</option>";
			elseif(!$ordrVals['deliveryPoint'] && $PVZaddr == $code){
				$strOfPSV.="<option selected value='".$code."'>".$PVZdesrc['ADDRESS']." [".$code."]</option>";
			}else
				$strOfPSV.="<option value='".$code."'>".$PVZdesrc['ADDRESS']." [".$code."]</option>";
			
			foreach($arOCPVZ as $hitKey => $content)
				if($PVZdesrc[$hitKey]){
					if(!array_key_exists($city,$arOCPVZ[$hitKey]))
						$arOCPVZ[$hitKey][$city] = array();
				$arOCPVZ[$hitKey][$city][$code] = $PVZdesrc[$hitKey];
			}
		}
		$strOfPSV.="</select>";
	}

	$jsPVZ = "{";
	foreach($arOCPVZ as $hitKey => $cities){
		$jsPVZ .= "'$hitKey': {";
		foreach($cities as $city => $PVZ){
			$jsPVZ .=  "'$city' : {";
			foreach($PVZ as $code => $date)
				$jsPVZ .= "'$code':'$date',";
			$jsPVZ .=  "},";
		}
		$jsPVZ .= "},";
	}
	$jsPVZ .= "}";

	$jsServices = "{";
	foreach($arList['exceptionSR'] as $city => $services){
		$jsServices .= "'".str_replace(" ","_",$city)."' : {";
			foreach($services as $service)
				$jsServices .= "'$service' : true,";
		$jsServices .= "},";
	}
	$jsServices .= "}";

	$jsPaySys  = "{";
	foreach($arList['Service'] as $id => $service)
		$jsPaySys .= "'".$id."' : ".((array_key_exists('AmountMAX',$service) && $service['AmountMAX'] <= 0) ? 'true' : 'false').",";
	$jsPaySys .= "}";

	$strOfCT='';
	foreach(array(1,2,0) as $code)
		$strOfCT.='<option '.(($ordrVals['contentType'] === $code) ? 'selected ' : '').'value="'.$code.'">'.GetMessage("IPOLIML_JS_SOD_CT_".$code).'</option>';

	$satBut='';
	if($status=='SENDED')
		$satBut="&nbsp;<a href='javascript:void(0)' onclick='IPOLIML_oExport.checkStat();'>".GetMessage('IPOLIML_JSC_SOD_STATCHECK')."</a>";

	$dateOfDelive = CDeliveryIML::countDelivTime(self::toUpper($ordrVals['city']));
	$dateOfDelive = (CDeliveryIML::checkToday()) ? $dateOfDelive[0] : $dateOfDelive[1];
	if(!$ordrVals['issue'])
		$ordrVals['issue'] = date('d.m.Y',$dateOfDelive);
	// оплата
	$toPay = (array_key_exists('SUM_PAID',$orderinfo)) ? ($orderinfo['PRICE'] - $orderinfo['SUM_PAID']) : $orderinfo['PRICE'];
	$payed = ($toPay <= 0 || $orderinfo['PAYED'] == 'Y');
	CJSCore::Init(array("jquery"));

	?>
		<script>
			var IPOLIML_oExport = {
				OrderId : '<?=$_REQUEST['ID']?>',
				Status  : '<?=$status?>',
				MinDate : '<?=$dateOfDelive?>',
				Payed   : <?=($payed)? 'true' : 'false'?>,
				TimeDeliv : <?=json_encode(self::zajsonit(self::getCityTimeDeliv()))?>,
				Restricts : {
					PVZ     : <?=$jsPVZ?>,
					service : <?=$jsServices?>,
					beznal  : <?=$jsPaySys?>
				},
				skipCheckRestricts: <?=(COption::GetOptionString(self::$MODULE_ID,'turnOffRestrictsOS','N')=='Y') ? 'true' : 'false'?>,
				Wnd: false,

				showWindow: function(){
					var butStats = {'save':'','delete':'','unlock':'','print':''};

					if(IPOLIML_oExport.inArray(IPOLIML_oExport.Status,['SENDED','OK','DELIVD','OTKAZ','STORE','CORIER','PVZ']))
						butStats.save    = 'style="display:none"';
					if(IPOLIML_oExport.inArray(IPOLIML_oExport.Status,['SENDED','','NEW']))
						butStats.delete  = 'style="display:none"';
					if(IPOLIML_oExport.Status!='OK')
						butStats.print   = 'style="display:none"';
					if(IPOLIML_oExport.inArray(IPOLIML_oExport.Status,['DELIVD','','NEW']))
						butStats.refresh = 'style="display:none"';

					if(!IPOLIML_oExport.Wnd){
						var html=$('#IPOLIML_wndOrder').parent().html();
						$('#IPOLIML_wndOrder').replaceWith('');
						IPOLIML_oExport.Wnd = new BX.CDialog({
							title: '<?=GetMessage('IPOLIML_JSC_SOD_WNDTITLE')?>',
							content: html,
							icon: 'head-block',
							resizable: true,
							draggable: true,
							height: '500',
							width: '500',
							buttons: [
								'<input type=\"button\" id="IPOLIML_sendBtn" value=\"<?=GetMessage('IPOLIML_JSC_SOD_SAVESEND')?>\"  '+butStats.save+'onclick=\"IPOLIML_oExport.send(\'saveAndSend\')\"/>',
								<?/*'<input type=\"button\" value=\"<?=GetMessage('IPOLIML_JSC_SOD_DELETE')?>\" '+butStats.delete+' onclick=\"IPOLIML_oExport.delete()\"/>',*/?>
								'<input type=\"button\" id="IPOLIML_rebuildBtn" value=\"<?=GetMessage('IPOLIML_JSC_SOD_REBUILD')?>\" '+butStats.print+' onclick=\"IPOLIML_oExport.rebuild()\"/>',
								'<input type=\"button\" value=\"<?=GetMessage('IPOLIML_JSC_SOD_REFRESH')?>\" '+butStats.refresh+' onclick=\"IPOLIML_oExport.checkStat()\"/>',
								'<input type=\"button\" value=\"<?=GetMessage('IPOLIML_JSC_SOD_PRNTSH')?>\" '+butStats.print+' onclick="window.open(\'/bitrix/js/<?=self::$MODULE_ID?>/printBK.php?ORDER_ID='+IPOLIML_oExport.OrderId+'\'); return false;"/>'
							]
						});
					}
					IPOLIML_oExport.onCodeChange($('#IPOLIML_service'));
					IPOLIML_oExport.onRegionChange($('#IPOLIML_destination'));
					IPOLIML_oExport.Wnd.Show();
				},

				onLoad: function(){
					if($('#IPOLIML_bnt').length) return;
						$('.adm-detail-toolbar').find('.adm-detail-toolbar-right').prepend("<a href='javascript:void(0)' onclick='IPOLIML_oExport.showWindow()' class='adm-btn' id='IPOLIML_bnt'><?=GetMessage('IPOLIML_JSC_SOD_BTNAME')?></a>");
						var btn = $('#IPOLIML_bnt');
						switch(IPOLIML_oExport.Status){
							case 'SENDED' : btn.css('color','#CEA912'); break;
							case 'NEW'    : break;
							case 'ERROR'  :
							case 'REJECT' : btn.css('color','#F13939'); break;
							default       : btn.css('color','#3A9640'); break;
						}
				},

				// REQUESTS
				getInputs: function(){//заполняем инпуты из окна
					var dO={};
					var reqFields=['service','issue','destination','name','telephone1'];
					var chznreg=$('#IPOLIML_destination').val();

					if($('#IPOLIML_service').val() == null)
						return $('#IPOLIML_service').closest('tr').children('td').html();

					if($('#IPOLIML_service').val().indexOf('<?=GetMessage('IPOLIML_JSC_SOD_S')?>')!==-1)
						reqFields.push('deliveryPoint_'+chznreg);
					else{
						reqFields.push('city');
						reqFields.push('line');
					}

					if(IPOLIML_oExport.isPost()){
						reqFields.push("contentType");
						reqFields.push("postCode");
					}
					for(var i in reqFields){
						if(typeof(reqFields[i]) != 'string')continue;
						var index=reqFields[i];
						if(reqFields[i].indexOf('deliveryPoint')===0){
							index='deliveryPoint';
							if(reqFields[i].indexOf(' ')!==-1)
								reqFields[i]=reqFields[i].replace(' ','_');
						}
						dO[index]=$('#IPOLIML_'+reqFields[i]).val();
						if(!dO[index]){
							if(index=='deliveryPoint')
								return $('[id^="IPOLIML_deliveryPoint"]').closest('tr').children('td').html();
							else
								return $('#IPOLIML_'+reqFields[i]).closest('tr').children('td').html();
						}
					}
					reqFields=['timeFrom','timeTo','email','telephone2','telephone3','comment','departure','VATRate','StatAmount','complectation'];
					if(!IPOLIML_oExport.isPost())
						reqFields.push("postCode");
					if($('#IPOLIML_places').val() > 1)
						reqFields.push('places');
					for(var i in reqFields){
						if(typeof(reqFields[i]) != 'string')continue;
						var hndl=$('#IPOLIML_'+reqFields[i]).val();
						if(hndl)
							dO[reqFields[i]]=hndl;
					}

					return dO;
				},

				send: function(mode){//кнопочка "сохранить"
					if(typeof(mode) == 'undefined')
						mode = 'saveAndSend';
					$('#IPOLIML_sendBtn').css("visibility","hidden");
					var dataObject=IPOLIML_oExport.getInputs();
					if(typeof dataObject != 'object'){
						alert('<?=GetMessage('IPOLIML_JSC_SOD_ZAPOLNI')?> "'+dataObject.trim()+'"');
						$('#IPOLIML_sendBtn').css("visibility","visible");
						return;
					}
					dataObject['action']=mode;
					dataObject['orderId']=IPOLIML_oExport.OrderId;
					IPOLIML_oExport.ajax({
						data: dataObject,
						dataType: 'json',
						success: IPOLIML_oExport.onSendAnswer,
						error: function(a,b,c){alert(b+" "+c);}
					});
				},

				delete: function(){
					if(confirm('<?=GetMessage('IPOLIML_JSC_SOD_IFDELETE')?>'))
						IPOLIML_oExport.ajax({
							data: {action:'delReq',oid:IPOLIML_oExport.OrderId},
							success: function(data){
								alert(data.substr(3));
								if(data.indexOf('GD')!==-1)
									document.location.reload();
							}
						});
				},

				checkStat: function(){
					$('[onclick="IPOLIML_oExport.checkStat()"]').css('visibility','hidden');
					IPOLIML_oExport.ajax({
						data: {'action':'optiondGetOutbox'},
						success: function(data){
							document.location.reload();
						}
					});
				},

				rebuild: function(){
					if(confirm('<?=GetMessage('IPOLIML_JSC_SOD_IFREBUILD')?>')){
						$('#IPOLIML_rebuildBtn').css("visibility","hidden");
						var dataObject=IPOLIML_oExport.getInputs();
						if(typeof dataObject != 'object'){
							alert('<?=GetMessage('IPOLIML_JSC_SOD_ZAPOLNI')?> "'+dataObject+'"');
							$('#IPOLIML_sendBtn').css("visibility","visible");
							return;
						}
						dataObject['action']='rebuildAndSend';
						dataObject['orderId']=IPOLIML_oExport.OrderId;
						IPOLIML_oExport.ajax({
							data: dataObject,
							dataType: 'json',
							success: IPOLIML_oExport.onSendAnswer,
							error: function(a,b,c){alert(b+" "+c);}
						});
					}
				},

				onSendAnswer: function(data){
					var btnHandler = (IPOLIML_oExport.Status == 'OK') ? 'IPOLIML_rebuildBtn' : 'IPOLIML_sendBtn';
					$('#'+btnHandler).css('display','none');
					if(data.result == 'OK'){
						alert('<?=GetMessage('IPOLIML_SOD_CONFIRMED')?>');
						IPOLIML_oExport.Wnd.Close();
					}else{
						$('#'+btnHandler).css('display','');
						if(typeof(data.text) != 'undefined')
							alert(data.text);
						else
							alert('<?=GetMessage('IPOLIML_SOD_UNKNOWN')?>');
						if(data.result == 'ERROR_SENDED')
							window.location.reload();
					}
				},

				// EVENTS
				onCodeChange: function(wat){//изменилась услуга: проверяем, не самовывоз ли, скрываем/показываем соответствующие поля доставки
					IPOLIML_oExport.cleanServiceSigns();
					//самовывоз
					if(wat.val() !== null && wat.val().indexOf('<?=GetMessage('IPOLIML_JSC_SOD_S')?>')!==-1){
						$('#IPOLIML_wndOrder').find('.IPOLIML_notSV').css('display','none');
						$('#IPOLIML_timeFrom').closest('tr').css('display','none');
						$('#IPOLIML_wndOrder').find('.IPOLIML_SV').css('display','');
						IPOLIML_oExport.onPVZChange();
					}else{
						$('#IPOLIML_wndOrder').find('.IPOLIML_notSV').css('display','');
						$('#IPOLIML_timeFrom').closest('tr').css('display','');
						$('#IPOLIML_wndOrder').find('.IPOLIML_SV').css('display','none');
					}

					if(IPOLIML_oExport.isPost(wat))
						$('#IPOLIML_contentTypeCT').css('display','');
					else
						$('#IPOLIML_contentTypeCT').css('display','none');

					if(!IPOLIML_oExport.isPost(wat) && wat.val() !== null && typeof(IPOLIML_oExport.Restricts.beznal[wat.val()]) != 'undefined'){
						if(IPOLIML_oExport.Restricts.beznal[wat.val()] && !IPOLIML_oExport.Payed)
							$('#IPOLIML_notPayedBNal').css('display','block');
						if(!IPOLIML_oExport.Restricts.beznal[wat.val()] && IPOLIML_oExport.Payed)
							$('#IPOLIML_payedNal').css('display','block');
					}
				},

				onDataSelect: function(isReboot){
					var regTime = IPOLIML_oExport.TimeDeliv[$('#IPOLIML_destination').val()];
					
					if(regTime == false || regTime === null || typeof(regTime) == 'undefined')
						regTime = {
							minTime: 11, 
							maxTime: 15,
							subTime: 15,
						};

					$('#IPOLIML_nightTimeWarning').css('display','none');

					var date=$('#IPOLIML_issue').val();
					if(isReboot === true && $('#IPOLIML_timeFrom').val())
						date+=" "+$('#IPOLIML_timeFrom').val();

					var isNoDelivery = false;
					var errorDate = false;
					
					var pregDate=/(\d\d)\.(\d\d)\.([\d]{4})/;
					if(date.indexOf(':')!==-1)
						pregDate=/(\d\d)\.(\d\d)\.([\d]{4}) ([\d]{1,2}):/;
					var formDate = pregDate.exec(date);

					var curDay = new Date(formDate[3],parseInt(formDate[2])-1,formDate[1]);
						curDay = curDay.getDay();

					switch(curDay){
						case 0:
							var cityTo = $('#IPOLIML_departure').val();
							if(cityTo != '<?=self::toUpper(GetMessage('IPOLIML_CITY_MSK'))?>' && cityTo != '<?=self::toUpper(GetMessage('IPOLIML_CITY_SPB'))?>'){
								alert('<?=GetMessage('IPOLIML_JSC_SOD_NODELONSUNDAY')?>');
								isNoDelivery=true;
								break;
							}
						case 6: 
							if(typeof regTime.subTime == 'undefined' || parseInt(regTime.subTime)===0 || isNaN(parseInt(regTime.subTime))){
								alert('<?=GetMessage('IPOLIML_JSC_SOD_NODELONSATURDAY')?>');isNoDelivery=true;
							}
						break;
					}

					$('#IPOLIML_timeFrom').val('');
					$('#IPOLIML_timeTo').val('');

					if(isNoDelivery) // нет доставки
						$('#IPOLIML_issue').val('');
					else{
						if(IPOLIML_oExport.MinDate){ // минимальная дата
							var mkDate = new Date(formDate[3],parseInt(formDate[2])-1,formDate[1]);
							if(IPOLIML_oExport.MinDate>(mkDate.valueOf()/1000))
								$('#IPOLIML_timeWarning').css('display','');
							else
								$('#IPOLIML_timeWarning').css('display','none');
						}
						if(date.indexOf(':')!==-1){ // указано время
							$('#IPOLIML_issue').val(date.substr(0,date.indexOf(' ')));
							
							if(formDate[4].indexOf('0')===0)
								formDate[4]=formDate[4].substr(1);
							formDate[4]=parseInt(formDate[4]);

							if(formDate[4]>=regTime.minTime&&formDate[4]<=regTime.maxTime){ // все ок со временем
								if(curDay == 6 && formDate[4]>regTime.subTime){ // максимум доставка в субботу
									alert('<?=GetMessage('IPOLIML_JSC_SOD_MAXHOURSATURDAY')?>'+regTime.subTime);
									errorDate=true;
								}
							}
							else{
								if(typeof regTime.nightTime != 'undefined' && regTime.nightTime && formDate[4]>=regTime.nightTime && formDate[4] < 22)
									$('#IPOLIML_nightTimeWarning').css('display','inline');
								else{
									alert('<?=GetMessage('IPOLIML_JSC_SOD_BADINTERVAL')?>');
									errorDate=true;
								}
							}

							if(!errorDate){
								$('#IPOLIML_timeFrom').val(formDate[4]+":00");
								$('#IPOLIML_timeTo').val((formDate[4]+3)+":00");
							}
							else{
								if(isReboot !== true){
									$('#IPOLIML_popupDate').trigger('click');
								}
							}
						}
					}
				},

				//изменяется регион: показываем в пункте самовывоза нужный пункт
				onRegionChange: function(wat){
					IPOLIML_oExport.cleanSigns()
					var chznR=wat.val();
					$('#IPOLIML_city').val($(wat).find('option[value="'+chznR+'"]').html());
					while(chznR.indexOf(" ")!==-1)
						chznR=chznR.substr(0,chznR.indexOf(" "))+"_"+chznR.substr(chznR.indexOf(" ")+1);

					var fndd=false;//чтобы был только один селект у первого
					$('[id^="IPOLIML_deliveryPoint_"]').each(function(){
						if($(this).attr('id')=='IPOLIML_deliveryPoint_'+chznR)
							$(this).css('display','');
						else
							$(this).css('display','none');
					});
					if($('#IPOLIML_deliveryPoint_'+chznR).length==0)
						$('#IPOLIML_deliveryPoint_noSV').css('display','');
					else
						$('#IPOLIML_deliveryPoint_noSV').css('display','none');

					if(typeof(IPOLIML_oExport.Restricts.service[chznR]) != 'undefined')
						IPOLIML_oExport.parseServices(IPOLIML_oExport.Restricts.service[chznR]);
					else
						IPOLIML_oExport.parseServices();

					IPOLIML_oExport.onDataSelect(true);
				},

				// изменился ПВЗ: проверяем на закрытие/открытие
				onPVZChange: function(){
					IPOLIML_oExport.cleanSigns();
					var dest = $('#IPOLIML_destination').val();
					var pvz = $("#IPOLIML_deliveryPoint_"+dest).val();
					for(var i in IPOLIML_oExport.Restricts.PVZ){ // OPEN / CLOSE
						for(var j in IPOLIML_oExport.Restricts.PVZ[i])
							if(j == dest){
								for(var k in IPOLIML_oExport.Restricts.PVZ[i][j])
									if(k == pvz){
										$('#IPOLIML_PVZ_'+i).css('display','inline');
										$('#IPOLIML_PVZ_DATEOC').css('display','inline');
										$('#IPOLIML_PVZ_DATEOC').html(IPOLIML_oExport.Restricts.PVZ[i][j][k]);
									}
							}
					}
				},

				// ACTIONS
				parseServices: function(servises){
					if(typeof(servises) !== 'object')
						servises = {};
					$('#IPOLIML_service').find('option').each(function(){
						if(!IPOLIML_oExport.skipCheckRestricts && typeof(servises[$(this).val()]) != 'undefined')
							$(this).attr('disabled','disabled');
						else
							$(this).removeAttr('disabled');
					});
				},

				addPhone: function(nmbr){
					var btn=$("[onclick='IPOLIML_oExport.addPhone("+nmbr+")']");
					btn.parent().append("<br><input id='IPOLIML_telephone"+nmbr+"' type='text'>");
					if(nmbr==3){
						btn.css('visibility','hidden');
						btn.attr('onclick','');
					}
					else
						btn.attr('onclick',"IPOLIML_oExport.addPhone("+(nmbr+1)+")");
				},

				cleanSigns: function(){
					$('#IPOLIML_PVZ_OPEN').css('display','none');
					$('#IPOLIML_PVZ_CLOSE').css('display','none');
					$('#IPOLIML_PVZ_DATEOC').css('display','none');
				},

				cleanServiceSigns: function(){
					$('#IPOLIML_notPayedBNal').css('display','');
					$('#IPOLIML_payedNal').css('display','');
				},

				popUp: function(code, info){ // Вспл. подсказки
					var obj = $('#'+code);
					var LEFT = (parseInt($('#IPOLIML_wndOrder').width())-parseInt(obj.width()))/2;
					obj.css({
						top: ($(info).position().top+15)+'px',
						left: LEFT,
						display: 'block'
					});	
					return false;
				},

				ajax: function(params){
					var ajaxParams = {
						type : 'POST',
						url  : "/bitrix/js/<?=self::$MODULE_ID?>/ajax.php",
					};
					if(typeof(params.data) != 'undefined')
						ajaxParams.data = params.data;
					if(typeof(params.dataType) != 'undefined')
						ajaxParams.dataType = params.dataType;
					if(typeof(params.success) != 'undefined')
						ajaxParams.success = params.success;
					$.ajax(ajaxParams);
				},

				// SERVISE
				inArray: function(wat,arr){
					return arr.filter(function(item){return item == wat}).length;
				},

				isPost: function(wat){
					if(typeof(wat) == 'undefined')
						wat = $('#IPOLIML_service');
					return (wat.val() !== null && wat.val().trim() == '<?=GetMessage('IPOLIML_SIGN_POST')?>');
				}
			};

			$(document).ready(IPOLIML_oExport.onLoad);
		</script>
		<style type='text/css'>
			.PropHint { 
				background: url('/bitrix/images/<?=self::$MODULE_ID?>/hint.gif') no-repeat transparent;
				display: inline-block;
				height: 12px;
				position: relative;
				width: 12px;
			}
			.PropHint:hover{background: url('/bitrix/images/<?=self::$MODULE_ID?>/hint.gif') no-repeat transparent !important;}
			.b-popup { 
				background-color: #FEFEFE;
				border: 1px solid #9A9B9B;
				box-shadow: 0px 0px 10px #B9B9B9;
				display: none;
				font-size: 12px;
				padding: 19px 13px 15px;
				position: absolute;
				top: 38px;
				width: 300px;
				z-index: 12;
			}
			.b-popup .pop-text { 
				margin-bottom: 10px;
				color:#000;
			}
			.pop-text i {color:#AC12B1;}
			.b-popup .close { 
				background: url('/bitrix/images/<?=self::$MODULE_ID?>/popup_close.gif') no-repeat transparent;
				cursor: pointer;
				height: 10px;
				position: absolute;
				right: 4px;
				top: 4px;
				width: 10px;
			}
			#IPOLIML_service{
				max-width: 328px;
			}
			#IPOLIML_wndOrder td:last-child{
				max-width: 328px;
			}
			#IPOLIML_notPayedBNal,#IPOLIML_payedNal{
				display:none;
			}
		</style>
	<div style='display:none'>
		<table id='IPOLIML_wndOrder'>
<?// Общее?>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_STATUS')?></td><td><?=$status.$satBut?></td></tr>
			<tr><td colspan='2'><small><?=GetMessage('IPOLIML_JS_SOD_STAT_'.$status)?></small><?=$message['number']?></td></tr>
			<tr>
				<td><?=GetMessage('IPOLIML_JS_SOD_toPay')?></td>
				<td><?=$toPay?> 
					<?=(array_key_exists('SUM_PAID',$orderinfo) && $orderinfo['SUM_PAID'] > 0)?"<span style='color:red'>".GetMessage('IPOLIML_JS_SOD_alreadyPayed')." ".$orderinfo['SUM_PAID']."</span>":''?>
					&nbsp;
					<?=($payed)?'<span style="color:green">'.GetMessage('IPOLIML_JS_SOD_payed').'</span>':''?>
				</td>
			</tr>
<?// Заявка?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLIML_JS_SOD_HD_PARAMS')?></td></tr>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_number')?></td><td><?=$orderinfo['ACCOUNT_NUMBER']?></td></tr>
			<tr>
				<td>
					<?=GetMessage('IPOLIML_JS_SOD_service')?>
				</td>
				<td>
					<select id='IPOLIML_service' onchange='IPOLIML_oExport.onCodeChange($(this))'><?=$strOfCodes?></select>
					<?=$message['service']?>
					<div id='IPOLIML_notPayedBNal'><?=GetMessage('IPOLIML_JS_SOD_NPBNal')?></div>
					<div id='IPOLIML_payedNal'>    <?=GetMessage('IPOLIML_JS_SOD_PNal')?>  </div>
				</td>
			</tr>
			<tr id='IPOLIML_contentTypeCT'><td><?=GetMessage('IPOLIML_JS_SOD_contentType')?></td><td><select id='IPOLIML_contentType'><?=$strOfCT?></select><?=$message['service']?></td></tr>
			<?if(COption::GetOptionString(self::$MODULE_ID,'selectDeparture','N') == 'Y'){?>
				<tr><td><?=GetMessage('IPOLIML_JS_SOD_departure')?></td><td>
					<?imlOption::makeSelect('IPOLIML_departure',$arList['Region'],COption::GetOptionString(self::$MODULE_ID,'departure'))?>
					<?=$message['departure']?>
				</td></tr>
			<?}?>
<?// Дата?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLIML_JS_SOD_HD_DATE')?> <a href='javascript:void(0)' id='IPOLIML_popupDate' class='PropHint' onclick='return IPOLIML_oExport.popUp("pop-IMLdate", this);'></a></td></tr>
			<tr>
				<td><?=GetMessage('IPOLIML_JS_SOD_issue')?></td>
				<td>
					<div class="adm-input-wrap adm-input-wrap-calendar">
						<input class="adm-input adm-input-calendar" id='IPOLIML_issue' disabled type="text" name="IPOLIML_issue" size="22" value="<?=$ordrVals['issue']?>">
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'IPOLIML_issue', form: '', bTime: true, bHideTime: true, callback_after: IPOLIML_oExport.onDataSelect});"></span>
					</div>
					<?=$message['issue']?>
				</td>
			</tr>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_TD')?> <a href='javascript:void(0)' class='PropHint' onclick='return IPOLIML_oExport.popUp("pop-IMLtime", this);'></a></td><td><input id='IPOLIML_timeFrom' size='5' disabled type='text' value="<?=$ordrVals['timeFrom']?>"> - <input id='IPOLIML_timeTo' size='5' disabled type='text' value="<?=$ordrVals['timeTo']?>"><?=$message['timeFrom']?> <?=$message['timeTo']?></td></tr>
			<?if(intval(date('H'))>=18){?>
				<tr><td colspan='2' style='color:red'><?=GetMessage('IPOLIML_JSC_SOD_timeWarn18')?></td></tr>
			<?}?>
			<tr><td colspan='2'><span id='IPOLIML_timeWarning' style='color:red;display:none;'><?=GetMessage('IPOLIML_JSC_SOD_timeWarnSmall').date('d.m.Y',$dateOfDelive)?></span></td></tr>
			<tr><td colspan='2'><span id='IPOLIML_nightTimeWarning' style='color:red;display:none;'><?=GetMessage('IPOLIML_JSC_SOD_nightTimeWarning')?></span></td></tr>
<?// Адрес?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLIML_JS_SOD_HD_ADDRESS')?></td></tr>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_destination')?></td><td>
				<?imlOption::makeSelect('IPOLIML_destination',$arList['Region'],$regionTo,"onchange='IPOLIML_oExport.onRegionChange($(this))'") ?>
				<?=$message['destination']?>
			</td></tr>
			<tr class='IPOLIML_notSV'><td><?=GetMessage('IPOLIML_JS_SOD_city')?></td><td><input id='IPOLIML_city' type='text' value='<?=$ordrVals['city']?>'><?=$message['city']?></td></tr>
			<tr class='IPOLIML_notSV'><td><?=GetMessage('IPOLIML_JS_SOD_line')?></td><td><input id='IPOLIML_line' type='text' value='<?=$ordrVals['line']?>'><?=$message['line']?></td></tr>
			<tr class='IPOLIML_notSV'><td><?=GetMessage('IPOLIML_JS_SOD_postCode')?></td><td><input id='IPOLIML_postCode' type='text' value='<?=$ordrVals['postCode']?>'><?=$message['postCode']?></td></tr>
			<tr class='IPOLIML_SV'><td><?=GetMessage('IPOLIML_JS_SOD_deliveryPoint')?></td><td><?=$strOfPSV?><span id='IPOLIML_deliveryPoint_noSV'><?=GetMessage('IPOLIML_JS_SOD_NOSVREG')?></span><?=$message['deliveryPoint']?></td></tr>
			<tr class='IPOLIML_SV'><td colspan='2'><span id='IPOLIML_PVZ_OPEN' style='display:none;color:red'><?=GetMessage('IPOLIML_JS_SOD_NOTOPEN')?></span><span id='IPOLIML_PVZ_CLOSE' style='display:none;color:red'><?=GetMessage('IPOLIML_JS_SOD_CLOSING')?></span><span id='IPOLIML_PVZ_DATEOC' style='display:none;color:red'></span></td></tr>
<?// Получатель?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLIML_JS_SOD_HD_RESIEVER')?></td></tr>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_name')?></td><td><input id='IPOLIML_name' type='text' value='<?=$ordrVals['name']?>'><?=$message['name']?></td></tr>
			<tr><td valign="top"><?=GetMessage('IPOLIML_JS_SOD_telephone1')?></td><td><input id='IPOLIML_telephone1' type='text' value='<?=$ordrVals['telephone1']?>'><?=$message['telephone1']?>&nbsp;
				<?if(!$ordrVals['telephone3']){/*я не придумал ничего лучше*/?><button type='button' onclick='IPOLIML_oExport.addPhone(<?if(!$ordrVals['telephone2']) echo '2'; else echo '3';?>)'>+</button><?}?>
				<?if($ordrVals['telephone2']){?><br><input id='IPOLIML_telephone2' type='text' value='<?=$ordrVals['telephone2']?>'><?=$message['telephone2']?><?}?>
				<?if($ordrVals['telephone3']){?><br><input id='IPOLIML_telephone3' type='text' value='<?=$ordrVals['telephone3']?>'><?=$message['telephone3']?><?}?>
			</td></tr>
			<tr><td valign="top"><?=GetMessage('IPOLIML_JS_SOD_email')?></td><td><input id='IPOLIML_email' type='text' value='<?=$ordrVals['email']?>'>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_comment')?></td><td><textarea id='IPOLIML_comment'><?=$ordrVals['comment']?></textarea><?=$message['comment']?></td></tr>
			<tr><td colspan='2'>
				<div id="pop-IMLdate" class="b-popup" >
					<div class="pop-text"><?=GetMessage("IPOLIML_JSC_SOD_HELPER_date")?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
				<div id="pop-IMLtime" class="b-popup" >
					<div class="pop-text"><?=GetMessage("IPOLIML_JSC_SOD_HELPER_time")?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
				<div id="pop-IMLplases" class="b-popup" >
					<div class="pop-text"><?=GetMessage("IPOLIML_JSC_SOD_HELPER_places")?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
				<div id="pop-IMLcomplectation" class="b-popup" >
					<div class="pop-text"><?=GetMessage("IPOLIML_JSC_SOD_HELPER_complectation")?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
			</td></tr>
<?// Прочее?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLIML_JS_SOD_HD_OTHER')?></td></tr>
			<tr><td valign="top"><?=GetMessage('IPOLIML_JS_SOD_VATRate')?></td><td><?imlOption::makeSelect('IPOLIML_VATRate',array('NONDS'=>GetMessage('IPOLIML_SIGN_NONDS'),'0'=>'0%','10'=>'10%','18'=>'18%'),$ordrVals['VATRate'])?><?=$message['VATRate']?><?=($message['VATAmount']) ? "<br>".$message['VATAmount']:''?></td></tr>
			<?if(
				COption::getOptionString(self::$MODULE_ID,'editStatisticalValue','N') === 'Y' &&
				COption::getOptionString(self::$MODULE_ID,'noVats','N') === 'N'
			){?>
				<tr><td valign="top"><?=GetMessage('IPOLIML_JS_SOD_StatAmount')?></td><td><input id='IPOLIML_StatAmount' type='text' value='<?=$ordrVals['StatAmount']?>'></td></tr>
			<?}?>
			<tr><td><?=GetMessage('IPOLIML_JS_SOD_places')?> <a href='javascript:void(0)' class='PropHint' onclick='return IPOLIML_oExport.popUp("pop-IMLplases", this);'></a></td><td>
				<select id='IPOLIML_places'>
				<?for($i=1; $i<10;$i++){?>
					<option value='<?=$i?>' <?=($ordrVals['places'] == $i)?'selected':''?>><?=$i?></option>
				<?}?>
				</select>
			</td></tr>
			<tr><td valign="top"><?=GetMessage('IPOLIML_JS_SOD_complectation')?> <a href='javascript:void(0)' class='PropHint' onclick='return IPOLIML_oExport.popUp("pop-IMLcomplectation", this);'></a></td><td><input name='IPOLIML_complectation' id='IPOLIML_complectation' value='Y' type='checkbox' <?=(array_key_exists('complectation',$ordrVals) && $ordrVals['complectation']) ? 'checked' : ''?>></td></tr>
		</table>
	</div>