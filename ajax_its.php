<?
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

	$APPLICATION->RestartBuffer();
	
	//error processing
	$fields = array();
	
	$required = array('company', 'location', 'info', 'fio', 'mylo');
	
	foreach ($required as $test_req) {
		
		if ($_POST[$test_req]=='') {
			$error['type'] = 'required';
			$error['ru'] = 'Не введено значение';
			$error['name'] = $test_req;
			$fields[] = $error;			
		}
		
		if ($test_req=='mylo' && strlen($_POST[$test_req]) > 0 && !filter_var($_POST[$test_req], FILTER_VALIDATE_EMAIL)) {
			$error['type'] = 'required';
			$error['ru'] = 'Неверный e-mail';
			$error['name'] = $test_req;
			$fields[] = $error;	
		}
		
	}
	
	if (count($fields) > 0) {
		$result['ok'] = 0;
		$result['fields'] = $fields;
	} else {
		$result['ok'] = 1;
	}
	
	
	
	if (!$_POST['email']) {		
		//honeypot
		//отправляем инфоблок
		
		CModule::IncludeModule('iblock'); 
		
		//ищем дубли
		
		$arSelectDbl = Array("ID");
		$arFilterDbl = Array("IBLOCK_ID"=>506, "PROPERTY_EMAIL"=>htmlspecialcharsbx($_POST['mylo']));
		$resDbl = CIBlockElement::GetList(Array(), $arFilterDbl, false, Array("nPageSize"=>1), $arSelectDbl);
		if($obDbl = $resDbl->GetNextElement())
		{
			// чувак спамит, игнорим
			//$result['spam'] = 1;
		} 
		else 
		{

			$el = new CIBlockElement;
			
			$PROP = array();
			
			$PROP["LOCATION"] = htmlspecialcharsbx($_POST['location']);
			$PROP["STATUS"] = Array("VALUE" => $_POST['status']);
			$PROP["WEB"] = htmlspecialcharsbx($_POST['website']);
			$PROP["INFO"] = htmlspecialcharsbx($_POST['info']);
			$PROP["SHOPS"] = htmlspecialcharsbx($_POST['shops']);
			$PROP["NAME"] = htmlspecialcharsbx($_POST['fio']);
			$PROP["EMAIL"] = htmlspecialcharsbx($_POST['mylo']);
			$PROP["PHONE"] = htmlspecialcharsbx($_POST['phone']);
			$FileID = CFile::SaveFile($_FILES['attach'], "form"); 		
			$PROP["ATTACH"] = $FileID;
			
			$ID = $el->Add(array( 
			   "IBLOCK_ID" => 506, 
			   "NAME" => htmlspecialcharsbx($_POST['company']),  
			   "PROPERTY_VALUES" => $PROP
			)); 
			
			$result['err'] = $el->LAST_ERROR;
			
			$result['eid'] = $ID;
			
			$arEventFields = $PROP;
			$arEventFields['COMPANY'] = htmlspecialcharsbx($_POST['company']);
			
			$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"STATUS"));
			
			while($enum_fields = $property_enums->GetNext())
			{
			  if ($enum_fields["ID"]==$_POST['status']) {
				  $arEventFields["STATUS"] = $enum_fields["VALUE"];
			  }
			}
			
			$arEventFields["ATTACH"] = 'https://moshoztorg.ru'.CFile::GetPath($FileID);

			CEvent::Send("PARTNERSHIP", 'el', $arEventFields);
		}
	} 
	
	echo CUtil::PhpToJSObject($result);
	die();
?>