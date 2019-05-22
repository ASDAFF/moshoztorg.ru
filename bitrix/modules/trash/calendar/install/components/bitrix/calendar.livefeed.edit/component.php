<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!isset($arParams["CALENDAR_TYPE"]))
	$arParams["CALENDAR_TYPE"] = 'user';

$curUserId = $USER->IsAuthorized() ? $USER->GetID() : '';
$id = 'cal_'.randString(4);
if(!CModule::IncludeModule("calendar") || !class_exists("CCalendar"))
	return ShowError(GetMessage("EC_CALENDAR_MODULE_NOT_INSTALLED"));

// Userfields
global $USER_FIELD_MANAGER;

$arResult['ID'] = $id;
$arParams["FORM_ID"] = (!empty($arParams["FORM_ID"]) ? $arParams["FORM_ID"] : "blogPostForm");
$arParams["JS_OBJECT_NAME"] = 'oCalEditor'.$id;
$arParams['EDITOR_HEIGHT'] = 120;
$arParams['EVENT_ID'] = 0; // Only for new events
$arParams['OWNER_TYPE'] = 'user';
$arParams['CUR_USER'] = $USER->GetId();
$arResult['USER_FIELDS'] = $USER_FIELD_MANAGER->GetUserFields("CALENDAR_EVENT", $arParams['EVENT_ID'], LANGUAGE_ID);

// Webdaw upload file UF
$arParams["UPLOAD_WEBDAV_ELEMENT"] = $arResult['USER_FIELDS']['UF_WEBDAV_CAL_EVENT'];

$arParams['SECTIONS'] = CCalendar::GetSectionList(array(
	'CAL_TYPE' => $arParams['OWNER_TYPE'],
	'OWNER_ID' => $arParams['CUR_USER']
));

if (empty($arParams['SECTIONS']))
{
	$defCalendar = CCalendarSect::CreateDefault(array(
		'type' => $arParams['OWNER_TYPE'],
		'ownerId' => $arParams['CUR_USER']
	));
	$arParams['SECTIONS'][] = $defCalendar;
	CCalendar::SetCurUserMeetingSection($defCalendar['ID']);
}

$arParams['EVENT'] = CCalendarEvent::GetById($arParams['EVENT_ID']);

$arParams["LHE"] = array(
	'id' => 'lhe'.$id,
	'height' => $arParams['EDITOR_HEIGHT'],
	'inputName' => 'EVENT_DESCRIPTION',
	'content' => $arParams["EVENT"]["DESCRIPTION"],
	'jsObjName' => $arParams["JS_OBJECT_NAME"],
	'bInitByJS' => true,
	'toolbarConfig' => array(
		'Bold', 'Italic', 'Underline', 'RemoveFormat',
		'Image',
		'BackColor', 'ForeColor',
		'InsertOrderedList', 'InsertUnorderedList',
		'FontSizeList',
		'Source',
		'CreateLink',
		'InputVideoCal'
	),
	'bUseFileDialogs' => false,
	'bUseMedialib' => false,
	'bSaveOnBlur' => false,
	'BBCode' => true,
	'bConvertContentFromBBCodes' => false,
	'bSetDefaultCodeView' => false, // Set first view to CODE or to WYSIWYG
	'bBBParseImageSize' => true, // [IMG ID=XXX WEIGHT=5 HEIGHT=6],  [IMG WEIGHT=5 HEIGHT=6]/image.gif[/IMG]
	'bResizable' => true,
	'bAutoResize' => true,
	'autoResizeOffset' => 40,
	'controlButtonsHeight' => '34',
	'autoResizeSaveSize' => false
);

$arParams["DESTINATION"] = (is_array($arParams["DESTINATION"]) && IsModuleInstalled("socialnetwork") ? $arParams["DESTINATION"] : array());
$arParams["DESTINATION"] = (array_key_exists("VALUE", $arParams["DESTINATION"]) ? $arParams["DESTINATION"]["VALUE"] : $arParams["DESTINATION"]);

// Empty destination for new events
if (!$arParams['EVENT_ID'])
	$arParams["DESTINATION"]["SELECTED"] = array();

$this->IncludeComponentTemplate();

?>