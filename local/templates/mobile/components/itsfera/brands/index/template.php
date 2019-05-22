<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    ?><ul>
        <li>
            <div class="selectwrap">
                <select id="brand-section-select">
                <option value="0" selected>Выберите категорию товара</option>
                <?foreach($arResult['CATEGORIES'] as $catId=>$arSection):?>
                    <option data-link="<?=$arSection['full-link']?>" value="<?=$arSection['id']?>"><?=$arSection['name']?></option>
                <?endforeach?>
                </select>
            </div>
        </li>

    <li>

        <div class="accordpages">
            <div class="pagesleft"></div>
            <div class="pagesletterswrap">
                <?$k= 0;
                foreach($arResult['BRANDS'] as $letter=>$arBrands):?>
                    <?
                    $sSectionClass = 'category-brands';
                    foreach($arBrands as $arBrand){
                        if (isset($arBrand['childs'])){
                            foreach($arBrand['childs'] as $arChild) {
                                $sSectionClass .= ' category-brand-' . $arChild['category_id'];
                            }
                        }else {
                            $sSectionClass .= ' category-brand-' . $arBrand['category_id'];
                        }
                    }
                    ?><div><a href="javascript:void(0)" class="letterlink<?php echo $k++==0?' active':'';?> <?php echo $sSectionClass;?>"><?php echo $letter?></a></div>
                <?endforeach?>
            </div>
            <div class="pagesright"></div>
        </div>
        <div class="lettersdatacontent">
            <?$k= 0;
            foreach($arResult['BRANDS'] as $letter=>$arBrands):?>
            <div class="lettersdatacontentitem" data-letter="<?php echo $letter;?>">
                <ul>
                    <?foreach($arBrands as $arBrand):

                        /*if ($arBrand['name']=="Guzzini")
                            myPrintR( $arBrand , __FILE__, __LINE__ );*/


                        if (isset($arBrand['childs'])){
                            foreach($arBrand['childs'] as $k=>$arChild) {
                                $sSectionClass = 'category-brands ';
                                $sSectionClass .= 'category-brand-' . $arChild['category_id'].' ';

                                if ($k>0) $sSectionClass .= ' other-sections-brand';

                                ?><li class="<?php echo $sSectionClass;?>"><a data-brand-link="<?php echo $arChild['brand-link'];?>"
                                                                data-link="<?php echo $arChild['link'];?>"
                                                                href="<?php echo $arChild['brand-link'];?>"><?php echo $arChild['name'];?></a></li><?
                            }

                        }else {
                            $sSectionClass = 'category-brands category-brand-' . $arBrand['category_id'];
                            ?><li class="<?php echo $sSectionClass;?>"><a data-brand-link="<?php echo $arBrand['brand-link'];?>"
                                                                          data-link="<?php echo $arBrand['link'];?>"
                                                                          href="<?php echo $arBrand['brand-link'];?>"><?php echo $arBrand['name'];?></a></li><?
                        }
                        ?>
                    <?endforeach?>
                </ul>
            </div>
            <?endforeach?>
        </div>
    </li>
    </ul>