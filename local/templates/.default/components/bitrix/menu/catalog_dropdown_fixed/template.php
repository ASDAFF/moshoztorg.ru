<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

?>
<div class="fixed_catalogbtn">
    <a href="javascript:void(0)" class="dark catalog-btn">
        <div class="show-menu">
            <span class="icon-sandwich"></span>
        </div>
        Каталог
    </a>
                            <ul class="gtx_secondlevel gtxnotmob">

<?php
                                foreach($arResult as $index=>$arItem): ?>

                                    <li>
                                        <a href="<?php echo $arItem['LINK'];?>">
                                            <div class="gtx_imgholder">
                                                <?
                                                echo file_get_contents($CurPath.'/svg/menu_icons/'.$arItem["PARAMS"]["CODE"].'.svg'); ?>
                                            </div>
                                            <p><?php echo $arItem['TEXT'];?></p>
                                        </a>
                                        <div class="gtx_thirdlevel">
                                            <ul>
                                            <?foreach($arItem['CHILDREN'] as $subCount => $arSubItem):
                                            ?>
                                                <li>
                                                    <a href="<?php echo $arSubItem['LINK']?>"><?php echo $arSubItem['TEXT']?></a>
                                                </li>
                                            <?endforeach;?>
                                            </ul>
                                        </div>
                                    </li>
                                    <?if(($index+1)%4==0){?>
                                    <li class="gtx_thirdlevelwrap"></li>
                                    <?}?>
                                <?endforeach;?>
                                <li class="gtx_thirdlevelwrap"></li>
                            </ul>
</div>
                       