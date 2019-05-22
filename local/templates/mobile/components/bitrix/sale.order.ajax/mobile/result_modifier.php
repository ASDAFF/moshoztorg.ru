<?php
use Bitrix\Main\Loader;
Loader::includeModule("catalog");


$arStores = array();
$select = array("*");
$filter = array("ACTIVE" => "Y","ISSUING_CENTER"=>"Y");

$rsProps = CCatalogStore::GetList(
    array('TITLE' => 'ASC', 'ID' => 'ASC'),
    $filter,
    false,
    false,
    $select
);

while ($prop = $rsProps->GetNext()){
    $arStores[ $prop['ID'] ] = $prop;
}

$arResult['STORES_INFO'] = $arStores;