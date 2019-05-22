<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
IncludeTemplateLangFile(__FILE__);

use \Bitrix\Main\Page\Asset;
//use Bitrix\Main\Application;

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

//Две строчки ниже для защиты форм от спам-ботов
session_start();
$_SESSION['sf_key'] = md5(substr(session_id(), mt_rand(0, 10), mt_rand(3, 10)) . time());


global $APPLICATION;
$CurDir = $APPLICATION->GetCurDir();
$pagenationOn = false;
foreach ($_GET as $key=>$val)
    if (strpos($key, 'PAGEN') !== false || strpos($key, 'page') !== false || $_GET['desktop'])
        $pagenationOn = true;


switch(@$_GET['desktop']){
	case 'y':
		$_SESSION['only-desktop'] = 'y';
		break;

	case 'n':
		unset($_SESSION['only-desktop']);
		break;
}


$oAsset = Asset::getInstance();


//$request = Application::getInstance()->getContext()->getRequest();
//$sCurPage = $request->getRequestedPage();

?><!doctype html>
<html>
<head>

<?/*
	<script src="//api-maps.yandex.ru/2.1.17/?lang=ru_RU"></script>*/?>
	<meta charset="utf-8">
    <meta name="yandex-verification" content="5b6530aabe125ef3" />
	<title><?$APPLICATION->ShowTitle(true)?></title>
    <?
    if($pagenationOn){?>
        <link rel="canonical" href="<?="https://".SITE_SERVER_NAME.$CurDir?>">
	<?}
    //$oAsset->addCss("/local/templates/mht/js/fancybox3/jquery.fancybox.min.css", true);
    //$oAsset->addJs("/local/templates/mht/js/fancybox3/jquery.fancybox.min.js");

		
		// CJSCore::Init(array("jquery"));
		Webprofy\Bitrix\General::setJSandCSS(array(
			'js' => array(
				'priority' => array(
					//'jquery-1.11.1.min.js',
                    'jquery-1.11.1.min.js',
                    //'jquery-migrate-1.4.1.min.js',
                    'jquery.fancybox.min.js',
					'jquery-ui.min.js',
					'jquery.ui.touch-punch.js',
					'modernizr-latest.js',
				)
			)
		));

    $oAsset->addCss("/local/templates/mht/css/jquery.formstyler.min.css", true);
	$oAsset->addCss("/local/templates/mht/css/wSelect.css", true);
	$oAsset->addCss("/local/templates/mht/fonts/flaticon.css", true);
	$oAsset->addCss("/local/templates/mht/css/yastrebov.css", true);
	$oAsset->addCss("/local/templates/mht/css/serov.css", true);
	$oAsset->addCss("/local/templates/mht/css/Complaint.css", true);
	$oAsset->addCss("/local/templates/mht/css/custom.css", true);
	$oAsset->addCss("/local/templates/mht/css/header.css", true);

	/*$oAsset->addJs("/local/templates/mht/js/jquery-1.11.1.min.js");
	$oAsset->addJs("/local/templates/mht/js/jquery-ui.min.js");
	$oAsset->addJs("/local/templates/mht/js/jquery.ui.touch-punch.js");
	$oAsset->addJs("/local/templates/mht/js/jquery.fancybox.js");
	$oAsset->addJs("/local/templates/mht/js/modernizr-latest.js");*/

	$oAsset->addJs("/local/templates/mht/js/jquery.maskedinput.js");
	$oAsset->addJs("/local/templates/mht/js/wSelect.js");
	$oAsset->addJs("/local/templates/mht/js/yastrebov.js");
	$oAsset->addJs("/local/templates/mht/js/Complaint.js");
    $oAsset->addJs("/local/templates/mht/js/main_new.js");
    $oAsset->addJs("/local/templates/mht/js/jquery.scrollbar.js");

    $oAsset->addJs("/local/templates/mht/js/custom.js");

    ?>
	<meta name="viewport" content="width=990px">
	<link rel="alternate" media="only screen and (max-width: 640px)" href="<?php echo MOBILE_SITE_VERSION.strip_tags($_SERVER['REQUEST_URI']);?>">


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
	<link rel="manifest" href="/local/templates/mht/favicons/manifest.json">
	<meta name="msapplication-TileColor" content="#ed2024">
	<meta name="msapplication-TileImage" content="/local/templates/mht/favicons/mstile-144x144.png">
	<meta name="theme-color" content="#ffffff">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NP5F57Z');</script>
<!-- End Google Tag Manager -->



	<!--[if lt IE 9]>
    <script src="/local/templates/mht/js/html5shiv.js"></script>
	<![endif]-->
	
	<script>
		var mht = {};
	</script>

	
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
	



	<?if(strpos($APPLICATION->GetCurPage(), "/magaziny/") !== false){
        $oAsset->addJs("//api-maps.yandex.ru/2.1.17/?lang=ru_RU&coordorder=longlat");
    }?>



	<?$APPLICATION->ShowHead();?>

</head>


<body class="<?=isset($_SESSION['only-desktop']) ? '' : 'mobile_enabled'?>">


<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NP5F57Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="overlay_menu"></div>


<?$APPLICATION->ShowPanel()?>

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

<div id="global-wrapper">

<section class="maket">
<div class="wrap">
    <div class="headerspacer"></div>
    <div class="headerspacerfixed">
        <div class="container">
        <div class="spaceforanimation"></div>
        </div>
    </div>
    <header class="layout-header header">
        <div class="container">
            <div class="row bottomLine">
                <!-- global-nav -->
                <div class="col-5-12 col-xs-1-3">
                    <div class="kostyl"></div>
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
                                    "HIDE_ON_BASKET_PAGES" => "N",
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
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "sub",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );?>

                <div class="catalog-search float-right">
                    <form action="/search/" method="get"  data-ajax-action="/search/request.php">
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


    <div class="hayhop">
        <div class="window">
            <div class="title"></div>
            <div class="note"></div>
            <div class="close">&times;</div>
            <div class="content"></div>
        </div>
    </div>

<? if($APPLICATION->GetCurDir() !== '/'){ ?>
    <div class="container">
	    <?$APPLICATION->IncludeComponent('bitrix:breadcrumb', 'mht')?>
    </div>
<? } ?>



    <a href="#top" class="go-up">
        <img src="/img/goup.png" alt="">
    </a>

<div class="container">
<?if($APPLICATION->GetProperty("is_content_page") == "Y"){?>
	<div class="styles_page">
		<div class="styles">
			<h1><?$APPLICATION->ShowTitle(true)?></h1>
<?}?>