<?//define("NO_KEEP_STATISTIC", true);
//define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");

CModule::IncludeModule("iblock");

$data = array();
$user_id = cuser::getid();

switch ($_REQUEST['action']){
    case 'addcard':
        if($_SESSION['addcardcounter'] > 1)
            $data['error'][] = 'Не получилось привязать карту. Напишите в техподдержку в диалоговом окне снизу или позвоните по номеру 8 (800) 550-47-47';
        else {

            if (!$_REQUEST['cardNumber'])
                $data['error'][] = 'Не указан номер карты';
            else {
                $IBLOCK_ID = getIBlockIdByCode("discount_cards");
                //$IBLOCK_ID = 552;
                $arSelect = Array("ID", "NAME", "PROPERTY_USER_ID", "PROPERTY_PHONE", "PROPERTY_FIO");
                $arFilter = Array(
                    "IBLOCK_ID" => $IBLOCK_ID,
                    "NAME" => $_REQUEST['cardNumber'],
                    //"PROPERTY_EMAIL" => $_REQUEST['email'],
                    //"PROPERTY_PHONE" => $_REQUEST['tel'],
                    //"PROPERTY_FIO" => $_REQUEST['fio'],
                );
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                if ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ((int)$arFields['PROPERTY_USER_ID_VALUE'] <= 0) {
                        if (strlen($arFields['PROPERTY_FIO_VALUE']) < 1 && strlen($arFields['PROPERTY_PHONE_VALUE']) < 1)
                            $data['error'][] = 'Карта найдена, но поля Ф.И.О. и Телефон не найдены в базе. Напишите в техподдержку в диалоговом окне снизу или позвоните по номеру 8 (800) 550-47-47.';
                        elseif ($arFields['PROPERTY_USER_ID_VALUE'] != $user_id && $arFields['PROPERTY_USER_ID_VALUE'] > 0)
                            $data['error'][] = 'Карта найдена, но уже привязана к другому пользователю.';
                        elseif ($arFields['PROPERTY_USER_ID_VALUE'] == $user_id)
                            $data['error'][] = 'Карта найдена и уже привязана к пользователю.';
                        if ($arFields['PROPERTY_PHONE_VALUE'] != $_REQUEST['tel'] || $arFields['PROPERTY_FIO_VALUE'] != $_REQUEST['fio']) {
                            if ($arFields['PROPERTY_PHONE_VALUE'] != $_REQUEST['tel'])
                                $field = 'Телефон';
                            elseif ($arFields['PROPERTY_FIO_VALUE'] != $_REQUEST['fio'])
                                $field = 'Ф.И.О.';
                            $data['error'][] = 'Карта найдена, но поле ' . $field . ' не совпадает с указанным.';
                        }
                        if (count($data['error']) == 0) {
                            CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array('USER_ID' => $user_id));
                            $arGroups = CUser::GetUserGroup($user_id);
                            $arGroups[] = 12;
                            CUser::SetUserGroup($user_id, $arGroups);
                            $data['message'] = 'Карта номер ' . $_REQUEST['cardNumber'] . ' успешно привязана к профилю пользователя.';
                        }
                    }
                } else
                    $data['error'][] = 'Карта не найдена. Напишите в техподдержку в диалоговом окне снизу или позвоните по номеру +7 (925) 400-82-00';
            }
            if(count($data['error']) > 0)
                $_SESSION['addcardcounter']++;
        }
        break;
}
echo json_encode($data);

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");