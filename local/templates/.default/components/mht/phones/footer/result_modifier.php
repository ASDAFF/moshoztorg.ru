<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	foreach ($arResult['PHONES'] as &$arPhone) {

	    $arParts = explode( ' ', strip_tags($arPhone['html']));
	    $arPhone['html'] = $arParts[0].' '.$arParts[1].' <b>'.$arParts[2].'</b>';

    }
