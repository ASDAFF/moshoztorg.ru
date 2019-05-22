<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
IncludeTemplateLangFile(__FILE__);

switch(@$_GET['desktop']){
	case 'y':
		$_SESSION['only-desktop'] = 'y';
		break;

	case 'n':
		unset($_SESSION['only-desktop']);
		break;
}

use \Bitrix\Main\Page\Asset;
$oAsset = Asset::getInstance();

?>
    <!doctype html>
    <html>

    <head>
        <script src="//api-maps.yandex.ru/2.1.17/?lang=ru_RU"></script>
        <meta charset="utf-8">
        <title>
            <?$APPLICATION->ShowTitle(true)?>
        </title>
        <?
		
		
		// CJSCore::Init(array("jquery"));
		Webprofy\Bitrix\General::setJSandCSS(array(
			'js' => array(
				'priority' => array(
					'jquery-1.11.1.min.js',
					'jquery-ui.min.js',
					'jquery.ui.touch-punch.js',
					'jquery.fancybox.js',
					'modernizr-latest.js',
				)
			)
		));
		
		$oAsset->addCss("/local/templates/mht/css/yastrebov.css", true);
		$oAsset->addCss("/local/templates/mht/css/serov.css", true);
		$oAsset->addCss("/local/templates/mht/css/Complaint.css", true);
		
		$oAsset->addJs("/local/templates/mht/js/jquery.maskedinput.js");
		$oAsset->addJs("/local/templates/mht/js/yastrebov.js");
		$oAsset->addJs("/local/templates/mht/js/Complaint.js");
	?>
            <meta name="viewport" content="width=device-width,initial-scale=1">

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

            <!--[if lt IE 9]>
    <script src="/local/templates/mht/js/html5shiv.js"></script>
	<![endif]-->

            <script>
                var mht = {};

            </script>

            <!-- Google Analytics -->
            <script>
                (function(i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function() {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
                ga('create', 'UA-65831537-1', 'auto', {
                    'siteSpeedSampleRate': 70
                });
                ga('send', 'pageview');

            </script>
            <?$APPLICATION->ShowHead();?>
    </head>


    <body class="<?=isset($_SESSION['only-desktop']) ? '' : 'mobile_enabled'?>">
        <?$APPLICATION->ShowPanel()?>
            <section class="maket">
                <div class="wrap">
                    <header class="header" id="#top">
                        <div class="header_content">
                            <a href="#" class="menu_button"></a>
                            <input readonly="readonly" id="header_region" type="text" class="region" value="<?=WP::get('region')->cur()->prop('name')?>">
                            <!--
      -->
                            <nav class="top_menu">
                                <ul>
                                    <li><a href="#">Главная</a></li>
                                    <li class="gtx_haschild"><a href="/catalog/">Каталогъ</a>
                                        <ul class="gtx_secondlevel">
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/1.png" alt=""></div>
                                                    <p>Авто товары</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/2.png" alt=""></div>
                                                    <p>Аксессуары для ванной комнаты</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/3.png" alt=""></div>
                                                    <p>Бытовая техника</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/4.png" alt=""></div>
                                                    <p>Бытовая химия</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/5.png" alt=""></div>
                                                    <p>Вода</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/6.png" alt=""></div>
                                                    <p>Инструмент малярно-штукатурный</p>
                                                </a>
                                                <ul class="gtx_thirdlevel">
                                                    <li><a href="#">Аппликаторы декоративные</a></li>
                                                    <li><a href="#">Валики и ролики малярные</a></li>
                                                    <li><a href="#">Вспомогательный малярный инструмент</a></li>
                                                    <li><a href="#">Гладилки мярные</a></li>
                                                    <li><a href="#">Инструмент разметочный</a></li>
                                                    <li><a href="#">Кельмы</a></li>
                                                    <li><a href="#">Кисти малярные</a></li>
                                                    <li><a href="#">Правила</a></li>
                                                    <li><a href="#">Скребки</a></li>
                                                    <li><a href="#">Терки финишные</a></li>
                                                    <li><a href="#">Шнуры малярные</a></li>
                                                    <li><a href="#">Шпатели</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/7.png" alt=""></div>                                       <p>Инструменты измерительные</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/8.png" alt=""></div>                                       <p>Климатическое оборудование</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/9.png" alt=""></div>                                       <p>Косметика и гигиена</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/10.png" alt=""></div>
                                                    <p>Освещение, лампочки</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/11.png" alt=""></div>
                                                    <p>Пневматическое оборудование</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/12.png" alt=""></div>
                                                    <p>Помосты, лестницы</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/13.png" alt=""></div>
                                                    <p>Расходные материалы и оснастка</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/14.png" alt=""></div>
                                                    <p>Садовый инструмент и инвентарь</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/15.png" alt=""></div>
                                                    <p>Сантехника</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/16.png" alt=""></div>
                                                    <p>Силовая и строительная техника</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/17.png" alt=""></div>
                                                    <p>Системы хранения</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/18.png" alt=""></div>
                                                    <p>Спецодежда</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/19.png" alt=""></div>
                                                    <p>Средства индивидуальной защиты</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/20.png" alt=""></div>
                                                    <p>Строительная химия</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/21.png" alt=""></div>
                                                    <p>Товары для активного отдыха</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/22.png" alt=""></div>
                                                    <p>Товары для дома</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/23.png" alt=""></div>
                                                    <p>Товары для сада</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/24.png" alt=""></div>
                                                    <p>Электро-инструменты</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><div class="gtx_imgholder"><img src="gtx_catphotos/25.png" alt=""></div>
                                                    <p>Распродажа</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a href="#">О компании</a></li>
                                    <li><a href="#">Доставка и оплата</a></li>
                                    <li><a href="#">Магазины</a></li>
                                </ul>
                            </nav>
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
                                <?/*
      --><a class="backet" href="/basket/">корзина пуста</a><!--
    */
?>
                                    <div class="not-mobile">
                                        <?
			/*
				$APPLICATION->IncludeComponent('mht:usermenu', '', array(

				));
				*/
	      ?>
                                            <?$APPLICATION->IncludeComponent(
				"bitrix:system.auth.form",
				"header",
				Array(
				)
			);?>

                                    </div>
                        </div>
                    </header>

                    <?
	$APPLICATION->IncludeComponent('mht:usermenu', 'phone', array(

	));
?>

                        <div class="hayhop">
                            <div class="window">
                                <div class="title"></div>
                                <div class="note"></div>
                                <div class="close">&times;</div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <?/*
   <div class="form_success">
    <div class="image"></div>
    <div class="text">Ваша заявка принята.</div>
    <div class="close"></div>
</div>
*/?>
                            <? if($APPLICATION->GetCurDir() !== '/'){ ?>
                                <?=MHT::searchHipHopHTML()?>
                                    <div class="head_block">
                                        <div class="logotype_block">
                                            <a href="/" class="logotype"><img alt="" src="/local/templates/mht/images/logotype-black@2x.png" width="195" height="50"></a>
                                        </div>
                                        <?$APPLICATION->IncludeComponent('mht:phones')?>
                                            <?=MHT::searchHTML('global', false)?>
                                                <nav class="submenu">
                                                    <ul>
                                                        <?
		    			$a = array(
		    				array('/catalog/', 'Каталог'),
		    				array('/brands/', 'Бренды'),		    				
		    				array('/catalog/new/', 'Новинки'),
							array('/informatsiya/video/', 'Видео'),
							array('/catalog/offers/', 'Акции')
		    			);

		    			$dir = $APPLICATION->GetCurDir();
		    			$inactive = true;
		    			for($i = 1; $i < 4; $i++){
		    				if(strpos($dir, $a[$i][0]) === 0){
		    					$a[$i][2] = true;
		    					$inactive = false;
		    					break;
		    				}
		    			}

		    			if($inactive && strpos($dir, '/catalog/') === 0){
		    				$a[0][2] = true;
		    			}

		    			foreach($a as $b){
		    				list($link, $name, $active) = $b;
		    				?>
                                                            <li<?=$active ? ' class="active"' : ''?>>
                                                                <a href="<?=$link?>">
                                                                    <?=$name?>
                                                                </a>
                                                                </li>
                                                                <?
		    			}
		    		?>
                                                    </ul>
                                                </nav>
                                    </div>

                                    <?$APPLICATION->IncludeComponent('bitrix:breadcrumb', 'mht')?>
                                        <? } ?>

                                            <div class="top-notification">
                                                <div class="wrapper">
                                                    <div class="image">
                                                    </div>
                                                    <div class="text">
                                                        Данные успешно отправлены.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mobile-header">
                                                <a href="/" class="logo">
                                                    <img src="/img/mht-logo.png" alt="МосХозТорг">
                                                </a>
                                                <a href="#" class="search-button js-search-button"><img src="/img/mobile/search.png" /></a>

                                                <div class="phones">
                                                    <?$APPLICATION->IncludeComponent('mht:phones')?>
                                                </div>
                                            </div>
                                            <div class="clear"></div>


                                            <a href="#top" class="go-up">
                                                <img src="/img/goup.png" alt="">
                                            </a>
                                            <?if($APPLICATION->GetProperty("is_content_page") == "Y"){?>
                                                <div class="styles_page">
                                                    <div class="styles">
                                                        <h1><?$APPLICATION->ShowTitle(true)?></h1>
                                                        <?}?>
