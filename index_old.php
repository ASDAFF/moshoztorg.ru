<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("tags", "Магазины МОСХОЗТОРГ - товары для дома и сада. Интернет магазин Москва. Хозяйственные товары. Бытовая химия. Электроинструмент. Посуда и домашняя утварь.");
$APPLICATION->SetPageProperty("keywords_inner", "Интернет магазин Москва. Хозяйственный магазин. МОСХОЗТОРГ - товары для дома и сада. Хозяйственные товары. Бытовая химия. Электроинструмент. Хозтовары.");
$APPLICATION->SetPageProperty("title", "Хозяйственные магазины МОСХОЗТОРГ - товары для дома и сада. Интернет магазин Москва. Хозяйственные товары. Бытовая химия. Электроинструмент. Посуда и домашняя утварь. Хозтовары.");
$APPLICATION->SetPageProperty("keywords", "Интернет магазин Москва Хозяйственный МОСХОЗТОРГ товары дом сад дача бытовая химия электроинструмент посуда средства косметика");
$APPLICATION->SetPageProperty("description", "Хозяйственные магазины МОСХОЗТОРГ: интернет магазин товаров для дома и сада, широкий ассортимент бытовой химии, хозтоваров, товаров для хранения, посуды, хозяйственных мелочей. Бесплатная доставка по Москве от 2000 рублей, доставка во все регионы России.");
$APPLICATION->SetTitle("Хозяйственные магазины МОСХОЗТОРГ - товары для дома и сада. Интернет магазин Москва. Бытовая химия. Электроинструмент. Посуда и домашняя утварь. Хозтовары.");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js_/index.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css_/index.css');
?><div class="index_page">
	 <?=MHT::searchHipHopHTML()?>
	<div class="beauty">
		<div class="beauty_bg">
		</div>
		<div class="logotype_block">
 <span class="logotype"><img width="195" src="/local/templates/mht/images/logotype-black@2x.png" height="50"></span>
		</div>
		 <?$APPLICATION->IncludeComponent(
	"mht:phones",
	"",
Array()
);?> <?$APPLICATION->IncludeComponent(
	"mht:big_banners",
	"",
	Array(
		"ELEMENTS_COUNT" => 5
	)
);?> <?/*
    <div class="offert_block">
      <div class="offert_name">dyson <span>360 eye</span></div>
      <div class="offert_text">делает всю работу за вас</div>
    </div>
    */?> <?=MHT::searchHTML('', false)?>
		<div class="best_info_block">
			 <!--
            --><?$APPLICATION->IncludeComponent(
	"mht:banners",
	".default",
	Array(
		"TYPE" => "small_main"
	)
);?><!--
        -->
			<div class="best_info">
				<div class="best_info_block_inf">
 <a href="/catalog/"><img width="165" src="/img/index/best_info_catalog.jpg" height="165" class="best_product_bg"><img width="165" src="/img/index/best_info_catalog_content.png" height="165" class="best_product_content"><span class="number"><?$APPLICATION->IncludeComponent(
	"mht:amount",
	"",
	Array(
		"TYPE" => "PRODUCTS"
	)
);?></span></a>
				</div>
				 <!--
            -->
				<div class="best_info_central">
					<div class="best_info_central_title">
						 не знаете что вам нужно?
					</div>
					<div class="best_info_central_subtitle">
						 мы подскажем!
					</div>
					<div class="best_info_central_description">
						 ответьте на несколько простых вопросов и наша система подберет нужный вам товар
					</div>
				</div>
				 <!--
            -->
				<div class="best_info_block_inf">
 <a href="/brands/"><img width="165" src="/img/index/best_info_brend.jpg" height="165" class="best_product_bg"><img width="165" src="/img/index/best_info_brend_content.png" height="165" class="best_product_content"><span class="number right"><?$APPLICATION->IncludeComponent(
	"mht:amount",
	"",
	Array(
		"TYPE" => "BRANDS"
	)
);?></span></a>
				</div>
			</div>
		</div>
	</div>
	 <?$APPLICATION->IncludeComponent(
	"mht:we_will_help",
	".default",
Array()
);?> 


<div data-retailrocket-markup-block="58886f945a658842d81a03fd" id="retailrocket-its"></div>


<?$APPLICATION->IncludeComponent(
	"mht:popular_products",
	"",
Array()
);?>
	<div class="behaviors">
 <a href="/catalog/offers/" class="behavior offers">
		<div class="behavior_hover">
		</div>
		<div class="behavior_title">
			 РАСПРОДАЖА
		</div>
		<div class="behavior_description">
			 <?WP::includeArea('index/offers')?>
		</div>
 </a><!--
    --><a href="/catalog/new/" class="behavior news">
		<div class="behavior_hover">
		</div>
		<div class="behavior_title">
			 Новинки
		</div>
		<div class="behavior_description">
			 <?WP::includeArea('index/new')?>
		</div>
 </a>
	</div>
	 <?$APPLICATION->IncludeComponent(
	"mht:brands",
	"index",
	Array(
		"IS_PREDEFINED_BRANDS" => "Y",
		"SEF_FOLDER" => "/brands/",
		"SEF_MODE" => "Y",
		"VARIABLE_ALIASES" => Array()
	)
);?> <?/*$APPLICATION->IncludeComponent('mht:shops', '', array(
    ))*/?>
</div>
 <br><?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>