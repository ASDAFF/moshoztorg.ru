<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main\Application;
if(CModule::IncludeModule('iblock')) {

	$file =  fopen ("friday.csv","r"); // лежит в этой же папке
	$ids = array();
	while ($row = fgets($file, 1500)) {

		
		$ids[] = trim($row);

	}
	
	//print_r ($ids);	


	$res = CIBlock::GetList(
		Array(), 
		Array(
			'TYPE'=>'mht_products',         
			'ACTIVE'=>'Y'        
		), true
	);
	while($ar_res = $res->Fetch())
	{
		
			$IB = $ar_res['ID'];		
			
			$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IB, "CODE"=>"SAYT_BLACK_FRIDAY_TOVAR"));
			while($enum_fields = $property_enums->GetNext())
			{
			  $prop[$enum_fields["XML_ID"]] = $enum_fields["ID"];			  
			}
			
			
			
			
			
			$arSelect = Array("ID", "NAME", "IBLOCK_ID");
			$arFilter = Array("IBLOCK_ID"=>$IB, "ACTIVE_DATE"=>"Y");
			$res1 = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			while($ob = $res1->GetNextElement())
			{
			  
			  $arFields = $ob->GetFields();
			  
			  
			  
			  
			  //echo ('*********************** <br/>');
			  $traits = $ob->GetProperty("CML2_TRAITS");
			  $key = 0;
			  foreach ($traits["DESCRIPTION"] as $key => $value) {
				  //echo ($value.' + '.$key.'<Br/>');
				  if ($value == 'Код') {
					  $val_key = $key;
					  //echo ('sovpalo'.$val_key);
				  }
			  }
			  
			  $code = $traits['VALUE'][$val_key];
			  
			  //echo ('<br/> ищу code '.$code);
			  
			  
			  if (in_array (trim($code), $ids)) {				  
				  echo ('<br/>Буду обновлять '.$arFields['NAME'].' + '.$arFields['ID']);
				  ++$i;
				 //if ($i>5) die();
				 echo ('<br/> Set'.$arFields['ID'].'to'.$prop['true'].'<Br/>');			  
				 CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array('SAYT_BLACK_FRIDAY_TOVAR' => $prop['true']));
			  
				  				  
				  
			  }
			  
			  
			  
			  
			  
			}

			
			
		
	}


	
}
