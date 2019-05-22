<?
if($APPLICATION->GetProperty("is_content_page") == "Y"){?>
    </div>
</div>
<?}?>

  </div>
</div>

<div class="main2018">

<?
/**
 * тут новый шаблон
 *
 * костыль - для нормального отображения все стили нового шаблона применяются к блоку .main2018
 *
 *
 */

use \Bitrix\Main\Page\Asset;

GLOBAL $APPLICATION, $USER;

$oAsset = Asset::getInstance();
$oAsset->addCss(NEW_SITE_TEMPLATE_PATH."/css/main2018_template.css", true);

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.NEW_SITE_TEMPLATE_PATH;

?>

    <!--===================================================== Footer -->
    <footer class="layout-footer">
        <div class="container">
            <div class="row">

                <!-- Logo -->
                <div class="col-1-6 col-xs-5-6 xs-align-center">
                    <a href="#" class="logo si-jump">
                        <img class="hidden-xs" src="<?=NEW_SITE_TEMPLATE_PATH?>/images/logo-light.png" alt=" ">
                        <img class="visible-xs" src="<?=NEW_SITE_TEMPLATE_PATH?>/images/logo-light-mob.png" alt=" ">
                    </a>
                    <div class="schedule">

                        <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => NEW_SITE_TEMPLATE_PATH."/include/schedule.php"
                        )
                        );?>

                    </div>
                </div>


             <div class="col-1-12 col-xs-1-6">
                    <img class="hidden-xs" src="<?=NEW_SITE_TEMPLATE_PATH?>/images/blackhole.png" alt=" ">
                   <!-- <img class="visible-xs mht-mob" src="<?=NEW_SITE_TEMPLATE_PATH?>/images/mht-mob.png" alt=" "> -->
                </div>


                <? $APPLICATION->IncludeComponent("bitrix:menu", "footer", Array(
                    "ROOT_MENU_TYPE"        => "footer",
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

                <!-- Phone block -->
                <div class="col-1-4 align-right col-xs-1 xs-align-center">
                    <div class="si-phone">

                        <?$APPLICATION->IncludeComponent('mht:phones', 'footer')?>

                        <a href="#" class="btn open-phone-modal" data-extra="2">
                            <div class="pseudo-table">
                                <div class="pseudo-table-cell">
                                    Отправить сообщение
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="social">
                        <div class="social-title">Мы в соцсетях</div>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/MosHozTorg" class="light"><? echo file_get_contents($CurPath.'/svg/face.svg'); ?></a>
                            <a href="https://www.ok.ru/group/54890146037790" class="light"><? echo file_get_contents($CurPath.'/svg/ok.svg'); ?></a>
                            <a href="https://instagram.com/moshoztorg/" class="light"><? echo file_get_contents($CurPath.'/svg/insta.svg'); ?></a>
                            <a href="https://www.vk.com/moshoztorg" class="light"><? echo file_get_contents($CurPath.'/svg/vk.svg'); ?></a>
                        </div>

                        <div class="social-card">
                           <img src="<?=NEW_SITE_TEMPLATE_PATH?>/images/card/visa.svg" alt=" ">
                            <img src="<?=NEW_SITE_TEMPLATE_PATH?>/images/card/master.svg" alt=" ">
                            <img src="<?=NEW_SITE_TEMPLATE_PATH?>/images/card/mir.svg" alt=" ">
                           <!-- <img src="<?=NEW_SITE_TEMPLATE_PATH?>/images/card/yandex.png" alt=" ">
                            <img src="<?=NEW_SITE_TEMPLATE_PATH?>/images/card/wm.png" alt=" "> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <!--===================================================== Modals -->
    <!-- Overlay(s) -->
    <div class="si-overlay"></div>
    <div class="si-overlay-2"></div>

    <!-- Wrappers -->
    <div class="si-modals-wrapper-2"></div>

    <div class="si-modals-wrapper">

        <!--============================================== success modal -->
        <div class="si-success-modal si-success-modal-1">
            <a href="#" class="si-close">
                <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
            </a>

            <div class="modal-container align-center">

                <div class="si-success-modal-title">
                    Спасибо!
                </div>

                <div class="success-time offtop">
                    Наш менеджер свяжется с вами <br/>
                    в ближайшее время
                </div>

                <p class="success-schedule">
                    <strong>Время работы отдела продаж:</strong>
                    пн-пт с 9.00 до 21.00<br>
сб с 10.00 до 20.00<br>
вс с 11.00 до 20.00
                </p>
            </div>

            <div class="social success-social">
                <div class="social-title">Подпишитесь на наши новости:</div>
                <div class="social-icons">
                    <a target="_blank" href="https://www.facebook.com/MosHozTorg" class="dark"><? echo file_get_contents($CurPath.'/svg/face.svg'); ?></a>
                    <a href="https://www.ok.ru/group/54890146037790" class="dark"><? echo file_get_contents($CurPath.'/svg/ok.svg'); ?></a>
                    <a href="https://instagram.com/moshoztorg/" class="dark"><? echo file_get_contents($CurPath.'/svg/insta.svg'); ?></a>
                    <a href="https://www.vk.com/moshoztorg" class="dark"><? echo file_get_contents($CurPath.'/svg/vk.svg'); ?></a>
                </div>
            </div>
        </div>
        <div class="si-success-modal si-success-modal-2">
            <a href="#" class="si-close">
                <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
            </a>

            <div class="modal-container align-center">

                <div class="si-success-modal-title">
                    Спасибо!
                </div>

            </div>

        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:system.auth.form",
            "modal",
            Array(
            )
        );?>

        <!--============================================== phone modal -->
        <div class="si-modal phone-modal">
            <a href="#" class="si-close">
                <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
            </a>

            <div class="modal-container align-center">

                <div class="modal-form-title">
                    Закажите <br/> консультацию
                </div>

                <form method="post" class="send-form" autocomplete="off">
                    <div class="row">
                        <div class="col-1">
                            <input type="text" name="client_name" class="client-name"
                                   placeholder="Ваше имя">
                        </div>

                        <div class="col-1">
                            <input type="tel" name="client_phone" class="client-phone"
                                   placeholder="Ваш телефон">
                        </div>

                        <div class="col-1">
                            <input type="email" name="client_mail" class="client-mail"
                                   placeholder="Ваш email">
                        </div>
                    </div>

                    <textarea name="client_message" class="client-message"
                              placeholder="Ваш вопрос"></textarea>

                    <input type="hidden" name="send_type" class="send-type" value="1">
                    <input type="hidden" name="send_extra" class="send-extra" value="1">
                    <input type="hidden" name="key" value="<?php echo $_SESSION['sf_key'] ?>">
                    <?php //Поле выше для защиты формы от спам-ботов ?>

                    <div class="btn-holder">
                        <button type="submit" class="btn">Свяжитесь со мной</button>
                        <!--<div class="g-recaptcha" id="g-recaptcha"></div>-->
                    </div>

                    <!-- Agreement -->
                    <div class="form-agree align-left">
                        <label class="checkbox-label form-agree-check checked">
                            <input type="checkbox" checked>
                            Нажимая кнопку "ПОЗВОНИТЕ МНЕ", я&nbsp;даю своё согласие на&nbsp;обработку
                            моих персональных данных в&nbsp;соответствии с&nbsp;Федеральным законом
                            от&nbsp;27.07.2006&nbsp;года №152&#8209;ФЗ "О&nbsp;персональных данных",
                            на&nbsp;условиях и&nbsp;для&nbsp;целей, определённых
                            в&nbsp;Согласии на&nbsp;обработку персональных данных.
                        </label>
                    </div>
                </form>

            </div>
        </div>


    </div>


</div>



<script type="text/javascript">
    var template_url = '<?=NEW_SITE_TEMPLATE_PATH?>';
</script>

<!-- IE -->
<!--[if IE]>
<script src="<?=NEW_SITE_TEMPLATE_PATH?>/js/html5shiv.js"></script> <![endif]-->

<?
    // подключаем измененный js
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/plugins-all.js");

    // подключаем измененный js
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/smooth-scroll-1.4.4.min.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/main.js");
	$oAsset->addJs(NEW_SITE_TEMPLATE_PATH."/js/share.js");
    $oAsset->addJs(NEW_SITE_TEMPLATE_PATH."/js/jquery.scrollbar.js");
?>

<?$APPLICATION->IncludeComponent(
	"itsfera:new_user_coupon",
	"",
Array(),
false
);?>


<?
/**
 * дальше старый шаблон
 */
?>


  <script>
      mht.regions = <?
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
        echo WP::js($regions);
      ?>;
  </script>


<?/*
if ($APPLICATION->GetCurPage()=='/magaziny/index.php'):?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<?endif
*/
?>


</div>
</div>

<script src="https://www.artfut.com/static/tagtag.min.js?campaign_code=b4b564561f" async onerror='var self = this;window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers=ADMITAD.Helpers||{},ADMITAD.Helpers.generateDomains=function(){for(var e=new Date,n=Math.floor(new Date(2020,e.getMonth(),e.getDate()).setUTCHours(0,0,0,0)/1e3),t=parseInt(1e12*(Math.sin(n)+1)).toString(30),i=["de"],o=[],a=0;a<i.length;++a)o.push({domain:t+"."+i[a],name:t});return o},ADMITAD.Helpers.findTodaysDomain=function(e){function n(){var o=new XMLHttpRequest,a=i[t].domain,D="https://"+a+"/";o.open("HEAD",D,!0),o.onload=function(){setTimeout(e,0,i[t])},o.onerror=function(){++t<i.length?setTimeout(n,0):setTimeout(e,0,void 0)},o.send()}var t=0,i=ADMITAD.Helpers.generateDomains();n()},window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers.findTodaysDomain(function(e){if(window.ADMITAD.dynamic=e,window.ADMITAD.dynamic){var n=function(){return function(){return self.src?self:""}}(),t=n(),i=(/campaign_code=([^&]+)/.exec(t.src)||[])[1]||"";t.parentNode.removeChild(t);var o=document.getElementsByTagName("head")[0],a=document.createElement("script");a.src="https://www."+window.ADMITAD.dynamic.domain+"/static/"+window.ADMITAD.dynamic.name.slice(1)+window.ADMITAD.dynamic.name.slice(0,1)+".min.js?campaign_code="+i,o.appendChild(a)}});'></script>


</body>
</html>