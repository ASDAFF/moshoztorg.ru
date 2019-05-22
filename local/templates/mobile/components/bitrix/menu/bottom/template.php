<div class="menu_block"><?
	if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){
		die();
	}

	$elements = array();
	$group = null;

	foreach($arResult as $arItem){
		$name = $arItem['TEXT'];
		$link = $arItem['LINK'];

		if($name[0] != '-'){
			$group['children'][] = array(
				'name' => $name,
				'link' => $link
			);
			continue;
		}

		$name = trim(substr($name, 1));
		if($group !== null){
			$elements[] = $group;
		}
		$group = array(
			'name' => $name,
			'link' => $link,
			'children' => array()
		);
	}

	$elements[] = $group;


	$i = 0;
	foreach($elements as $element){
		if(!$element['name']){
			continue;
		}
		?>
			<div class="menu">
				<div class="menu_title">
					<a href="<?=$element['link']?>"><?=$element['name']?></a>
				</div>
				<ul>
					<?
						foreach($element['children'] as $child){
							?>
								<li>
									<a href="<?=$child['link']?>"><?=$child['name']?></a>
								</li>
							<?
						}
					?>
				</ul>
			</div>
		<?
	}
?></div>