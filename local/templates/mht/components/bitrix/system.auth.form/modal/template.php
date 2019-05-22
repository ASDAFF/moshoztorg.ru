<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
    //путь для подключения файлов include-ом
    $CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;
?>

        <!--============================================== authorization modal -->
        <div class="si-modal authorization-modal">
            <a href="#" class="si-close">
                <? echo file_get_contents($CurPath.'/svg/close.svg'); ?>
            </a>

            <div class="modal-container align-center">

                <div class="modal-form-title">
                    ВОЙТИ НА САЙТ
                </div>
                <div class="modal-form-social">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "header",
                        array(
                            "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
                            "AUTH_URL"=>$arResult["AUTH_URL"],
                            "POST"=>$arResult["POST"],
                            "POPUP"=>"Y",
                            "SUFFIX"=>"form",
                        ),
                        $component,
                        array()
                    );
                    ?>
                    <hr>
                </div>
                <form method="post" class="send-form" autocomplete="off">

				<?
					if($arResult["BACKURL"] <> ''){
						?>
							<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
						<?
					}

					foreach ($arResult["POST"] as $key => $value){
						?>
							<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
						<?
					}
				?>
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="AUTH" />


                    <div class="row">
                        <div class="col-1">
                            <input type="text" name="client_name" class="client-name"
                                   placeholder="Логин/email/телефон"  <?=(isset($_COOKIE['CLIENT_NAME']) ? 'value="'.$_COOKIE['CLIENT_NAME'].'"' : '')?>>
                        </div>

                        <div class="col-1">
                            <input type="password" name="client_pass" class="client-pass"
                                   placeholder="Пароль" <?=(isset($_COOKIE['CLIENT_PASS']) ? 'value="'.$_COOKIE['CLIENT_PASS'].'"' : '')?>>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1-2">
                            <div class="align-left">
                                <label class="checkbox-label form-remember-me checked">
                                    <input type="checkbox" class="rememberme" name="client_remember" checked />
                                    Запомнить меня
                                </label>
                            </div>
                        </div>
                        <div class="col-1-2 align-right">
                            <a href="https://<?=SITE_SERVER_NAME?>/personal/forgot/" class="primary">Забыли
                                пароль?</a>
                            <a href="https://<?=SITE_SERVER_NAME?>/personal/register/" class="primary">Регистрация</a>
                        </div>
                    </div>


                    <input type="hidden" name="send_type" class="send-type" value="1">
                    <input type="hidden" name="send_extra" class="send-extra" value="1">
                    <input type="hidden" name="key" value="<?php echo $_SESSION['sf_key'] ?>">
                    <?php //Поле выше для защиты формы от спам-ботов ?>

                    <div class="btn-holder">
                        <button type="submit" class="btn">Войти</button>
                        <!--<div class="g-recaptcha" id="g-recaptcha"></div>-->
                    </div>

                </form>

            </div>
        </div>
