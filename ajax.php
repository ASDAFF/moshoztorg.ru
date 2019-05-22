<?
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

	use Bitrix\Catalog;
	use Bitrix\Sale;
    use Bitrix\Sale\DiscountCouponsManager;
    use Bitrix\Sale\Discount;

	$APPLICATION->RestartBuffer();
	$live = false;

	function sendBasket($action = false){

	    global $USER,$APPLICATION;

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());

        $fullPrice = $basket->getBasePrice();

        $result = [
            'fullPrice' => CurrencyFormatNumber($fullPrice, 'RUB'),
        ];

        $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "empty", Array(
                "HIDE_ON_BASKET_PAGES" => "Y",
                "PATH_TO_BASKET" => "/cart/",
                "PATH_TO_ORDER" => "/make/",
                "PATH_TO_PERSONAL" => "personal/",
                "PATH_TO_PROFILE" => "personal/",
                "PATH_TO_REGISTER" => "login/",
                "POSITION_FIXED" => "Y",
                "POSITION_HORIZONTAL" => "right",
                "POSITION_VERTICAL" => "top",
                "SHOW_AUTHOR" => "Y",
                "SHOW_DELAY" => "N",
                "SHOW_EMPTY_VALUES" => "Y",
                "SHOW_IMAGE" => "Y",
                "SHOW_NOTAVAIL" => "N",
                "SHOW_NUM_PRODUCTS" => "Y",
                "SHOW_PERSONAL_LINK" => "N",
                "SHOW_PRICE" => "Y",
                "SHOW_PRODUCTS" => "Y",
                "SHOW_SUMMARY" => "Y",
                "SHOW_TOTAL_PRICE" => "Y"
            ),
            false
        );


        $result['arBasket'] = $GLOBALS['arBasket'];

        if (!$action) {
            $result['action'] = $action;
        }

        header('Content-Type: application/json');

        echo \Bitrix\Main\Web\Json::encode($result);

        CMain::FinalActions();
        die();

    }

	switch($_GET['action']){
		case 'compare-delete':
			$iblock = intval($_GET['id1']);
			$element = intval($_GET['id2']);
			if(!$iblock || !$element){
				break;
			}
			unset($_SESSION['CATALOG_COMPARE_LIST'][$iblock]['ITEMS'][$element]);
			$iblock = WP::iblocks(array(
				'filter' => array(
					'ID' => $iblock
				)
			));
			$iblock = $iblock[0];

			LocalRedirect($iblock['LIST_PAGE_URL'].'compare/');
			break;
	}
	
	switch($_REQUEST['action']){
		case 'RRsentemail':
            GLOBAL $USER;
            $rsUser = CUser::GetByID($USER->getid());
            $arUser = $rsUser->Fetch();
			if(strlen($arUser['EMAIL'])>0) {
                ?>
                <script>
                    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                        rrApi.setEmail("<?=$arUser['EMAIL']?>");
                    });
                </script><?
            }
			break;
		case 'unsubscribe':
			CModule::IncludeModule('subscribe');
			$list = CSubscription::GetList(array(), array(
				'USER_ID' => $USER->GetID()
			));
			while($subscribe = $list->Fetch()){
				\WP::log($subscribe);
			}
			break;

		case 'get-section-menu':
			list($iblock, $section) = array_map('intval', explode(':', $_REQUEST['data']));

			if(!$iblock || !$section){
				return;
			}
			
			$APPLICATION->IncludeComponent('mht:catalog_menu', '', array(
				'IBLOCK_ID' => $iblock,
				'SECTION_ID' => $section
			));

			break;

		case 'unlog':
			$USER->Logout();
			break;
			
		case 'vote':
			MHT::vote(array(
				'id' => intval($_REQUEST['id']),
				'vote' => intval($_REQUEST['value'])
			));
			break;
		case 'search-get-initial':
			$result = WP::cache('ajax-search-get-initial-1', WP::time(1, 'm'), function(){
				$result = array(
					'products' => array(),
					'ok' => true
				);

				/*	if($i < 4){
						$type = 'offer';
					}
					elseif($i < 8){
						$type = 'new';
					}
					elseif($i < 12){
						$type = 'popular';
					}
					else{
						return false;
					}
				*/

				foreach(array(
					array('SAYT_NOVINKA', 'new'),
					array('SAYT_AKTSIONNYY_TOVAR', 'offer'),
					array('SAYT_NA_GLAVNUYU', 'popular')
				) as $a){
					list($n, $name) = $a;

					$data = array(
						'f' => array(
							'IBLOCK_ID' => MHT::getCatalogIDs(),
							'ACITVE' => 'Y',
							'GLOBAL_ACTIVE' => 'Y',
							'!DETAIL_PICTURE' => false
						),
						'each' => function($f, $p, $i) use (&$result, &$name){
							$product = new MHT\Product($f, $p);
							if($f['ACTIVE'] == "Y"){
								$result['products'][$name][] = array(
									'active' => $f['ACTIVE'],
									'name' => $f['NAME'],
									'price' => $product->get('price'),
									'image' => $product->get('small-image', 'src'),
									'link' => $product->get('link')
								);
								if($i == 3){
									return false;
								}
							}
						}
					);

					/*
					if($n == 'popular'){
						$data['sort']['SHOW_COUNTER'] = 'DESC';
					}
					else{
						$data['sort']['RAND'] = 'Y';
						$data['p'][$n] = 'Y';
					}
					*/
					$data['p'][$n."_VALUE"] = 'Да';
					WP::elements($data);
				}
			
				return $result;
			});

			echo json_encode($result);
			break;

		case 'change-per-page':
			MHT\CatalogPerPage::getInstance()->set($_REQUEST['value']);
			break;

		case 'change-sort':
			if(isset($_REQUEST['list']) && $_REQUEST['list'] != ''){
				MHT\CatalogSort::getInstance()->setListId($_REQUEST['list']);
			}
			MHT\CatalogSort::getInstance()->set($_REQUEST['value']);
			break;

		case 'apply_discount':
			echo $_REQUEST['value'];
			break;

		case 'basket-get-amount':
			echo MHT::getBasketItemsAmount().'';
			break;
		case 'fav-add':
		case 'fav-remove':
			$product = MHT\Product::byId($_REQUEST['id']);
			$product->fav();
		break;
		/*case 'fav-remove':
                CModule::IncludeModule("sale");
				var_dump(intval($_REQUEST['id']));
				CSaleBasket::Delete(intval($_REQUEST['id']));
			break;*/

        case 'basket-get':
            sendBasket(false);
			break;
		case 'basket-add':
			var_dump($_REQUEST['id']);
		
			$product = MHT\Product::byId($_REQUEST['id']);
			$amount = intval($_REQUEST['amount']);
			$amount = $amount > 0 ? $amount : 1;
			$product->buy($amount);
		break;
		case 'basket-remove':
			CModule::IncludeModule("sale");
			CSaleBasket::Delete(intval($_REQUEST['id']));
			sendBasket('basket-remove');
			break;	

		case 'basket-set-amount':
			CModule::IncludeModule("sale");
			CSaleBasket::Update(intval($_REQUEST['id']), array(
				'QUANTITY' => intval($_REQUEST['amount'])
			));
            sendBasket('basket-set-amount');
			break;

		case 'searchtitle':
			$APPLICATION->IncludeComponent(
				"bitrix:search.title",
				"mht",
				Array(
					"COMPONENT_TEMPLATE" => ".default",
					"NUM_CATEGORIES" => "1",
					"TOP_COUNT" => "5",
					"ORDER" => "date",
					"USE_LANGUAGE_GUESS" => "Y",
					"CHECK_DATES" => "Y",
					"SHOW_OTHERS" => "N",
					"PAGE" => "#SITE_DIR#search/",
					"SHOW_INPUT" => "Y",
					"INPUT_ID" => "title-search-input",
					"CONTAINER_ID" => "title-search",
					"CATEGORY_0_TITLE" => "",
					"CATEGORY_0" => array("iblock_mht_products"),
					"CATEGORY_0_iblock_mht_products" => array("all"),
					"PRICE_CODE" => array(PRICE_CODE),
					"PRICE_VAT_INCLUDE" => "Y",
					"CONVERT_CURRENCY" => "N",
					"PREVIEW_WIDTH" => "75",
					"PREVIEW_HEIGHT" => "75",
					"CURRENCY_ID" => "RUB"
				)
			);
			break;

		default:
			$live = true;
			break;
	}
	
	if(!$live){
		die();
	}

	$result = Webprofy\Ajax\Main::getInstance()
		->addForms(MHT::forms())
		->run();
	echo CUtil::PhpToJSObject($result);

	die();
?>