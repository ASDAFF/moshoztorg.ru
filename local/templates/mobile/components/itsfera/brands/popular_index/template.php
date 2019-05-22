<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//myPrintR( $arResult['BRANDS'] , __FILE__, __LINE__ );

?>
<div class="popularbrands">
    <div class="headingline">
        <div class="popbarrowleft">
            <i class="flaticon-left"></i>
        </div>
        <p class="heading">Популярные бренды</p>
        <div class="popbarrowright">
            <i class="flaticon-right"></i>
        </div>
    </div>
    <div class="itemscarousel">

        <div>
            <?
            foreach($arResult['BRANDS'] as $group){
                ?><div class="brandtrinity">
                    <p><?=$group['name']?></p>
                    <ul>
                        <?
                        foreach($group['brands'] as $brand){
                            ?>
                            <li><a href="<?=$brand['brand-link']?>"><?=$brand['name']?></a></li>
                            <?
                        }
                        ?>
                    </ul>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>

<?/*

<ul>
        <li>
            <div class="selectwrap">
                <select id="brand-section-select">
                <option value="0" selected>Выберите категорию товара</option>
                <?foreach($arResult['CATEGORIES'] as $catId=>$arSection):?>
                    <option value="<?=$arSection['id']?>"><?=$arSection['name']?></option>
                <?endforeach?>
                </select>
            </div>
        </li>

    <li>
        <div class="accordpages">
            <!--div class="pagesleft"></div-->
            <div class="pagesletterswrap">
                <?$k= 0;
                foreach($arResult['BRANDS'] as $letter=>$arBrands):?>
                    <?
                    $sSectionClass = 'category-brands';
                    foreach($arBrands as $arBrand):
                        if (isset($arBrand['child'])){
                            foreach($arBrand['child'] as $arChild) {
                                $sSectionClass .= ' category-brand-' . $arChild['category_id'];
                            }
                        }else {
                            $sSectionClass .= ' category-brand-' . $arBrand['category_id'];
                        }

                        ?>

                    <?endforeach?>

                    <div><a href="javascript:void(0)" class="letterlink<?php echo $k++==0?' active':'';?> <?php echo $sSectionClass;?>"><?php echo $letter?></a></div>
                <?endforeach?>
            </div>
            <!--div class="pagesright"></div-->
        </div>
        <div class="lettersdatacontent">
            <?$k= 0;
            foreach($arResult['BRANDS'] as $letter=>$arBrands):?>
            <div class="lettersdatacontentitem" data-letter="<?php echo $letter;?>">
                <ul>
                    <?foreach($arBrands as $arBrand):
                        if (isset($arBrand['child'])){
                            foreach($arBrand['child'] as $arChild) {
                                $sSectionClass = 'category-brand-' . $arChild['category_id'];?>
                                <li class="category-brands <?php echo $sSectionClass;?>"><a href="<?php echo $arBrand['link'];?>"><?php echo $arBrand['name'];?></a></li><?
                            }
                        }else {
                            $sSectionClass = 'category-brands category-brand-' . $arBrand['category_id'];
                            ?><li class="<?php echo $sSectionClass;?>"><a href="<?php echo $arBrand['link'];?>"><?php echo $arBrand['name'];?></a></li>
                            <?
                        }

                        ?>

                    <?endforeach?>
                </ul>
            </div>
            <?endforeach?>
        </div>
    </li>
    </ul>*/?>