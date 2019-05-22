<?
set_time_limit(0);
ob_implicit_flush(1);
$module_id = 'unisender.integration';

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/' . $module_id . '/include.php';

$entity_id = "USER";

use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale;
use Bitrix\Main\Loader;

Loader::includeModule("sale");
Loader::includeModule("catalog");
Loader::includeModule("iblock");


IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/user_admin.php");

global $APPLICATION;
$APPLICATION->SetTitle("Экспорт пользователей дисконтных карт");

$API_KEY = COption::GetOptionString($module_id, 'UNISENDER_API_KEY');

if ($API_KEY !== '') {
    $API = new UniAPI($API_KEY);

$sTableID = "tbl_user";

$oSort = new CAdminSorting($sTableID, "DATE_REGISTER", "desc");
$lAdmin = new CAdminUiList($sTableID, $oSort);

$arFilterFields = Array(
	"find",
	"find_date_register",
	//"find_active",
	"find_group_id"
);

$lAdmin->InitFilter($arFilterFields);

function CheckFilter($FilterArr)
{
	global $strError;
	foreach($FilterArr as $f)
		global ${$f};

	$str = "";

	$strError .= $str;
	if(strlen($str)>0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}

$arFilter = Array();


/* Prepare data for new filter */
$queryObject = CGroup::GetDropDownList("AND ID!=2");
$listGroup = array();
while($group = $queryObject->fetch())
	$listGroup[$group["REFERENCE_ID"]] = $group["REFERENCE"];

$filterFields = array(
	array(
		"id" => "ORDERS",
		"name" => "Заказов",
		"type" => "number",
		"filterable" => "",
		"default" => true
	),
	array(
		"id" => "DATE_REGISTER",
		"name" => GetMessage("DATE_REGISTER"),
		"type" => "date",
	),
	array(
		"id" => "GROUPS_ID",
		"name" => GetMessage("F_GROUP"),
		"type" => "list",
		"items" => $listGroup,
		"params" => array("multiple" => "Y"),
		"filterable" => ""
	),
);

$USER_FIELD_MANAGER->AdminListAddFilterFieldsV2($entity_id, $filterFields);
$arFilter = array();
$lAdmin->AddFilter($filterFields, $arFilter);

$USER_FIELD_MANAGER->AdminListAddFilterV2($entity_id, $arFilter, $sTableID, $filterFields);

$userQuery = new Query(UserTable::getEntity());

$userQuery->setSelect(array(
        "DATE_REGISTER", "ORDERS",
        'ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'WORK_POSITION', 'LOGIN', 'EMAIL'
));


$sortBy = strtoupper($by);
$sortOrder = strtoupper($order);


$userQuery->setOrder(array($sortBy => $sortOrder));
$userQuery->countTotal(true);

$userQuery->where("ACTIVE", "=", "Y");
$userQuery->where("EMAIL", "!=", false);

$filterOption = new Bitrix\Main\UI\Filter\Options($sTableID);
$filterData = $filterOption->getFilter($filterFields);

if (!empty($filterData["FIND"]))
{
	$userQuery->setFilter(\Bitrix\Main\UserUtils::getAdminSearchFilter(array("FIND" => $filterData["FIND"])));
}

foreach ($arFilter as $key => $value) {
    if ($value == '' )
        unset($arFilter[$key]);
}

if (isset($arFilter["DATE_REGISTER_1"]))
{
	$userQuery->where("DATE_REGISTER", ">=", new DateTime($arFilter["DATE_REGISTER_1"]));
}
if (isset($arFilter["DATE_REGISTER_2"]))
{
	$userQuery->where("DATE_REGISTER", "<=", new DateTime($arFilter["DATE_REGISTER_2"]));
}

if (isset($arFilter["GROUPS_ID"]))
{
	if (is_numeric($arFilter["GROUPS_ID"]) && intval($arFilter["GROUPS_ID"]) > 0)
		$arFilter["GROUPS_ID"] = array($arFilter["GROUPS_ID"]);
	$listGroupId = array();
	foreach ($arFilter["GROUPS_ID"] as $groupId)
		$listGroupId[intval($groupId)] = intval($groupId);

	$userGroupQuery = UserGroupTable::query();
	$userGroupQuery->where("USER_ID", new SqlExpression("%s"));
	$userGroupQuery->whereIn("GROUP_ID", $listGroupId);
	$nowTimeExpression = new SqlExpression(
		$userGroupQuery->getEntity()->getConnection()->getSqlHelper()->getCurrentDateTimeFunction());
	$userGroupQuery->where(Query::filter()->logic("or")
		->whereNull("DATE_ACTIVE_FROM")
		->where("DATE_ACTIVE_FROM", "<=", $nowTimeExpression)
	);
	$userGroupQuery->where(Query::filter()->logic("or")
		->whereNull("DATE_ACTIVE_TO")
		->where("DATE_ACTIVE_TO", ">=", $nowTimeExpression)
	);
}


    //Добавляем количество заказов
  $userQuery->registerRuntimeField('ORDER_ELEMENT', [
            'data_type' => \Bitrix\Sale\Internals\OrderTable::getEntity(),
            'reference' => [
                '=this.ID' => 'ref.USER_ID',
            ],
        ]
    );

//тут оплаченные заказы
//    $userQuery->setFilter([
//            'ORDER_ELEMENT.PAYED' => "Y"
//        ]);

    $userQuery->registerRuntimeField('ORDERS', [
        'data_type'=>'integer',
        'expression' => ['COUNT(%s)', 'ORDER_ELEMENT.ID']
    ]);

    $userQuery->setGroup('ID');



$ignoreKey = array("NAME", "CHECK_SUBORDINATE", "CHECK_SUBORDINATE_AND_OWN", "NOT_ADMIN", "INTRANET_USERS", "GROUPS_ID",
	"KEYWORDS", "TIMESTAMP_1", "TIMESTAMP_2", "LAST_LOGIN_1", "LAST_LOGIN_2","DATE_REGISTER_1","DATE_REGISTER_2"
);
foreach ($arFilter as $filterKey => $filterValue)
{
	if (!in_array($filterKey, $ignoreKey))
	{
		$userQuery->addFilter($filterKey, $filterValue);
	}
}




//тут ответ на аякс запрос
    if ($_REQUEST["action"] == "js_send" && check_bitrix_sessid()) {

        $perPage   = 100;

        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");


        $_POST['groups'][] = 3;

        $_POST['phone'] = 'PERSONAL_PHONE';

        $_POST['fields'] =
            array(
                'Name'   => 'NAME',
                'coupon' => 'UF_TOKEN',
            );


        $fieldIterator = 8;
        $list_id       = (int)$APPLICATION->get_cookie("UNISENDER_LIST_ID");
        $response      = array();

        $params                   = array();
        $params['double_optin']   = 1;
        $params['field_names[0]'] = 'email';
        $params['field_names[1]'] = 'email_status';
        $params['field_names[2]'] = 'email_add_time';
        $params['field_names[3]'] = 'email_list_ids';

        if ( ! empty($_POST['phone'])) {
            $params['field_names[4]'] = 'phone';
            $params['field_names[5]'] = 'phone_status';
            $params['field_names[6]'] = 'phone_add_time';
            $params['field_names[7]'] = 'phone_list_ids';
        }

        $fieldId = $fieldIterator;
        foreach ($_POST['fields'] as $name => $userField) {
            $params['field_names[' . $fieldId . ']'] = $name;
            $fieldId++;
        }

        $arIDs = array();

        $IBLOCK_ID = getIBlockIdByCode("discount_cards");
        $arSelect  = Array(
            "ID",
            "NAME",
            "PROPERTY_USER_ID",
            "PROPERTY_PERCENT",
            "PROPERTY_CARDTYPE",
            "PROPERTY_TOTAL",
        );
        $arFilter  = Array(
            "IBLOCK_ID"         => $IBLOCK_ID,
            "!PROPERTY_USER_ID" => false,
            "PROPERTY_CARDTYPE" => 317085,
        );


        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields                                   = $ob->GetFields();
            $arIDs[$arFields['PROPERTY_USER_ID_VALUE']] = $arFields['PROPERTY_USER_ID_VALUE'];
        }


        $nav = new PageNavigation("pages-user-admin-test");
        $nav->allowAllRecords(true)->setPageSize($perPage)->setCurrentPage($_REQUEST["page"] + 1);

        $userQuery->setLimit($nav->getLimit());
        $userQuery->setOffset($nav->getOffset());

        $result = $userQuery->exec();

        $nav->setRecordCount($result->getCount());

        $nEmailsSent  = $_REQUEST["page"] * $perPage;
        $nEmailsError = 0;
        $nEmailsTotal = $result->getCount();

        $i = 0;
        while ($user = $result->Fetch()) {

            if ( ! $arIDs[$user['ID']]) {

                $currId = 'data[' . $i . ']';

                $data = array(
                    $currId . '[0]' => $user['EMAIL'],
                    $currId . '[1]' => 'active',
                    $currId . '[2]' => ConvertDateTime($user['DATE_REGISTER'], 'YYYY-MM-DD HH:MI:SS'),
                    $currId . '[3]' => $list_id,
                );

                $data = array_merge($data, array(
                    $currId . '[4]' => $user['PERSONAL_PHONE'],
                    $currId . '[5]' => 'active',
                    $currId . '[6]' => $data[$currId . '[2]'],
                    $currId . '[7]' => $list_id,
                ));


                $fieldId = $fieldIterator;
                foreach ($_POST['fields'] as $name => $userField) {
                    if ( ! empty($user[$userField])) {
                        $data[$currId . '[' . $fieldId . ']'] = $user[$userField];
                    }

                    if ($name == 'coupon') {

                        $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
                        $discount_id = (int)$APPLICATION->get_cookie("UNISENDER_DISCOUNT_ID");

                        $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add(array(
                            'DISCOUNT_ID' => $discount_id,
                            'ACTIVE'      => 'Y',
                            'COUPON'      => $coupon,
                            'TYPE'        => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                            'MAX_USE'     => 1,
                            'USER_ID'     => $user['ID'],
                            'DESCRIPTION' => \Bitrix\Sale\Internals\DiscountTable::getById($discount_id)->getFields('NAME'),
                        ));

                        $data[$currId . '[' . $fieldId . ']'] = $coupon;
                    }

                    $fieldId++;
                }

                $params = array_merge($params, $data);

                $i++;
            }

        }

        if ( ! empty($params)) {
            $result = $API->importContacts($params);
            if ($result === false) {
                $API->showError();
            } else {
                unset($params['data']);
                $i = 0;
                foreach ($result as $name => $value) {
                    if ( ! isset($response[$name])) {
                        $response[$name] = $value;
                    } else {
                        $response[$name] += $value;
                    }
                }
            }
        }


        if ( ! $API->getError()) {

            $total      = (int)$APPLICATION->get_cookie("UNISENDER_RESPONSE_TOTAL") + $response['total'];
            $inserted   = (int)$APPLICATION->get_cookie("UNISENDER_RESPONSE_INSERTED") + $response['inserted'];
            $updated    = (int)$APPLICATION->get_cookie("UNISENDER_RESPONSE_UPDATED") + $response['updated'];
            $new_emails = (int)$APPLICATION->get_cookie("UNISENDER_RESPONSE_NEW_EMAILS") + $response['new_emails'];

            $tmp = ($nav->getCurrentPage() * $perPage) > $nEmailsTotal ? $nEmailsTotal : ($nav->getCurrentPage() * $perPage);

            if ($nav->getCurrentPage() < $nav->getPageCount()) {
                $textMessage = '<p>' . "Идет отправка данных." . '<br>' . "Не уходите со страницы до окончания процесса." . '</p>'
                               . '#PROGRESS_BAR#'
                               . '<p>' . "Обработано пользователей:" . ' <b>' . $tmp . '</b> ' . "из" . ' <b>' . $nEmailsTotal . '</b></p>'
                               . '<p>' . "Добавлено" . ': <b>' . $inserted . '</b></p>'
                               . '<p>' . "Обновлено" . ': <b>' . $updated . '</b></p>'
                               . '<p>' . "Новых Email" . ': <b>' . $new_emails . '</b></p>';

                $arButtons = array(
                    array(
                        "ID"      => "btn_stop",
                        "VALUE"   => "Остановить",
                        "ONCLICK" => "Stop()",
                    ),
                    array(
                        "ID"      => "btn_cont",
                        "VALUE"   => "Продолжить",
                        "ONCLICK" => "Cont(" . $nav->getCurrentPage() . ")",
                    ),
                );
            } else {
                $textMessage = '<p>Данные успешно отправлены</p>'
                               . '#PROGRESS_BAR#'
                               . '<p>' . "Обработано пользователей:" . ' <b>' . $tmp . '</b> ' . "из" . ' <b>' . $nEmailsTotal . '</b></p>'
                               . '<p>' . "Добавлено" . ': <b>' . $inserted . '</b></p>'
                               . '<p>' . "Обновлено" . ': <b>' . $updated . '</b></p>'
                               . '<p>' . "Новых Email" . ': <b>' . $new_emails . '</b></p>';
                $arButtons   = [];
            }

            //тут ответ
            CAdminMessage::ShowMessage(array(
                "DETAILS"        => $textMessage,
                "HTML"           => true,
                "TYPE"           => "PROGRESS",
                "PROGRESS_TOTAL" => $nEmailsTotal,
                "PROGRESS_VALUE" => $tmp,
                "BUTTONS"        => $arButtons,
            ));
            if ($nav->getCurrentPage() < $nav->getPageCount()) {
                ?>
                <script>
                    MoveProgress(<?=$nav->getCurrentPage()?>);
                </script><?
            } else {
                echo '<span class="notetext">Экспорт завершён</span><br/>';
                echo '<a href="https://cp.unisender.com/ru/v5/contact/subscriber/list/' . $list_id . '" target="_blank">Перейти к списку контактов на UniSender</a>';

//кудато поставить
//            if ( ! empty($response['logs'])) {
//                echo '<p><b>При экспорте произошли следующие ошибки:</b><ul>';
//                foreach ($response['logs'] as $log) {
//                    echo '<li>' . $log . '</li>';
//                }
//                echo '</ul></p>';
//            }
            }


            $APPLICATION->set_cookie("UNISENDER_RESPONSE_INSERTED", $inserted, time() + 60 * 60 * 24 * 30 * 12 * 2,
                "/");
            $APPLICATION->set_cookie("UNISENDER_RESPONSE_UPDATED", $updated, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
            $APPLICATION->set_cookie("UNISENDER_RESPONSE_NEW_EMAILS", $new_emails, time() + 60 * 60 * 24 * 30 * 12 * 2,
                "/");
            $APPLICATION->set_cookie("UNISENDER_RESPONSE_TOTAL", $total, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");

        }


        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
    }



    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

    if ($_REQUEST['action'] == "send") {

        $APPLICATION->set_cookie("UNISENDER_LIST_ID", $_REQUEST['list_id'], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $APPLICATION->set_cookie("UNISENDER_DISCOUNT_ID", $_REQUEST['discount_id'], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $APPLICATION->set_cookie("UNISENDER_RESPONSE_INSERTED", 0, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $APPLICATION->set_cookie("UNISENDER_RESPONSE_UPDATED", 0, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $APPLICATION->set_cookie("UNISENDER_RESPONSE_NEW_EMAILS", 0, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $APPLICATION->set_cookie("UNISENDER_RESPONSE_TOTAL", 0, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");


        ?>
        <div id="progress_message">
        </div>
        <script>
            var stop = false;

            function Stop() {
                stop = true;
                document.getElementById('btn_stop').disabled = true;
                document.getElementById('btn_cont').disabled = false;
            }

            function Cont(page) {
                stop = false;
                document.getElementById('btn_stop').disabled = false;
                document.getElementById('btn_cont').disabled = true;
                MoveProgress(page);
            }

            function MoveProgress(page) {
                if (stop)
                    return;

                var url = '<?=$APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&page=' + page + '&<?echo bitrix_sessid_get()?>&action=js_send';
                ShowWaitWindow();
                BX.ajax.post(
                    url,
                    null,
                    function (result) {
                        CloseWaitWindow();
                        document.getElementById('progress_message').innerHTML = result;
                    }
                );
            }

            setTimeout('MoveProgress()', 100);
        </script>
        <?

    } else {

        echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
        echo '<script type="text/javascript" src="/bitrix/js/' . $module_id . '/js.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/unisender.integration.css">';

        $lists      = $API->getLists();
        $uniFields  = $API->getFields();
        $userFields = Unisender::getUserFields();


        $discountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
            'select' => array('ID', 'NAME'),
            'filter' => array(
                'ACTIVE' => 'Y',
            )
        ));
        $arDiscount       = $discountIterator->fetchAll();


        if ( ! is_array($lists) || ! is_array($uniFields)) {
            $API->showError();
        } else {


            $nav = new PageNavigation("pages-user-admin-test");
            $nav->setPageSize($lAdmin->getNavSize());
            $nav->initFromUri();
            $userQuery->setLimit($nav->getLimit());
            $userQuery->setOffset($nav->getOffset());

            $result = $userQuery->exec();


            function setHeaderColumn (CAdminUiList $lAdmin)
            {
                $arHeaders = array(
                    array("id" => "ID", "content" => "ID", "sort" => "id", "default" => true, "align" => "right"),
                    array("id" => "LOGIN", "content" => GetMessage("LOGIN"), "sort" => "login", "default" => true),
                    array("id" => "DATE_REGISTER", "content" => GetMessage("DATE_REGISTER"), "sort" => "date_register"),
                    array("id" => "NAME", "content" => GetMessage("NAME"), "sort" => "name", "default" => true),
                    array(
                        "id"      => "LAST_NAME",
                        "content" => GetMessage("LAST_NAME"),
                        "sort"    => "last_name",
                        "default" => true
                    ),
                    array("id" => "EMAIL", "content" => GetMessage('EMAIL'), "sort" => "email", "default" => true),
                    array("id" => "ORDERS", "content" => "Заказов", "sort" => "orders", "default" => true),
                );

                $lAdmin->addHeaders($arHeaders);
            }

            setHeaderColumn($lAdmin);
            $nav->setRecordCount($result->getCount());
            $lAdmin->setNavigation($nav, GetMessage("MAIN_USER_ADMIN_PAGES"), false);

            while ($userData = $result->fetch()) {
                $userId = $userData["ID"];
                $row    =& $lAdmin->addRow($userId, $userData);


                $row->addViewField("ID",
                    "<a href='user_edit.php?lang=" . LANGUAGE_ID . "&ID=" . $userId . "' title='" . GetMessage("MAIN_EDIT_TITLE") . "'>" . $userId . "</a>");

                $row->addViewField("LOGIN",
                    "<a href='user_edit.php?lang=" . LANGUAGE_ID . "&ID=" . $userId . "' title='" . GetMessage("MAIN_EDIT_TITLE") . "'>" . HtmlFilter::encode($userData["LOGIN"]) . "</a>");
                $row->addViewField("EMAIL", TxtToHtml($userData["EMAIL"]));

            }


            require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/prolog_admin_after.php");


            $lAdmin->DisplayFilter($filterFields);
            $lAdmin->DisplayList();


            ?>
            <br>
            <div class="main-grid">
            <form method="post" id="export_form" action="unisender_export_coupon.php?action=send">


                <div class="main-grid-wrapper">


                    <fieldset>
                        <legend>Список в UniSender</legend>
                        <div class="uni_fieldset_content">
                            <table class="uni_fields_table">
                                <tr>
                                    <td width="200px">
                                        Список контактов:
                                    </td>
                                    <td>
                                        <select name="list_id">
                                            <? foreach ($lists as $list): ?>
                                                <option value="<?= $list['id'] ?>"><?= $list['title'] ?></option>
                                            <? endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <div class="uni_notetext">
                                <div>Создать список контактов в кабинете UniSender по ссылке <a
                                        href="https://cp.unisender.com/ru/v5/contact/field/list" target="_blank"> по
                                        ссылке </a></div>
                                <div style="font-style: italic;">После создания необходимо обновить страницу</div>
                            </div>
                        </div>
                    </fieldset>

                </div>
                <br>
                <div class="main-grid-wrapper">
                    <fieldset>
                        <legend>Скидка</legend>
                        <div class="uni_fieldset_content">
                            <table class="uni_fields_table">
                                <tr>
                                    <td width="200px">
                                        Список скидок:
                                    </td>
                                    <td>
                                        <select name="discount_id">
                                            <? foreach ($arDiscount as $list): ?>
                                                <option value="<?= $list['ID'] ?>"><?= $list['NAME'] ?></option>
                                            <? endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </fieldset>


                </div>

                    <dl class="submit_bt">
                        <dt><input type="submit" name="export" value="Перенести"/></dt>
                    </dl>
                </form>
            </div>
            <?

        }
    }

} else {
    echo '<span class="errortext">Ошибка</span>';
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");





?>