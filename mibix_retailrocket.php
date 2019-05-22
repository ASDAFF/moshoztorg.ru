<?

$SHOP_ID = 2;

/**
 * ============== Параметры ниже не рекомендуется редактировать ========================
 */
set_time_limit(0);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);
define("MIBIX_DEBUG_YAMEXPORT",false);


    $MODULE_ID = "mibix.yamexport";
    $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
    if (!CModule::IncludeModule($MODULE_ID) || !CModule::IncludeModule("iblock")) return;

// Получаем значения и вызываем функцию генерации XML-файла
    $YAM_EXPORT = CMibixYandexExport::get_step_settings($SHOP_ID);

    if(is_array($YAM_EXPORT))
    {
        $YAM_EXPORT_PATH = $DOCUMENT_ROOT . $YAM_EXPORT["step_path"]; // путь сохранения экспортируемого xml-файл


        CMibixYandexExport::GetYMLToFile($YAM_EXPORT_PATH, $SHOP_ID);

    }
    else
    {
        echo "Error config steps load. Please check fill of the limits for the shop.";
    }

    require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");




?>


