<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

?>

    <!--===================================================== Footer -->
    <footer class="layout-footer">
        <div class="container">
            <div class="row">

                <!-- Logo -->
                <div class="col-1-6 col-xs-5-6 xs-align-center">
                    <a href="#" class="logo si-jump">
                        <img class="hidden-xs" src="<?=SITE_TEMPLATE_PATH?>/images/logo-light.png" alt=" ">
                        <img class="visible-xs" src="<?=SITE_TEMPLATE_PATH?>/images/logo-light-mob.png" alt=" ">
                    </a>
                    <div class="schedule">

                        <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_TEMPLATE_PATH."/include/schedule.php"
                        )
                        );?>

                    </div>
                </div>


               <div class="col-1-12 col-xs-1-6">
                    <img class="hidden-xs" src="<?=SITE_TEMPLATE_PATH?>/images/blackhole.png" alt=" ">
                   <!-- <img class="visible-xs mht-mob" src="<?=SITE_TEMPLATE_PATH?>/images/mht-mob.png" alt=" "> -->
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
							<a href="https://www.facebook.com/MosHozTorg" class="light"><? echo file_get_contents($CurPath.'/svg/face.svg');?></a>
                            <a href="https://www.ok.ru/group/54890146037790" class="light"><? echo file_get_contents($CurPath.'/svg/ok.svg'); ?></a>
                            <a href="https://instagram.com/moshoztorg/" class="light"><? echo file_get_contents($CurPath.'/svg/insta.svg'); ?></a>
                            <a href="https://www.vk.com/moshoztorg" class="light"><? echo file_get_contents($CurPath.'/svg/vk.svg'); ?></a>
                        </div>

                       <div class="social-card">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/card/visa.svg" alt=" ">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/card/master.svg" alt=" ">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/card/mir.svg" alt=" ">
                           <!-- <img src="<?=SITE_TEMPLATE_PATH?>/images/card/yandex.png" alt=" ">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/card/wm.png" alt=" ">-->
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
                    <a target="_blank" href="https://www.facebook.com/MosHozTorg" class="dark"><? echo file_get_contents($CurPath.'/svg/face2.svg'); ?></a>
                    <a href="https://www.ok.ru/group/54890146037790" class="dark"><? echo file_get_contents($CurPath.'/svg/ok2.svg'); ?></a>
                    <a href="https://instagram.com/moshoztorg/" class="dark"><? echo file_get_contents($CurPath.'/svg/insta2.svg'); ?></a>
                    <a href="https://www.vk.com/moshoztorg" class="dark"><? echo file_get_contents($CurPath.'/svg/vk2.svg'); ?></a>
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
                                   placeholder="Ваше имя" required>
                        </div>

                        <div class="col-1">
                            <input type="tel" name="client_phone" class="client-phone"
                                   placeholder="Ваш телефон" required>
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



        <!--============================================== text modal 1 -->
        <div class="si-modal text-modal text-modal-1">
            <a href="#" class="si-close">
                <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
            </a>

            <div class="modal-container">

                <div class="modal-form-title align-center">
                    Согласие на обработку персональных данных
                </div>

                <div class="modal-text-block">
                    <p>
                        Настоящим в&nbsp;соответствии с&nbsp;Федеральным законом №152&#8209;ФЗ
                        «О&nbsp;персональных данных» от&nbsp;27.07.2006 года свободно, своей волей и&nbsp;в&nbsp;своём
                        интересе выражаю своё безусловное согласие на&nbsp;обработку моих
                        персональных данных
                        НАЗВАНИЕ КОМПАНИИ,
                        зарегистрированным в&nbsp;соответствии с&nbsp;законодательством&nbsp;РФ по&nbsp;адресу:
                        АДРЕС КОМПАНИИ
                        (далее по&nbsp;тексту&nbsp;- Оператор).
                    </p>

                    <p>
                        Персональные данные&nbsp;- любая информация, относящаяся к&nbsp;определённому
                        или&nbsp;определяемому на&nbsp;основании такой информации физическому лицу.
                    </p>

                    <p>
                        Настоящее Согласие выдано мною на&nbsp;обработку следующих персональных
                        данных:
                    </p>

                    <ul class="marked">
                        <li>
                            Имя;
                        </li>
                        <li>
                            Телефон;
                        </li>
                        <li>
                            E-mail;
                        </li>
                        <li>
                            Комментарий.
                        </li>
                    </ul>

                    <p>
                        Согласие дано Оператору для&nbsp;совершения следующих действий с&nbsp;моими
                        персональными
                        данными с&nbsp;использованием средств автоматизации и/или&nbsp;без&nbsp;использования
                        таких
                        средств: сбор, систематизация, накопление, хранение, уточнение (обновление,
                        изменение),
                        использование, обезличивание, передача третьим лицам для&nbsp;указанных ниже
                        целей,
                        а&nbsp;также осуществление любых иных действий, предусмотренных действующим
                        законодательством&nbsp;РФ, как&nbsp;неавтоматизированными, так&nbsp;и&nbsp;автоматизированными
                        способами.
                    </p>

                    <p>
                        Данное согласие даётся Оператору и&nbsp;третьему лицу(&#8209;ам)
                        ТРЕТЬИ ЛИЦА
                        для&nbsp;обработки моих персональных данных в&nbsp;следующих целях:
                    </p>

                    <ul class="marked">
                        <li>
                            предоставление мне услуг/работ;
                        </li>
                        <li>
                            направление в&nbsp;мой адрес уведомлений, касающихся предоставляемых
                            услуг/работ;
                        </li>
                        <li>
                            подготовка и&nbsp;направление ответов/коммерческих предложений на&nbsp;мои
                            запросы;
                        </li>
                        <li>
                            направление в&nbsp;мой адрес информации, в&nbsp;том числе рекламной,
                            о&nbsp;мероприятиях/товарах/услугах/работах Оператора.
                        </li>
                    </ul>

                    <p>
                        Настоящее согласие действует до&nbsp;момента его&nbsp;отзыва путём
                        направления соответствующего
                        уведомления на&nbsp;электронный адрес
                        <a href="mailto:">ЕМЕЙЛ</a>.
                        В&nbsp;случае отзыва мною согласия на&nbsp;обработку персональных данных
                        Оператор вправе
                        продолжить обработку персональных данных без&nbsp;моего согласия при&nbsp;наличии
                        оснований,
                        указанных в&nbsp;пунктах 2&#8209;11 части&nbsp;1 статьи&nbsp;6, части&nbsp;2
                        статьи&nbsp;10
                        и&nbsp;части&nbsp;2 статьи&nbsp;11 Федерального закона №152&#8209;ФЗ
                        «О&nbsp;персональных данных» от&nbsp;26.06.2006&nbsp;г.
                    </p>
                </div>

            </div>
        </div>

    </div>



</div>

<script type="text/javascript">
    var template_url = '<?=SITE_TEMPLATE_PATH?>';
</script>

<script>
    var mht = {};
</script>

<!-- Inlcude jQuery framework + jQuery migrate -->
<?
	$oAsset->addJs(SITE_TEMPLATE_PATH."/js/jquery-1.9.1.min.js");
	$oAsset->addJs(SITE_TEMPLATE_PATH."/js/jquery-migrate-1.4.1.min.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");

?>

<!-- IE -->
<!--[if IE]>
<script src="<?=SITE_TEMPLATE_PATH?>/js/html5shiv.js"></script> <![endif]-->

<!-- JS Scripts -->
<?
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/plugins-all.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/jquery.easing.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/smooth-scroll-1.4.4.min.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/jquery.scrollbar.js");
    $oAsset->addJs(SITE_TEMPLATE_PATH."/js/fullpagemenu.js");
?>

<?$APPLICATION->IncludeComponent(
	"itsfera:new_user_coupon",
	"",
Array(),
false
);?>


<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>



<!-- custom scripts -->
<?
	$oAsset->addJs(SITE_TEMPLATE_PATH."/js/main.js");
	$oAsset->addJs(SITE_TEMPLATE_PATH."/js/share.js");
?>

<?php //include('si-engine.php'); ?>

<script src="https://www.artfut.com/static/tagtag.min.js?campaign_code=b4b564561f" async onerror='var self = this;window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers=ADMITAD.Helpers||{},ADMITAD.Helpers.generateDomains=function(){for(var e=new Date,n=Math.floor(new Date(2020,e.getMonth(),e.getDate()).setUTCHours(0,0,0,0)/1e3),t=parseInt(1e12*(Math.sin(n)+1)).toString(30),i=["de"],o=[],a=0;a<i.length;++a)o.push({domain:t+"."+i[a],name:t});return o},ADMITAD.Helpers.findTodaysDomain=function(e){function n(){var o=new XMLHttpRequest,a=i[t].domain,D="https://"+a+"/";o.open("HEAD",D,!0),o.onload=function(){setTimeout(e,0,i[t])},o.onerror=function(){++t<i.length?setTimeout(n,0):setTimeout(e,0,void 0)},o.send()}var t=0,i=ADMITAD.Helpers.generateDomains();n()},window.ADMITAD=window.ADMITAD||{},ADMITAD.Helpers.findTodaysDomain(function(e){if(window.ADMITAD.dynamic=e,window.ADMITAD.dynamic){var n=function(){return function(){return self.src?self:""}}(),t=n(),i=(/campaign_code=([^&]+)/.exec(t.src)||[])[1]||"";t.parentNode.removeChild(t);var o=document.getElementsByTagName("head")[0],a=document.createElement("script");a.src="https://www."+window.ADMITAD.dynamic.domain+"/static/"+window.ADMITAD.dynamic.name.slice(1)+window.ADMITAD.dynamic.name.slice(0,1)+".min.js?campaign_code="+i,o.appendChild(a)}});'></script>




</body>
</html>