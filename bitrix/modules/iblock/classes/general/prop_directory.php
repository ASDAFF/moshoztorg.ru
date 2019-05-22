<?php
use Bitrix\Highloadblock as HL;
IncludeModuleLangFile(__FILE__);

class CIBlockPropertyDirectory
{
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "directory",
			"DESCRIPTION" => GetMessage("IBLOCK_PROP_DIRECTORY_DESCRIPTION"),
			"GetSettingsHTML" => array('CIBlockPropertyDirectory', "GetSettingsHTML"),
			"GetPropertyFieldHtml" => array('CIBlockPropertyDirectory', "GetPropertyFieldHtml"),
			"PrepareSettings" =>array("CIBlockPropertyDirectory","PrepareSettings"),
		);
	}

	function PrepareSettings($arProperty)
	{
		$size = 0;
		if(is_array($arProperty["USER_TYPE_SETTINGS"]))
			$size = intval($arProperty["USER_TYPE_SETTINGS"]["size"]);
		if($size <= 0)
			$size = 1;

		$width = 0;
		if(is_array($arProperty["USER_TYPE_SETTINGS"]))
			$width = intval($arProperty["USER_TYPE_SETTINGS"]["width"]);
		if($width <= 0)
			$width = 0;

		if(is_array($arProperty["USER_TYPE_SETTINGS"]) && $arProperty["USER_TYPE_SETTINGS"]["group"] === "Y")
			$group = "Y";
		else
			$group = "N";

		if(is_array($arProperty["USER_TYPE_SETTINGS"]) && $arProperty["USER_TYPE_SETTINGS"]["multiple"] === "Y")
			$multiple = "Y";
		else
			$multiple = "N";
		$directoryId = 0;
		if(is_array($arProperty["USER_TYPE_SETTINGS"]) && isset($arProperty["USER_TYPE_SETTINGS"]["DIR"]))
			$directoryId = intval($arProperty["USER_TYPE_SETTINGS"]['DIR']);
		return array(
			"size" =>  $size,
			"width" => $width,
			"group" => $group,
			"multiple" => $multiple,
			"DIR" => $directoryId,
		);
	}

	function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		if(!CModule::IncludeModule('highloadblock'))
			return '';
		$cellOption = '';
		$settings = CIBlockPropertyDirectory::PrepareSettings($arProperty);
		$rsData = HL\HighloadBlockTable::getList(array());
		while($arData = $rsData->fetch())
		{
			$selected = ($settings["DIR"] == $arData['ID']) ? ' selected' : '';
			$cellOption .= "<option ".$selected." value=".$arData['ID'].">".htmlspecialcharsbx($arData["NAME"].' ('.$arData["TABLE_NAME"]).")</option>";
		}
		$arPropertyFields = array(
			"HIDE" => array("ROW_COUNT", "COL_COUNT", "MULTIPLE_CNT", "DEFAULT_VALUE", "MULTIPLE"),
		);
		$selectDir = GetMessage("IBLOCK_PROP_DIRECTORY_SELECT_DIR");
		return <<<"HIBSELECT"
	<tr>
		<td>{$selectDir}:</td>
			<td>
			<select name="{$strHTMLControlName["NAME"]}[DIR]" id="{$strHTMLControlName["NAME"]}[DIR]" />
				$cellOption
			</select>
		</td>
	</tr>
HIBSELECT;
	}

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$settings = CIBlockPropertyDirectory::PrepareSettings($arProperty);
		if($settings["size"] > 1)
			$size = ' size="'.$settings["size"].'"';
		else
			$size = '';

		if($settings["width"] > 0)
			$width = ' style="width:'.$settings["width"].'px"';
		else
			$width = '';

		$options = CIBlockPropertyDirectory::GetOptionsHtml($arProperty, array($value["VALUE"]));
		$html = '<select name="'.$strHTMLControlName["VALUE"].'"'.$size.$width.'>';
		$html .= $options;
		$html .= '</select>';
		return  $html;
	}

	function GetOptionsHtml($arProperty, $values)
	{
		if(!CModule::IncludeModule('highloadblock'))
			return '';
		$cellOption = '';
		$highLoadIBId = 0;
		if(isset($arProperty["USER_TYPE_SETTINGS"]["DIR"]))
			$highLoadIBId = intval($arProperty["USER_TYPE_SETTINGS"]["DIR"]);
		if($highLoadIBId > 0)
		{
			$ibFields = self::getEntityFields($highLoadIBId);
			$hlblock = HL\HighloadBlockTable::getById($highLoadIBId)->fetch();
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			$entity_data_class = $entity->getDataClass();
			$rsData = $entity_data_class::getList(array());
			while($arData = $rsData->fetch())
			{
				$options = '';
				if(in_array($arData["ID"], $values))
					$options = ' selected';
				$cellOption .= "<option ".$options." value=".$arData['ID'].">".htmlspecialcharsbx($arData[$ibFields[0]["FIELD_NAME"]].' ('.$arData["ID"]).")</option>";
			}
		}

		return $cellOption;
	}

	private static function getEntityFields($highLoadIBId)
	{
		$arResult = array();
		if($highLoadIBId > 0)
		{
			$rsData = CUserTypeEntity::GetList(array($by=>$order), array(
				"ENTITY_ID" => 'HLBLOCK_'.$highLoadIBId,
			));
			while($arData = $rsData->fetch())
			{
				$arResult[] = $arData;
			}
		}
		return $arResult;
	}
}