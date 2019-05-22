<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/*************************************************************************
	Processing of received parameters
*************************************************************************/
/************** Обязательные параметры ***********************************/
	$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
	$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
	
	$arParams["ALBUM_PHOTO"]["WIDTH"] = (intVal($arParams["ALBUM_PHOTO_WIDTH"]) > 0 ? intVal($arParams["ALBUM_PHOTO_WIDTH"]) : 150);
	$arParams["ALBUM_PHOTO"]["HEIGHT"] = (intVal($arParams["ALBUM_PHOTO_HEIGHT"]) > 0 ? intVal($arParams["ALBUM_PHOTO_HEIGHT"]) : 150);
	$arParams["ALBUM_PHOTO"]["HEIGHT"] = $arParams["ALBUM_PHOTO"]["WIDTH"];
/************** Значения URL *********************************************/
	$URL_NAME_DEFAULT = array(
		"section" => "PAGE_NAME=section&SECTION_ID=#SECTION_ID#",
		"section_edit" => "PAGE_NAME=section_edit&SECTION_ID=#SECTION_ID#&ACTION=#ACTION#",
		"upload" => "PAGE_NAME=upload&SECTION_ID=#SECTION_ID#&ACTION=upload",
		"detail" => "PAGE_NAME=detail&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#");
		
	foreach ($URL_NAME_DEFAULT as $URL => $URL_VALUE)
	{
		$arParams[strToUpper($URL)."_URL"] = trim($arParams[strToUpper($URL)."_URL"]);
		if (empty($arParams[strToUpper($URL)."_URL"]))
			$arParams[strToUpper($URL)."_URL"] = $APPLICATION->GetCurPageParam($URL_VALUE, array("PAGE_NAME", "SECTION_ID", "ELEMENT_ID", "ACTION", "sessid", "edit"));
		$arParams["~".strToUpper($URL)."_URL"] = $arParams[strToUpper($URL)."_URL"];
		$arParams[strToUpper($URL)."_URL"] = htmlspecialchars($arParams["~".strToUpper($URL)."_URL"]);
	}
/************** Дополнительные параметры (права) **************************/
	$arParams["USE_PERMISSIONS"] = ($arParams["USE_PERMISSIONS"]=="Y");
	$arParams["GROUP_PERMISSIONS"] = (!is_array($arParams["GROUP_PERMISSIONS"]) ? array(1) : $arParams["GROUP_PERMISSIONS"]);
	
/************** Дополнительные параметры **************************/
	$arParams["DISPLAY_PANEL"] = ($arParams["DISPLAY_PANEL"]=="Y"); //Turn off by default
	$arParams["DATE_FORMAT"] = trim($arParams["DATE_FORMAT"]);
	if(strlen($arParams["DATE_FORMAT"]) <= 0)
		$arParams["DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
/************** Кеш ******************************************************/
	if(!isset($arParams["CACHE_TIME"]))
		$arParams["CACHE_TIME"] = 3600;
	if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
		$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
	else
		$arParams["CACHE_TIME"] = 0;
/************** Заголовок страницы ***************************************/
	$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N"; //Turn on by default
	$arParams["DISPLAY_PANEL"] = $arParams["DISPLAY_PANEL"]=="Y"; //Turn off by default
/*************************************************************************
	/Processing of received parameters
*************************************************************************/
	$bUSER_HAVE_ACCESS = (!$arParams["USE_PERMISSIONS"]);
	if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
	{
		$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
		foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
		{
			if(in_array($PERM, $arUserGroupArray))
			{
				$bUSER_HAVE_ACCESS = true;
				break;
			}
		}
	}
/*************************************************************************
				Кеширование
*************************************************************************/
// OutPut Data
	$arResult["SECTIONS"] = array();
	$arResult["USER_HAVE_ACCESS"] = false;
if($this->StartResultCache(false, array($USER->GetGroups(), $bUSER_HAVE_ACCESS)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult["TREE_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["SECTION_TREE_URL"], array("SECTION_ID" => "0"));
	
	if (CIBlock::GetPermission($arParams["IBLOCK_ID"]) >= "W")
	{
		$bUSER_HAVE_ACCESS = true;
		$arResult["~NEW_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_EDIT_URL"], 
			array("SECTION_ID" => "0", "ACTION" => "new"));
		$arResult["NEW_LINK"] = htmlSpecialChars($arResult["~NEW_LINK"]);
		$arResult["SECTION"]["~UPLOAD_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~UPLOAD_URL"], 
			array("SECTION_ID" => 0));
		$arResult["SECTION"]["UPLOAD_LINK"] = htmlSpecialChars($arResult["SECTION"]["~UPLOAD_LINK"]);
	}
	
	//WHERE
	$arFilter = array(
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE" => "Y",
		"SECTION_ID" => "-1"
	);
	//ORDER BY
	$arSort = array("ID" => "ASC");
	//EXECUTE
	$rsSections = CIBlockSection::GetList($arSort, $arFilter);
	while($arSection = $rsSections->GetNext())
	{
		$arSection["SECTION_PAGE_URL"] = CComponentEngine::MakePathFromTemplate($arParams["SECTION_URL"], 
				array("SECTION_ID" => $arSection["ID"], "SECTION_CODE" => $arSection["CODE"]));
			
		if (CIBlock::GetPermission($arParams["IBLOCK_ID"]) >= "W")
		{
			$arSection["~EDIT_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_EDIT_URL"], 
				array("SECTION_ID" => $arSection["ID"], "ACTION" => "edit"));
			$arSection["~DROP_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_EDIT_URL"], 
				array("SECTION_ID" => $arSection["ID"], "ACTION" => "drop"));
			if (strpos($arSection["~DROP_LINK"], "?") === false)
				$arSection["~DROP_LINK"] .= "?";
			$arSection["~DROP_LINK"] .= "&".bitrix_sessid_get()."&edit=Y";
			
			$arSection["~NEW_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_EDIT_URL"], 
				array("SECTION_ID" => $arSection["ID"], "ACTION" => "new"));
			$arSection["EDIT_LINK"] = htmlSpecialChars($arSection["~EDIT_LINK"]);
			$arSection["DROP_LINK"] = htmlSpecialChars($arSection["~DROP_LINK"]);
			$arSection["NEW_LINK"] = htmlSpecialChars($arSection["~NEW_LINK"]);
		}
		
		$arSection['ELEMENTS_CNT'] = intVal(CIBlockSection::GetSectionElementsCount($arSection["ID"], Array("CNT_ALL"=>"Y")));
		$arSection['SECTIONS_CNT'] = intVal(CIBlockSection::GetCount(array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "SECTION_ID"=>$arSection["ID"])));
		
		$arFilter["IBLOCK_SECTION_ID"] = $arSection["ID"];
		
		$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arParams["IBLOCK_ID"]."_SECTION", $arSection["ID"], LANGUAGE_ID);
		$arSection["~DATE"] = $arUserFields["UF_DATE"];
		if (is_array($arSection["~DATE"]))
			$arSection["DATE"] = CIBlockFormatProperties::DateFormat($arParams["DATE_FORMAT"], MakeTimeStamp($arSection["~DATE"], CSite::GetDateFormat()));
		
		if (intVal($arSection["~DETAIL_PICTURE"]) > 0)
			$arSection["PICTURE"] = CFile::GetFileArray($arSection["~DETAIL_PICTURE"]);
		elseif (intVal($arSection["~PICTURE"]) > 0)
			$arSection["PICTURE"] = CFile::GetFileArray($arSection["~PICTURE"]);
		
		$arResult["SECTIONS"][]=$arSection;
	}
	$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
	$this->IncludeComponentTemplate();
}
/*?><pre><b>$arResult["USER_HAVE_ACCESS"]: </b><?=$arResult["USER_HAVE_ACCESS"]?></pre><?*/
if ($arParams["SET_TITLE"])
	$APPLICATION->SetTitle(GetMessage("P_TITLE"));

if($USER->IsAuthorized())
{
	if($GLOBALS["APPLICATION"]->GetShowIncludeAreas() && CModule::IncludeModule("iblock"))
		$this->AddIncludeAreaIcons(CIBlock::ShowPanel($arParams["IBLOCK_ID"], 0, 0, $arParams["IBLOCK_TYPE"], true));
	if($arParams["DISPLAY_PANEL"] && CModule::IncludeModule("iblock"))
		CIBlock::ShowPanel($arParams["IBLOCK_ID"], 0, 0, $arParams["IBLOCK_TYPE"]);
}
?>
