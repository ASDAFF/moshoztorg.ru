<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Page\Asset;

GLOBAL $APPLICATION, $USER;

$oAsset = Asset::getInstance();

$CurDir = $APPLICATION->GetCurDir();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

//Две строчки ниже для защиты форм от спам-ботов
session_start();
$_SESSION['sf_key'] = md5(substr(session_id(), mt_rand(0, 10), mt_rand(3, 10)) . time());

$title = 'Мосхозторг';
$desc = 'Товары для дома и сада';
?><!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" class="loading">
    <head>
         <!-- Meta information (content-type + mobile mod) -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width">
        <meta name="format-detection" content="telephone=no">
        <meta name="cmsmagazine" content="2f345f737ed0d95e9259d18f5edc8cd7">
        <meta name="tagline" content="">

        <meta name="yandex-verification" content="5b6530aabe125ef3" />


<link rel="alternate" media="only screen and (max-width: 640px)" href="<?php echo MOBILE_SITE_VERSION.strip_tags($_SERVER['REQUEST_URI']);?>">


        <!-- Favicon -->
		<link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-96x96.png" type="image/x-icon">
		<link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon.ico" type="image/x-icon">

        <!-- CSS styles -->
        <?
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/jquery.fancybox.min.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/jquery.formstyler.min.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/jquery-ui.min.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/swiper.min.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/style.css?".time(), true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/ui-autocomplete.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/style-fix.css", true);
        $oAsset->addCss(SITE_TEMPLATE_PATH."/css/fullpagemenu.css", true);
        ?>

        <!-- OGP -->
        <meta property="og:title" content="<?php echo $title; ?>"/>
        <meta property="og:description" content="<?php echo $desc; ?>"/>
        <meta property="og:url" content="<?php echo $url; ?>">
        <meta property="og:image" content="<?php echo $image; ?>">


<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NP5F57Z');</script>
<!-- End Google Tag Manager -->




		<?$APPLICATION->ShowHead();?>
		<title><?$APPLICATION->ShowTitle();?></title>

	<!-- Retail Rocket -->

	<script type="text/javascript">
       var rrPartnerId = "587e22cd5a658836ec140d1c";       
       var rrApi = {}; 
       var rrApiOnReady = rrApiOnReady || [];
       rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = 
           rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};
       (function(d) {
           var ref = d.getElementsByTagName('script')[0];
           var apiJs, apiJsId = 'rrApi-jssdk';
           if (d.getElementById(apiJsId)) return;
           apiJs = d.createElement('script');
           apiJs.id = apiJsId;
           apiJs.async = true;
           apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
           ref.parentNode.insertBefore(apiJs, ref);
       }(document));
    </script>
	
	<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => SITE_TEMPLATE_PATH."/include/retailrocket.php"
	)
	);?>
						
	
</head>


    <body id="main">

        <div id="overlay_menu"></div>
    <?

    if ( $USER->isAuthorized()) {
        ?><div id="panel"><?$APPLICATION->ShowPanel();?></div><?
    }

        ?>


<?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu_2018", Array(
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"CHILD_MENU_TYPE" => "top_menu_2018_left",	// Тип меню для остальных уровней
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
			0 => "",
		),
		"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"MENU_CACHE_TYPE" => "N",	// Тип кеширования
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"ROOT_MENU_TYPE" => "top_menu_2018",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	),
	false
);?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NP5F57Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


<div id="global-wrapper">


    <!--===================================================== Loader -->
    <div class="loader">
        <div class="pseudo-table">
            <div class="pseudo-table-cell align-center">

                <img src="<?=SITE_TEMPLATE_PATH?>/images/logo2.png" alt=" ">

            </div>
        </div>
    </div>
    <div class="headerspacerfixed">
        <div class="container">
        <div class="spaceforanimation"></div>
        </div>
    </div>
    <!--===================================================== Header -->
    <header class="layout-header header">
        <div class="container">
            <div class="row bottomLine">
                <!-- global-nav -->
                <div class="col-5-12 col-xs-1-3">
                    <div class="kostyl"></div>
<!--                    <select name="client_city" class="client-city"-->
<!--                            data-placeholder="Выберите страну">-->
<!--                        <option value="spb">Санкт-Петербург</option>-->
<!--                        <option value="moscow">Москва</option>-->
<!--                    </select>-->
                    <nav class="global-nav hidden-xs">
                        <ul class="nav">
                             <? $APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
                                 "ROOT_MENU_TYPE"        => "top2018",
                                 "MAX_LEVEL"             => "1",
                                 "USE_EXT"               => "N",
                                 "DELAY"                 => "N",
                                 "ALLOW_MULTI_SELECT"    => "N",
                                 "MENU_CACHE_TYPE"       => "N",
                                 "MENU_CACHE_TIME"       => "3600",
                                 "MENU_CACHE_USE_GROUPS" => "Y",
                                 "MENU_CACHE_GET_VARS"   => "",
                             ),
                                 false
                             ); ?>
                            </li>
                        </ul>
                    </nav>

                </div>

                <!-- Logo -->
                <div class="col-1-6 col-xs-1-3">
                    <a href="https://<?=SITE_SERVER_NAME?>" class="logo si-jump">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/logo2.png" alt=" ">
                    </a>
                </div>

                <!-- Phone block -->
                <div class="col-5-12 align-right col-xs-1-3">

                    <?$APPLICATION->IncludeComponent('mht:phones')?>

                    <div class="authorization">

                        <?$APPLICATION->IncludeComponent(
                            "bitrix:system.auth.form",
                            "header",
                            Array(
                            )
                        );?>

                        <div class="authorization-info">

                            <?
                            $APPLICATION->IncludeComponent('mht:favorites', 'header', array(
                            ));
                            ?>

                            <?$APPLICATION->IncludeComponent(
                                "bitrix:sale.basket.basket.line",
                                "mht",
                                array(
                                    "PATH_TO_BASKET" => "/catalog/basket/",
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

                </div>
            </div>

            

                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "catalog",
                    array(
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "36000",
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

				<div class="catalog-search float-right">
                    <form action="/search/" method="get" data-ajax-action="/search/request.php">
                    <?=bitrix_sessid_post()?>
                    <input type="text" name="q" class="input-search inheader ui-autocomplete-input" placeholder="Поиск">
                    <input type="submit" class="input_search_submit js-search-button inheader" value="">
                    </form>

                    <a href="#" class="si-close dark">
                        Закрыть <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
                    </a>
                </div>

                <div class="authorization-info forfix">
                    <a href="javascript:void(0)" class="authorize open-authorization-modal">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/login.svg" alt="">
                    </a>
                    <?$APPLICATION->IncludeComponent('mht:favorites', 'header', array());?>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:sale.basket.basket.line",
                        "mht_fixed",
                        array(
                            "PATH_TO_BASKET" => "/catalog/basket/",
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
    </header>


<a href="#top" class="go-up">
	<img src="/img/goup.png" alt="">
</a>