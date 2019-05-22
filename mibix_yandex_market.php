<?

$SHOP_ID = 1;

/**
 * ============== ��������� ���� �� ������������� ������������� ========================
 */
set_time_limit(0);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);
define("MIBIX_DEBUG_YAMEXPORT",false);

$checkYaMarket = "/home/bitrix/www/yandex_market_bot_was_there.log";

$flagYaMarket = "/home/bitrix/www/yandex_market_in_process.log";

if (file_get_contents($checkYaMarket) && !file_get_contents($flagYaMarket)) {

    unlink($checkYaMarket);

    //������ ���� ������
    file_put_contents($flagYaMarket, date('d.m.Y H:i:s'));

    $MODULE_ID = "mibix.yamexport";
    $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
    if (!CModule::IncludeModule($MODULE_ID) || !CModule::IncludeModule("iblock")) return;

// �������� �������� � �������� ������� ��������� XML-�����
    $YAM_EXPORT = CMibixYandexExport::get_step_settings($SHOP_ID);

    if(is_array($YAM_EXPORT))
    {
        $YAM_EXPORT_PATH = $DOCUMENT_ROOT . $YAM_EXPORT["step_path"]; // ���� ���������� ��������������� xml-����


        CMibixYandexExport::GetYMLToFile($YAM_EXPORT_PATH, $SHOP_ID);

    }
    else
    {
        echo "Error config steps load. Please check fill of the limits for the shop.";
    }

    require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");


    //������� ����
    unlink($flagYaMarket);

}

?>


