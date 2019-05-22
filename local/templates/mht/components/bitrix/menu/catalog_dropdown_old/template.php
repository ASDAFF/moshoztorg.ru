<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

define("ITEMS_COUNT_IN_SUBMENU_BLOCK",6);
?><div class="triplegtmenu">
    <div class="level1">
        <ul>
        <?
        foreach($arResult as $index=>$arItem):
        ?><li><a data-index="<?=$index?>" href="<?php echo $arItem['LINK'];?>"><?php echo $arItem['TEXT'];?></a></li>
        <?endforeach;?>
        </ul>
    </div>

    <?

    foreach($arResult as $index=>$arItem):
        if (!isset($arItem['CHILDREN'])) continue;

        //считаем кол-во элементов всего для определение высоты колонки
        $iSubItemsCount = array_reduce($arItem['CHILDREN'],function($iCount,$arItem){
            $iCount += 4; //один заголовок за 3 маленьких подменю по высоте
            if (isset($arItem['CHILDREN'][0])){

                $iSize = count($arItem['CHILDREN']);

                $iCount += $iSize;//$iSize>ITEMS_COUNT_IN_SUBMENU_BLOCK?ITEMS_COUNT_IN_SUBMENU_BLOCK:$iSize;
            }
            return $iCount;
        });

        //кол-во элементов в одно колонке
        $iOneColItemsCount = intval($iSubItemsCount/3);

        $iItemCounter = 0;
    ?><div class="level2 secondlevel<?=$index?>">
        <div class="level2_col">
        <?foreach($arItem['CHILDREN'] as $arSubItem):
            $iItemCounter += 4; //один заголовок за 4 маленьких подменю по высоте
            ?>
            <div class="category">
                <a href="<?php echo $arSubItem['LINK']?>" class="heading"><?php echo $arSubItem['TEXT']?></a>
                <?if (isset($arSubItem['CHILDREN'])):?>
                <ul>
                     <?foreach($arSubItem['CHILDREN'] as $k=>$arSubSubItem):
                         $iItemCounter++;

                         ?>
                        <li <?=$k>ITEMS_COUNT_IN_SUBMENU_BLOCK-1?'style="display:none"':'' ?>><a href="<?php echo $arSubSubItem['LINK']?>"><?php echo $arSubSubItem['TEXT']?></a></li>
                     <?endforeach?>
                </ul>
                <?if (count($arSubItem['CHILDREN'])>ITEMS_COUNT_IN_SUBMENU_BLOCK):?><a href="#" class="seeall">Все категории</a><?endif?>
                <?endif?>
            </div>

            <?if ($iItemCounter>=$iOneColItemsCount):
                $iItemCounter=0;
            ?></div><div class="level2_col"><?
            endif?>
        <?endforeach;?>
        </div>
    </div>
    <?endforeach;?>
</div>
