<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult['REGIONS'] = [];
foreach(WP::get('region')->all() as $region){
    $arResult['REGIONS'][] = array(
        'code' => $region->prop('code'),
        'label' => $region->prop('name'),
        'value' => $region->getRegionURL(),
        'active' => $region->prop('active') ? 'y' : 'n',
        'coords' => array(
            $region->prop('lng'),
            $region->prop('lat')
        ),
        'zoom' => $region->prop('zoom')
    );
}


//Bitrix\Main\Page\Asset::getInstance()->addJs("//api-maps.yandex.ru/2.1.17/?lang=ru_RU");

$regions = array();
foreach(WP::get('region')->all() as $region){
    $regions[] = array(
        'code' => $region->prop('code'),
        'label' => $region->prop('name'),
        'value' => $region->getRegionURL(),
        'active' => $region->prop('active') ? 'y' : 'n',
        'coords' => array(
            $region->prop('lng'),
            $region->prop('lat')
        ),
        'zoom' => $region->prop('zoom')
    );
}

Bitrix\Main\Page\Asset::getInstance()->addString('<script>var mht = {}; mht.regions = '.WP::js($regions).';
	mht.shops = '.WP::js($arResult['SHOPS']).';
    mht.shopRegion = "'.$arResult['ACTIVE_REGION'].'";
    //'.__FILE__.'
</script>');

?>