<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();



use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs( "/search/jquery-ui.min.js");
Asset::getInstance()->addCss( "/search/jquery-ui.min.css");
Asset::getInstance()->addCss( "/search/searchStyle.css");
Asset::getInstance()->addJs( "/search/script_search.js");


