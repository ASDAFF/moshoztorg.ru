<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
  <div class="contacts_page">
    <div class="contacts container" id="contactsanchor">
        <h1><?=$arResult["NAME"]?></h1>
        <div class="slidersblock">
        <div class="photosslider">
            <?if($arResult["DETAIL_PICTURE"]["SRC"]){?>
            <div class="slide" style="background-image: url('<?=$arResult["DETAIL_PICTURE"]["SRC"]?>')"></div>
            <?}?>
            
            <?if($arResult["PREVIEW_PICTURE"]["SRC"]){?>
            <div class="slidewrap">
                <div class="slide" style="background-image: url('<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>')"></div>
            </div>
            <?}?>
            
            <?foreach($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $photo){?>
            <div class="slide" style="background-image: url('<?=CFile::GetPath($photo)?>')"></div>
            <?}?>
        </div>
        <div class="photossliderpager">
            <?if($arResult["DETAIL_PICTURE"]["SRC"]){?>
            <div class="slidewrap">
                <div class="slide" style="background-image: url('<?=$arResult["DETAIL_PICTURE"]["SRC"]?>')"></div>
            </div>
            <?}?>
            <?if($arResult["PREVIEW_PICTURE"]["SRC"]){?>
            <div class="slidewrap">
                <div class="slide" style="background-image: url('<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>')"></div>
            </div>
            <?}?>
            <?foreach($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $photo){?>
            <div class="slidewrap">
                <div class="slide" style="background-image: url('<?=CFile::GetPath($photo)?>')"></div>
            </div>
            <?}?>
        </div>
        </div>
        <div class="contactsblock">
            <ul>
                <?foreach($arResult["PROPERTY_119780"] as $phone){?>
                <li style="background-image:url('/img/contacts/info_icon1.png')"><p><?=$phone?></p></li>
                <?}?>
                <li style="background-image:url('/img/contacts/info_icon2.png')"><p>Будни: <?=$arResult["PROPERTY_146764"]?></p><p>Суббота: <?=$arResult["PROPERTY_146765"]?></p><p>Воскресенье: <?=$arResult["PROPERTY_146766"]?></p></li>
                <?if($arResult["PROPERTIES"]["HOW_TO_REACH"]["VALUE"]["TEXT"]){?>
                <li style="background-image:url('/img/contacts/info_icon3.png')"><p>Как добраться:</p>
                <p><?=$arResult["PROPERTIES"]["HOW_TO_REACH"]["VALUE"]["TEXT"]?></p></li>
                <?}?>
            </ul>

        </div>
        <?$coords = explode(',', $arResult["PROPERTIES"]["COORDS"]["VALUE"]);?>
        <div class="innermap" id="shops_map" data-lat="<?=$coords[0]?>" data-lng="<?=$coords[1]?>"></div>
    </div>
</div>