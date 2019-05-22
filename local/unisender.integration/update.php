<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

//добавляем перенаправление в админке
$local_module_ID = 'local:unisender';
if ( ! CUrlRewriter::GetList(array("ID" => $ID))) {

    CUrlRewriter::Add(array(
        "CONDITION" => "#^/bitrix/admin/unisender_export_coupon.php#",
        "PATH"      => '/local/unisender.integration/unisender_export_coupon.php',
        "ID"        => $ID,
    ));
}

//добавляем пункт меню в Сервисы/UniSender/
\Bitrix\Main\EventManager::getInstance()->addEventHandler('main', 'OnBuildGlobalMenu',
    function (&$arGlobalMenu, &$arModuleMenu) {

        foreach ($arModuleMenu as $sKey => &$arVal) {

            if (($arVal['parent_menu'] == 'global_menu_services') && ($arVal['section'] == 'unisender.integration')) {

                $arVal['items'][] = array(
                    'text'     => 'Экспорт пользователей дисконтных карт',
                    'title'    => 'Экспорт пользователей дисконтных карт',
                    'url'      => 'unisender_export_coupon.php',
                    'more_url' =>
                        array(
                            0 => 'unisender_export_coupon.php',
                        ),
                );
            }
        }
    });