<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs("/local/templates/mht"."/js/slick.js");
Asset::getInstance()->addJs("/local/templates/mht"."/js/slick_init.js");
Asset::getInstance()->addCss("/local/templates/mht"."/css/slick.css");

?>