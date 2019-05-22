<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;
IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<meta name="apple-itunes-app" content="app-id=1203742348">
	<meta name="google-play-app" content="app-id=ru.moshoztorg">

    <title><?$APPLICATION->ShowTitle(true)?></title>

    <?
    foreach ($_GET as $key=>$val) {
    }
        if ((strpos($key, 'PAGEN') === false) && (strpos($key, 'page') === false)) {
            ?>
            <link rel="canonical" href="<?="https://moshoztorg.ru".$APPLICATION->GetCurDir();?>">
            <?
        }
    ?>

    <link rel="apple-touch-icon" sizes="57x57" href="/local/templates/mht/favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/local/templates/mht/favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/local/templates/mht/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/local/templates/mht/favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/local/templates/mht/favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/local/templates/mht/favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/local/templates/mht/favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/local/templates/mht/favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/local/templates/mht/favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="/local/templates/mht/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/local/templates/mht/favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/local/templates/mht/favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/local/templates/mht/favicons/favicon-16x16.png" sizes="16x16">



    <?
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery.bxslider.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/style.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/fonts/flaticon/flaticon.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/fonts/mht/mht.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/slick.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery.smartbanner.css");

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/ion.rangeSlider.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/ion.rangeSlider.skinFlat.css");


    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery-1.12.3.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.smartbanner.js");
	

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery-ui.min.css");



    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.bxslider.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/script.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/slick.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/ion.rangeSlider.min.js");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/global.js");


    //if (DEBUG_MODE=='Y') 
      //  Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/itsfera.js");

    $APPLICATION->ShowHead();
    ?>
    <script>var mht = mht || {}</script>


<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NP5F57Z');</script>
<!-- End Google Tag Manager -->
<meta name="yandex-verification" content="63702235bff3a581" />
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NP5F57Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?$APPLICATION->ShowPanel()?>
<div class="mainmobilemenu">
<?$APPLICATION->IncludeComponent(
    "bitrix:menu",
    "mainmobilemenu",
    array(
        "MAX_ITEMS_IN_COL"=>10,
        "ROOT_MENU_TYPE" => "top",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(

        ),
        "MAX_LEVEL" => "2",
        "CHILD_MENU_TYPE" => "sub",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N"
    ),
    false
);?>
    <div class="mobilemenucityblock">
        <input readonly="readonly" id="header_region" type="text" class="region" value="<?=WP::get('region')->cur()->prop('name')?>">

        <!--<div class="cityname">
            <p>Москва</p>
        </div>
        <div class="othercity">
            <a href="#">Другой город</a>
        </div>-->
    </div>
</div>
<header<?php echo $GLOBALS['USER']->IsAuthorized()?' class="autorized"':'';?> >
    <div class="registerheading">
        <a id="signin-header-link" href="#"><i class="flaticon-user"></i> вход</a>
        <a id="signup-header-link" href="#">регистрация</a>
	</div>
    <div id="signin_form" style="display: none">
            <?$APPLICATION->IncludeComponent(
                "bitrix:system.auth.form",
                "header", //
                Array(
                    'SHOW_ERRORS'=>'Y'
                )
            );?>
        </div>
    <div id="signup_form" style="display: none">
            <?$APPLICATION->IncludeComponent(
                "bitrix:main.register",
                "mobile",
                Array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "SHOW_FIELDS" => array("EMAIL","NAME","LAST_NAME"),
                    "REQUIRED_FIELDS" => array("EMAIL","NAME"),
                    "AUTH" => "Y",
                    "USE_BACKURL" => "Y",
                    "SUCCESS_PAGE" => "",
                    "SET_TITLE" => "Y",
                    "USER_PROPERTY" => array(),
                    "USER_PROPERTY_NAME" => ""
                )
            );?>

            </div>
    
    <div class="blackheader">
        <div class="menubutton">
            <i class="flaticon-menu"></i>
        </div>
        <a href="/" class="logo">
            <img src="<?php echo SITE_TEMPLATE_PATH?>/img/logo.png" alt="">
        </a>
        <div class="rightheaderblock">
		<?
		global $USER;
		if ($USER->IsAuthorized()) {
			?><a href="/personal/" class="cabinetbutton"><?
		} else {
			?><a href="javascript:void(0);" class="cabinetbutton"><?			
		}
		?>
                <i class="flaticon-user"></i>
                <p><?php echo $GLOBALS['USER']->GetFullName()?$GLOBALS['USER']->GetFullName():$GLOBALS['USER']->GetLogin();?><i class="flaticon-bottom"></i></p>
            </a>
            <?$APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket.line",
                "mobile",
                array(
                    "PATH_TO_BASKET" => "/catalog/cart.php",
                    "PATH_TO_PERSONAL" => "/personal/",
                    "SHOW_PERSONAL_LINK" => "N",
                    "SHOW_NUM_PRODUCTS" => "Y",
                    "SHOW_TOTAL_PRICE" => "Y",
                    "SHOW_PRODUCTS" => "Y",
                    "POSITION_FIXED" => "N",
                    "SHOW_EMPTY_VALUES" => "Y",
                    "SHOW_AUTHOR" => "N",
                    "PATH_TO_REGISTER" => SITE_DIR."login/",
                    "PATH_TO_PROFILE" => SITE_DIR."personal/",
                    "SHOW_DELAY" => "N",
                    "SHOW_NOTAVAIL" => "Y",
                    "SHOW_SUBSCRIBE" => "Y",
                    "SHOW_IMAGE" => "Y",
                    "SHOW_PRICE" => "Y",
                    "SHOW_SUMMARY" => "Y",
                    "PATH_TO_ORDER" => "/personal/order/make/",
                    "BUY_URL_SIGN" => "action=ADD2BASKET",
                    "POSITION_HORIZONTAL" => "right",
                    "POSITION_VERTICAL" => "top"
                ),
                false
            );?>
        </div>
    </div>

    <?$APPLICATION->IncludeComponent(
        "bitrix:search.form",
        "",
        array(
            "USE_SUGGEST" => "N",
            "PAGE" => "#SITE_DIR#search/index.php"
        ),
        false
    );?>
    <?if ($GLOBALS['USER']->IsAuthorized()):?>
        <div class="autorizedmenu">

            <a href="javascript:void(0)" class="closepopup"><i class="flaticon-cross"></i></a>
             <?$APPLICATION->IncludeComponent(
                 "bitrix:main.include",
                 "",
                 Array(
                     "AREA_FILE_SHOW" => "file",
                     "PATH" => "/include_areas/header_auth_menu.php",
                     "AREA_FILE_RECURSIVE" => "N",
                     "EDIT_MODE" => "html",
                 )
             );?>

        </div>
    <?endif?>
</header>

	<!-- ТУТ ИЗВИНЕНИЯ ЗА НЕДОРАБОТКУ -->

	<div class="callpleasewrap">
		<div class="callplease">
			<p class="callpleaseheading">Приносим извинения</p>
			<hr>
			<p>К сожалению, мобильная версия сайта еще находится в разработке, поэтому не весь функционал работает корректно.</p>
			<p>Для оформления заказа вы можете воспользоваться <a href="mht.ru">полной версией сайта</a> или позвонить по телефону 8 (800) 550-47-47</p>
			<p>Извините за доставленные неудобства</p>
			<p><span class="closethis">Закрыть окно</span></p>
		</div>
	</div>
	<!-- ТУТ КОНЕЦ ИЗВИНЕНИЯМ ЗА НЕДОРАБОТКУ -->