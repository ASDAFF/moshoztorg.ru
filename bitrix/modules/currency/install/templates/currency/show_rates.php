<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/**************************************************************************
				Компонент для вывода курсов валют
***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("currency")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
				Инициализируем начальные параметры компонента
	*************************************************************************/
	/*
	$arrCURRENCY_FROM		= $arParams["arrCURRENCY_FROM"];				// массив переводимых валют
	$CURRENCY_BASE			= $arParams["CURRENCY_BASE"];					// валюта, к которой приводятся все значения
	$RATE_DAY				= $arParams["RATE_DAY"];						// дата курсов в формате "Y-m-d"
	$SHOW_CB				= $arParams["SHOW_CB"];							// показывать курсы с ЦБ РФ
	$CACHE_TIME				= $arParams["CACHE_TIME"];						// время кэширования (сек.)
	*/

	$CACHE_TIME = IntVal($CACHE_TIME);

	$CACHE_ID = __FILE__.md5(serialize($arParams));
	$obCache = new CPHPCache;

	if ($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		if (strlen($CURRENCY_BASE) <= 0)
			$CURRENCY_BASE = COption::GetOptionString("sale", "default_currency");

		if (strlen($CURRENCY_BASE) <= 0)
			$CURRENCY_BASE = CCurrency::GetBaseCurrency();

		if (strlen($CURRENCY_BASE) <= 0)
		{
			$dbCurrency = CCurrency::GetList(($by="SORT"), ($order="ASC"));
			$arCurrency = $dbCurrency->Fetch();
			$CURRENCY_BASE = $arCurrency["CURRENCY"];
		}

		if (StrLen($CURRENCY_BASE) > 0)
		{
			if (strlen($RATE_DAY) <= 0)
			{
				$RATE_DAY_SHOW = GetTime(time(), "SHORT", LANGUAGE_ID);
			}
			else
			{
				$arRATE_DAY_PARSED = ParseDate($RATE_DAY, "ymd");
				$RATE_DAY_SHOW = GetTime(mktime(0, 0, 0, $arRATE_DAY_PARSED[1], $arRATE_DAY_PARSED[0], $arRATE_DAY_PARSED[2]), "D.M.Y", LANGUAGE_ID);
			}

			if (count($arrCURRENCY_FROM) > 0)
			{
				if ($CURRENCY_BASE == "RUR" && $SHOW_CB == "Y")
				{
					$bWarning = False;
					$QUERY_STR = "date_req=".$DB->FormatDate($RATE_DAY_SHOW, CLang::GetDateFormat("SHORT", SITE_ID), "D.M.Y");
					$strQueryText = QueryGetData("www.cbr.ru", 80, "/scripts/XML_daily.asp", $QUERY_STR, $errno, $errstr);
					if (strlen($strQueryText) <= 0)
						$bWarning = True;

					if (!$bWarning)
					{
						require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

						$strQueryText = preg_replace("#<!DOCTYPE[^>]+?>#i", "", $strQueryText);
						$strQueryText = preg_replace("#<"."\\?XML[^>]+?\\?".">#i", "", $strQueryText);

						$objXML = new CDataXML();
						$objXML->LoadString($strQueryText);
						$arData = $objXML->GetArray();

						if (is_array($arData) && count($arData["ValCurs"]["#"]["Valute"])>0)
						{
							for ($j1 = 0; $j1<count($arData["ValCurs"]["#"]["Valute"]); $j1++)
							{
								if (in_array($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"], $arrCURRENCY_FROM))
								{
									$arFields[] = Array(
											"CURRENCY"	=> $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"],
											"RATE_CNT"	=> IntVal($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Nominal"][0]["#"]),
											"RATE"		=> DoubleVal(str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]))
										);
								}
							}
						}
					}
				}
				?>
				<table cellspacing="1" cellpadding="2" border="0" align="center">
				<?if ($SHOW_CB == "Y"):?>
					<tr>
						<td colspan="3"><font class="smalltext"><b><?=GetMessage("CURRENCY_SITE")?></b></font></td>
					</tr>
				<?endif;?>
				<?
				$arDBCurrenies = array();
				$dbCurrencyList = CCurrency::GetList(($b = ""), ($o = ""));
				while ($arCurrency = $dbCurrencyList->Fetch())
					$arDBCurrenies[$arCurrency["CURRENCY"]] = $arCurrency["AMOUNT_CNT"];

				for ($i = 0; $i < count($arrCURRENCY_FROM); $i++)
				{
					if (array_key_exists($arrCURRENCY_FROM[$i], $arDBCurrenies))
					{
						$rate = CCurrencyRates::ConvertCurrency($arDBCurrenies[$arrCURRENCY_FROM[$i]], $arrCURRENCY_FROM[$i], $CURRENCY_BASE, $RATE_DAY);
						?>
						<tr>
							<td nowrap><nobr><font class="smalltext"><?= CurrencyFormat($arDBCurrenies[$arrCURRENCY_FROM[$i]], $arrCURRENCY_FROM[$i]) ?></font></nobr></td>
							<td nowrap><nobr><font class="smalltext">=</font></nobr></td>
							<td nowrap><nobr><font class="smalltext"><?= CurrencyFormat($rate, $CURRENCY_BASE) ?></font></nobr></td>
						</tr>
						<?
					}
				}

				if (is_array($arFields) && $SHOW_CB == "Y")
				{
				?>
					<tr>
						<td colspan="3"><font class="smalltext"><br><b><?=GetMessage("CURRENCY_CBRF")?></b></font></td>
					</tr>
					<?
					foreach ($arFields as $val)
					{
						?>
						<tr>
							<td nowrap><nobr><font class="smalltext"><?= CurrencyFormat($val["RATE_CNT"], $val["CURRENCY"]) ?></font></nobr></td>
							<td nowrap><nobr><font class="smalltext">=</font></nobr></td>
							<td nowrap><nobr><font class="smalltext"><?= CurrencyFormat($val["RATE"], $CURRENCY_BASE) ?></font></nobr></td>
						</tr>
						<?
					}
				}
				?>
				</table>
				<?
			}
		}

		$obCache->EndDataCache();
	}
	//*******************************************************
endif;
?>