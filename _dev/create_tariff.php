<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

error_reporting(E_ERROR);
ini_set('display_errors', 1);

CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");


$res = CIBlock::GetList(
    Array(), 
    Array(
        'TYPE'=>'mht_products', 
        'SITE_ID'=>SITE_ID, 
        'ACTIVE'=>'Y', 
        "CNT_ACTIVE"=>"Y"        
    ), true
);
while($iblock = $res->Fetch())
{
    //echo $ar_res['NAME'].': '.$ar_res['ELEMENT_CNT'];
	$arFields = Array(
	  "NAME" => "Тариф AdmitAd", 
	  "ACTIVE" => "Y",
	  "SORT" => "100",
	  "CODE" => "ADMITAD_TARIFF",
	  "PROPERTY_TYPE" => "L",
	  "IBLOCK_ID" => $iblock["ID"]
	  );

	$arFields["VALUES"][0] = Array(
	  "VALUE" => "1",
	  "DEF" => "N",
	  "SORT" => "100",
	  "XML_ID" => "1"
	);

	$arFields["VALUES"][1] = Array(
	  "VALUE" => "2",
	  "DEF" => "N",
	  "SORT" => "200",
	  "XML_ID" => "2"
	);

	$arFields["VALUES"][2] = Array(
	  "VALUE" => "3",
	  "DEF" => "Y",
	  "SORT" => "300",
	  "XML_ID" => "3"
	);
	
	$arFields["VALUES"][3] = Array(
	  "VALUE" => "4",
	  "DEF" => "N",
	  "SORT" => "400",
	  "XML_ID" => "4"
	);

	$arFields["VALUES"][4] = Array(
	  "VALUE" => "5",
	  "DEF" => "N",
	  "SORT" => "500",
	  "XML_ID" => "5"
	);

	$arFields["VALUES"][5] = Array(
	  "VALUE" => "6",
	  "DEF" => "Y",
	  "SORT" => "600",
	  "XML_ID" => "6"
	);

	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
	
	echo ($iblock["ID"]."<Br/>".$PropID."<Br/>");
	
}