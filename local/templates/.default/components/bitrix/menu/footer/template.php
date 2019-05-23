<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

foreach($arResult as $key => $arItem):
?>
        <div class="col-1-6 col-xs-1-3">
            <ul class="nav moreInfo">
                <li class="moreInfo-title light">
                    <a href="<?php echo $arItem['LINK'];?>"
                                                    class="light">
                        <?php echo $arItem['TEXT'];?>
                    </a>
                </li>

                <? $APPLICATION->IncludeComponent("bitrix:menu", "footer_sub", Array(
                    "ROOT_MENU_TYPE"        => "footer_sub".($key + 1),
                    "MAX_LEVEL"             => "1",
                    "CHILD_MENU_TYPE"       => "top",
                    "USE_EXT"               => "Y",
                    "DELAY"                 => "N",
                    "ALLOW_MULTI_SELECT"    => "N",
                    "MENU_CACHE_TYPE"       => "N",
                    "MENU_CACHE_TIME"       => "3600",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS"   => "",
                ),
                    false
                ); ?>


            </ul>
        </div>
<?
endforeach?>