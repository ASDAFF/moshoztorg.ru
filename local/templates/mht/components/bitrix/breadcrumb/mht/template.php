<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//флаг что мы в каталоге товаров
$isCatalog = ($arResult[1]['LINK'] == '/catalog/');

$n = count($arResult);
if($n < 2){
	return '';
}

if(isset($GLOBALS['custom-bread'])){
	if($GLOBALS['custom-bread'] == false){
		return '';
	}
	$links = $GLOBALS['custom-bread'];
}
else{
	$links = array();
	for($i = 0; $i < $n; $i++){
		$link = $arResult[$i]["LINK"];
		
		// if($link == '/catalog/'){
		// 	continue;
		// }

		$links[] = array(
			'name' => htmlspecialcharsex($arResult[$i]["TITLE"]),
			'link' => $link
		);
	}
}

$links[count($links) - 1]['active'] = true;
$class = isset($GLOBALS['BREAD_CLASS']) ? $GLOBALS['BREAD_CLASS'] : '';
$result = '<div class="def-wrapper"><nav class="breadcrumbs '.$class.'"><ul itemscope itemtype="http://schema.org/BreadcrumbList">';
	foreach($links as $content => $link){
		$html = $link['name'];
		if($link['active']){
		    if (!$isCatalog) {
                continue;
            }
			$html = '<span class="active" itemprop="name">'.$html.'</span><meta itemprop="position" content="'.($content + 1).'" />';
		}
		else{
			$html = '<a '.WP::attr(array(
				'href' => $link['link'],
				'title' => $link['name']
			)). ' itemscope itemtype="http://schema.org/Thing" itemprop="item"><span itemprop="name">'.$html.'</span></a><meta itemprop="position" content="'.($content + 1).'" />';
		}
		$result .= '<li itemprop="itemListElement" itemscope
      itemtype="http://schema.org/ListItem">'.$html.'</li>';
		if(!$link['active']){
			$result .= '<li class="br">&nbsp;</li>';
		}
	}
$result .= '</ul></nav></div>';
return $result;
?>