<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Mail\Event;


$request = Context::getCurrent()->getRequest();

$APPLICATION->RestartBuffer();



/**
 * обработаем авторизацию
 */
if ( $request->get("AUTH_FORM") == 'Y' && $request->get("TYPE") == 'AUTH' ) {

    $login = $request->get("client_name");
    $password = $request->get("client_pass");
	$remember = $request->get("client_remember");
	if ($remember) {
		$rememberLetter = "Y";
	} else {
		$rememberLetter = "N";
	}

    if ($login && $password) {

        if (!is_object($USER)) $USER = new CUser;

        $arUser = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID', 'LOGIN'),
            'filter' => array(
                '=ACTIVE' => 'Y',
                array(
                "LOGIC" => "OR",
                "=LOGIN" => $login,
                "=EMAIL" => $login,
                "=PERSONAL_PHONE" => $login,
                "=PERSONAL_MOBILE" => $login,
                "=WORK_PHONE" => $login
                )
            )
        ))->fetch();

        if ( $arUser ) {

            if ( $rememberLetter == "Y"){
             setcookie("CLIENT_NAME", $login, time()+60*60*24*30*12, '/' );
             setcookie("CLIENT_PASS", $password, time()+60*60*24*30*12, '/');
            }

            $arAuthResult = $USER->Login($arUser['LOGIN'], $password, $rememberLetter);
            $APPLICATION->arAuthResult = $arAuthResult;



            if ( $arAuthResult['TYPE'] == 'ERROR' ) {
                die(json_encode(array('success' => 0, 'text' => $arAuthResult['MESSAGE'])));
            } else {
                die(json_encode(array('success' => 1, 'isReload' => true)));
            }

        } else {

             die(json_encode(array('success' => 0, 'text' => "Пользователь не найден")));
        }

        exit;


    } else {
         die(json_encode(array('success' => 0, 'text' => 'Заполните все поля')));
    }
}


/*
 * дальше код от клиента как есть
 */
session_start();
$code_word = 'MHT'; // Кодовое слово для идентификации, если указано, то вставляется в начало темы письма
$email_to = 'order@mht.ru'; // Можно несколько, через запятую
$email_from = 'no-reply@' . preg_replace('/\/+$/', '', $_SERVER['HTTP_HOST']); // E-mail отправителя
$geo = true; // Включить определение города по IP? true - да, false - нет

date_default_timezone_set('Europe/Moscow'); // Определяем часовой пояс - по умолчанию Москва, есть переход на зимнее время

function si_create_message($title, $data) {
    $time = date('d.m.Y в H:i');

    $message = "
			<!doctype html>
				<html>
					<head>
						<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
						<title>$title</title>
						<style>
							div, p, span, strong, b, em, i, a, li, td {
								-webkit-text-size-adjust: none;
							}
							td{vertical-align:middle}
						</style>
					</head>
					
					<body>
						
						<table width='500' cellspacing='0' cellpadding='5' border='1' bordercolor='1' style='border:solid 1px #000;border-collapse:collapse;'>
							<caption align='center' bgcolor='#dededd' border='1' bordercolor='1' style='border:solid 1px #000;border-collapse:collapse;background:#dededd;padding:10px 0'><b>$title</b></caption>";

    foreach ($data as $key => $val) {
        if ($val != '')
            $message .= '<tr><td bgcolor="#efeeee" style="background:#efeeee">' . $key . ':</td><td>' . $val . '</td>';
    }

    $message .= "<tr><td bgcolor='#efeeee' style='background:#efeeee'>Дата:</td><td>$time</td></tr><tr><td bgcolor='#efeeee' style='background:#efeeee'>IP:</td><td>$_SERVER[REMOTE_ADDR]</td></tr>";

    if (file_exists('SxGeo.php')) {
        include 'SxGeo.php';

        $SxGeoCity = new SxGeo('SxGeoCity.dat');
        $ip = $_SERVER['REMOTE_ADDR'];
        $region = $SxGeoCity->getCityFull($ip);
        $regionCity = $region["city"]["name_ru"];

        if ($regionCity) {
            $message .= "<td bgcolor='#efeeee' style='background:#efeeee'>Город:<br /><i>(определён на основе IP)</i></td><td>$regionCity</td></tr>";
        }
    }
    $message .= "</table></body></html>";
    return $message;
}

function si_recaptcha()
{
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $secret = '6Ld5qSQUAAAAAF4uicn7qVj9vQdJBaXa5LQTzAY7';
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if ($responseData->success) {
            return $responseData;
        } else {
            die(json_encode(array('success' => 0, 'text' => 'Подтвердите, что вы не робот')));
        }
    } else {
        die(json_encode(array('success' => 0, 'text' => 'Подтвердите, что вы не робот')));
    }
}

$name = isset($_POST['client_name']) ? $_POST['client_name'] : '';
$phone = isset($_POST['client_phone']) ? $_POST['client_phone'] : '';
$mail = isset($_POST['client_mail']) ? $_POST['client_mail'] : '';
$message = isset($_POST['client_message']) ? $_POST['client_message'] : '';

if (empty($name) || empty($phone))
    die(json_encode(array('success' => 0, 'text' => 'Заполните все поля')));

//Две строчки ниже для защиты форм от спам-ботов
if( empty( $_SESSION['sf_key'] ) || empty( $_POST['key'] ) || $_SESSION['sf_key'] != $_POST['key'] )
	die(json_encode(array('success' => 0, 'text' => 'Пожалуйста, включите Cookie')));

$type = intval($_POST['send_type']);
$extra = $_POST['send_extra'];

$text = '';
$code = 0;

switch ($type) {

    case 1:
        $mail_theme = 'Заявка на консультацию';
        $text .= 'Заявка на консультацию';

        switch ($extra) {
            case 1 :
                $text .= ' с шапки сайта';
                $code = 1;
                break;

            case 2 :
                $text .= ' с подвала сайта';
                $code = 2;
                break;
        }
        break;

    case 2:
        $mail_theme = 'Заявка c блока УТП';
        $text .= 'Заявка c блока УТП';

        switch ($extra) {
            case 1 :
                $text .= ' №1';
                $code = 1;
                break;

            case 2 :
                $text .= ' №2';
                $code = 2;
                break;
        }
        break;
}


$params = array(
    'Тема' => $text,
    'Имя' => $name,
    'Телефон' => $phone,
    'E-mail' => $mail,
    'Вопрос' => nl2br(htmlspecialchars($message)),
    'Статистическая информация о заявке' => $_POST['si_engine'],
    'UTM-метки' => $_POST['si_utm']
);

//recaptcha
//si_recaptcha();

// With file
if ($type == 11 || $type == 12) {

    // File check
    if (empty($_FILES['client_file']['tmp_name'])) {
        die(json_encode(array('success' => 0, 'text' => 'Выберите файл для загрузки')));
    }

    // Size check
    if ($_FILES['client_file']['size'] > 10485760) {
        die(json_encode(array('success' => 0, 'text' => 'Выберите файл размером меньше 10Мб')));
    }

    $fp = fopen($_FILES['client_file']['tmp_name'], "r");

    if (!$fp) {
        die(json_encode(array('success' => 0, 'text' => 'Ошибка при загрузке файла')));
        exit();
    }
    $file = fread($fp, filesize($_FILES['client_file']['tmp_name']));

    $boundary = "--" . md5(uniqid(time()));
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: multipart/mixed; boundary=\"$boundary\"\n";
    $headers .= "From: $email_from\r\n";
    $headers .= "Reply-To: $name\r\n";

    $multipart .= "--$boundary\n";
    $multipart .= "Content-Type: text/html; charset=utf-8\n";
    $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
    $multipart .= si_create_message($mail_theme, $params) . "\n\n";

    $message_part = "--$boundary\n";
    $message_part .= "Content-Type: application/octet-stream\n";
    $message_part .= "Content-Transfer-Encoding: base64\n";
    $message_part .= "Content-Disposition: attachment; filename = \"" . $_FILES['client_file']['name'] . "\"\n\n";
    $message_part .= chunk_split(base64_encode($file)) . "\n";

    $multipart .= $message_part . "--$boundary--\n";

}
else {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: " . $email_from . "\r\n";

    if (!empty($mail)) {
        if (!empty($name)) {
            $headers .= "Reply-To: $name <$mail>\r\n";
        } else {
            $headers .= "Reply-To: $mail\r\n";
        }
    }

    $multipart = si_create_message($mail_theme, $params);
}

if (!empty($code_word))
    $mail_theme = $code_word . ' | ' . $mail_theme;


    // тут подключаем битрикс
    $isMail = false;

    CModule::IncludeModule('iblock');
    $el = new \CIBlockElement;
    $arNewArray = Array(
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID"      => getIBlockIdByCode('footer_form'),
        "PROPERTY_VALUES"=> array(
            'PHONE' => $params['Телефон'],
            'EMAIL' => $params['E-mail'],
            'MESSAGE' => $params['Вопрос']
        ),
        "NAME"           => $params['Имя'],
        "ACTIVE"         => "Y",
      );

    if($newElId = $el->Add($arNewArray)) {

        $arFields = array(
                "AUTHOR" => $params['Имя'],
                "CONTACTS" => $params['Телефон'],
                "EMAIL" => $params['E-mail'],
                "TEXT" => $params['Вопрос'],
                "ELEMENT_ID" => $newElId,
                "IBLOCK_ID" => getIBlockIdByCode('footer_form')
                );

       $res = Event::send(array(
            "EVENT_NAME" => "WP_CONSULT_FORM",
            "LID" => "el",
            "C_FIELDS" => $arFields
        ));

        $isMail = true;
    }

if ( $isMail ) {
//if (mail($email_to, '=?UTF-8?B?' . base64_encode($mail_theme) . '?=', $multipart, $headers)) {
    echo json_encode(array('success' => 1, 'id' => $code, 'gcode' => 'target' . $code));
}
else {
    echo json_encode(array('success' => 0, 'text' => 'Не удалось отправить сообщение'));
}
