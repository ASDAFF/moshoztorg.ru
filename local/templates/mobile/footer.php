<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Application;
?>
<footer>
    <div class="footercontacts">
        <a href="#" class="footerlogo">
            <img src="<?php echo SITE_TEMPLATE_PATH;?>/images/logofooter.png" alt="">
        </a>
        <div class="contacttextblock">
            <?$APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                Array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => "/include_areas/footer_contacts.php",
                    "AREA_FILE_RECURSIVE" => "N",
                    "EDIT_MODE" => "html",
                )
            );?>

        </div>
    </div>
    <?$APPLICATION->IncludeComponent(
        "bitrix:menu",
        "bottommobilemenu",
        array(
            
            "ROOT_MENU_TYPE" => "bottom",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(

            ),
            "MAX_LEVEL" => "1",
            "CHILD_MENU_TYPE" => "sub",
            "USE_EXT" => "N",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N"
        ),
        false
    );?>
    <div class="footersocials">
         <?$APPLICATION->IncludeComponent(
             "bitrix:main.include",
             "",
             Array(
                 "AREA_FILE_SHOW" => "file",
                 "PATH" => "/include_areas/footer_social.php",
                 "AREA_FILE_RECURSIVE" => "N",
                 "EDIT_MODE" => "html",
             )
         );?>
    </div>
    <div class="footerpayers">
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => "/include_areas/footer_payments.php",
                "AREA_FILE_RECURSIVE" => "N",
                "EDIT_MODE" => "html",
            )
        );?>

    </div>
    <div class="footercopy">
        <?
        // включаемая область для раздела
        $APPLICATION->IncludeFile("/include_areas/footer_copyright.php", Array(), Array("MODE" => "html"));
        ?>
    </div>
    <?
    $request = Application::getInstance()->getContext()->getRequest();
    $sCurPage = $request->getRequestedPage();
    $sPrefix = '?';
    if (strpos($sCurPage,'?')!==false) $sPrefix = '&';
    ?>
    <a href="<?php echo FULL_SITE_VERSION ?><?php echo $sCurPage.$sPrefix.'from_mobile=Y';?>" class="fullversion">Полная версия сайта</a>
</footer>
<script src="https://www.artfut.com/static/tagtag.min.js?campaign_code=b4b564561f" async onerror='var self = this;window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers=ADMITAD.Helpers||{},ADMITAD.Helpers.generateDomains=function(){for(var e=new Date,n=Math.floor(new Date(2020,e.getMonth(),e.getDate()).setUTCHours(0,0,0,0)/1e3),t=parseInt(1e12*(Math.sin(n)+1)).toString(30),i=["de"],o=[],a=0;a<i.length;++a)o.push({domain:t+"."+i[a],name:t});return o},ADMITAD.Helpers.findTodaysDomain=function(e){function n(){var o=new XMLHttpRequest,a=i[t].domain,D="https://"+a+"/";o.open("HEAD",D,!0),o.onload=function(){setTimeout(e,0,i[t])},o.onerror=function(){++t<i.length?setTimeout(n,0):setTimeout(e,0,void 0)},o.send()}var t=0,i=ADMITAD.Helpers.generateDomains();n()},window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers.findTodaysDomain(function(e){if(window.ADMITAD.dynamic=e,window.ADMITAD.dynamic){var n=function(){return function(){return self.src?self:""}}(),t=n(),i=(/campaign_code=([^&]+)/.exec(t.src)||[])[1]||"";t.parentNode.removeChild(t);var o=document.getElementsByTagName("head")[0],a=document.createElement("script");a.src="https://www."+window.ADMITAD.dynamic.domain+"/static/"+window.ADMITAD.dynamic.name.slice(1)+window.ADMITAD.dynamic.name.slice(0,1)+".min.js?campaign_code="+i,o.appendChild(a)}});'></script>

</body>
</html>