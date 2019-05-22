<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
if($arResult["arUser"]["PERSONAL_PHONE"] && strlen($arResult["arUser"]["PERSONAL_PHONE"]) == 10) {
	$phone = $arResult["arUser"]["PERSONAL_PHONE"];
	$firstBlock = substr($phone, 0, 3);
	$secondBlock = substr($phone, 3, 3);
	$thirdBlock = substr($phone, 6, 2);
	$fourthBlock = substr($phone, 8, 2);

	$formatedPhone = "+7 " . "({$firstBlock})" . " {$secondBlock} " . "- {$thirdBlock} " . "- {$fourthBlock}";
	$arResult["arUser"]["PERSONAL_PHONE_FORMATED"] = $formatedPhone;
}
?>