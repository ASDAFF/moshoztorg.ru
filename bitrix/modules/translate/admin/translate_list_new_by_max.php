<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002 Bitrix                  #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/translate/prolog.php");
$TRANS_RIGHT = $APPLICATION->GetGroupRight("translate");
if($TRANS_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/translate/include.php");
IncludeModuleLangFile(__FILE__);

@set_time_limit(0);
$sTableID = "tbl_translate_list";
$lAdmin = new CAdminList($sTableID);

/***************************************************************************
								  Функции
***************************************************************************/

function GetTDirList($path)
{
	global $arDirs, $arFiles, $arTLangs;
	$handle=@opendir(prepare_path($_SERVER["DOCUMENT_ROOT"]."/".$path."/"));
	if ($handle)
	{
		while (false!==($file=readdir($handle)))
		{
			if ($file!="." && $file!=".." && $file!=".access.php" && $file!=".htaccess")
			{
				$IS_DIR = (is_dir(prepare_path($_SERVER["DOCUMENT_ROOT"]."/".$path."/".$file))) ? "Y" : "N";
				$path_prepared=prepare_path("/".$path."/".$file);
				if ($IS_DIR=="Y" && 
					($path_prepared=="/bitrix/updates" || 
					$path_prepared=="/bitrix/updates_enc" || 
					$path_prepared=="/bitrix/updates_enc5" || 
					$path_prepared=="/bitrix/help")
				) 
					continue;

				/////////////////
				//if (strpos(prepare_path("/".$path."/".$file), "modules")!==false) continue;
				//if (strpos(prepare_path("/".$path."/".$file), "php_interface")!==false) continue;
				/////////////////
				$bIsLangDir=is_lang_dir($path_prepared);
				if ($IS_DIR=="Y" || $bIsLangDir)
				{
					$arr["IS_DIR"] = $IS_DIR;
					$arr["PARENT"] = prepare_path("/".$path."/");
					$arr["PATH"] = ($IS_DIR=="Y") ? $path_prepared."/" : $path_prepared;
					$arr["FILE"] = $file;
					if ($IS_DIR=="Y") $arDirs[] = $arr;
					else $arFiles[] = $arr;
				}
				if ($IS_DIR=="Y") GetTDirList($path_prepared."/");
			}
		}
		closedir($handle);
	}
}

function GetLangDirs($arDirs)
{
	global $arLangDirs;
	if (is_array($arDirs))
	{
		$arLDirs=array();
		foreach($arDirs as $arr1)
			if(strpos($arr1["PATH"],"/lang/")!==false)
				$arLDirs[]=$arr1["PATH"];
		sort($arLDirs);
		foreach ($arDirs as $arr1)
		{
			if ($arr1["IS_DIR"]=="Y")
			{
				$path = $arr1["PATH"];
				$pathl=strlen($path);
				$next=1;$last=count($arLDirs);$found=false;
				while($next<=$last && !$found)
				{
					$i=intval(($next+$last)/2);
					$c=strncmp($path,$arLDirs[$i-1],$pathl);
					if($c<0)
						$last=$i-1;
					elseif($c>0)
						$next=$i+1;
					else
						$found=true;
				}
				if ($found) $arLangDirs[] = $arr1;
			}
			else $arLangDirs[] = $arr1;
		}
	}
}

function GetPhraseCounters($arCommon, $path, $key)
{
	global $arCommonCounter, $Counter, $arTLangs;
	$Counter++;

	// если директория то
	if (is_dir(prepare_path($_SERVER["DOCUMENT_ROOT"]."/".$path."/")))
	{
		if (is_lang_dir($path))
		{
			if (is_array($arTLangs))
			{
				// сформируем массив файлов для языковой директории
				foreach ($arTLangs as $lng)
				{
					$path = replace_lang_id($path, $lng);

					foreach ($arCommon as $arr)
					{
						if (substr($arr["PATH"],0,strlen($path))==$path)
						{
							if ($arr["IS_DIR"]=="N") $arDirFiles[] = $arr["PATH"];
						}
					}
				}
			}
		}
		else
		{
			if (is_array($arCommon))
			{
				// сформируем массив файлов для обычной директории
				foreach ($arCommon as $arr)
				{
					if (substr($arr["PATH"],0,strlen($path))==$path)
					{
						if ($arr["IS_DIR"]=="N") $arDirFiles[] = $arr["PATH"];
					}
				}
			}
		}
	}
	else // если файл то
	{
		// сформируем массив одноименных файлов для разных языков
		foreach ($arTLangs as $lng) $arDirFiles[] = replace_lang_id($path, $lng);
	}

	if (is_array($arDirFiles))
	{
		// сформируем массив для каждого файла по языкам
		$MESS_tmp = $MESS;
		foreach ($arDirFiles as $file)
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$file))
			{
				$arKeys = array();
				$MESS_TRANS = array();
				$arSlash = explode("/",$file);
				$lang_key = array_search("lang", $arSlash) + 1;
				$file_lang = $arSlash[$lang_key];
				if (in_array($file_lang, $arTLangs))
				{
					$MESS = array();
					if (substr($file, -3)!="php") continue;
					@include($_SERVER["DOCUMENT_ROOT"].$file);
ob_start();print_r($_SERVER["DOCUMENT_ROOT"].$file);$m=ob_get_contents();ob_end_clean();
$m="$m\n";$f=fopen("D:\\debug.log", "a");
fwrite($f, $m);fclose($f);
					$file_name = str_replace("/".$file_lang."/","/", $file);
					$arFilesLng[$file_name][$file_lang] = $MESS;
				}
			}
		}
		$MESS = $MESS_tmp;

		if (is_array($arFilesLng))
		{
			// посчитаем сумму и расхождения для каждого файла
			foreach ($arFilesLng as $file=>$arLns)
			{
				// посчитаем сумму
				$total_arr = array();
				foreach ($arLns as $ln=>$arLn)
					foreach($arLn as $message_key=>$message_value)
						$total_arr[$message_key]++;
				$total = sizeof($total_arr);

				$arr = array();
				$arr["TOTAL"] = $total;
				// посчитаем расхождения для каждого языка
				foreach($arTLangs as $lang)
				{
					if(is_array($arLns[$lang]))
					{
						$diff=0;
						foreach($arLns[$lang] as $message_key=>$message_value)
							if(!array_key_exists($message_key, $total_arr))
								++$diff;
					}
					else
						$diff = $total;
					$arr["DIFF"] = $diff;
					$arFilesLngCounter[$file][$lang] = $arr;
				}
			}

			if (is_array($arFilesLngCounter))
			{
				// посчитаем сумму и расхождения для всей директории
				foreach ($arFilesLngCounter as $arCount)
				{
					while (list($ln, $arLn)=each($arCount))
					{
						$arCommonCounter[$key][$ln]["TOTAL"] += $arLn["TOTAL"];
						$arCommonCounter[$key][$ln]["DIFF"] += $arLn["DIFF"];
					}
				}
			}
		}
	}
}
$s=getmicrotime();
$arLangCounters = array();
$arTLangs = array();
$ln = @CLanguage::GetList($o, $b, Array("ACTIVE"=>"Y"));
while ($lnr = $ln->Fetch())	$arTLangs[] = $lnr["LID"];

// если была нажата кнопка "Перейти"
if(strlen($go_path)>0 && !preg_match("#\.\.[\\/]#", $path))
	$path = add_lang_id($go_path, reset($arTLangs), $arTLangs);
// проверка на правильность
if(preg_match("#\.\.[\\/]#", $path))
	$path = "";
// если путь не задан то
if (strlen($path)<=0) $path = COption::GetOptionString("translate", "INIT_FOLDERS");

$path = prepare_path("/".$path."/");
$go_path = remove_lang_id($path, $arTLangs);

$IS_LANG_DIR = is_lang_dir($path);
// если мы зашли в каталог /lang/...
echo getmicrotime()-$s,"<br>";$s=getmicrotime();
if ($IS_LANG_DIR)
{
	reset($arTLangs);
	foreach ($arTLangs as $hlang)
	{
		$ph = add_lang_id($path, $hlang, $arTLangs);
		if (strlen($ph)>0) GetTDirList($ph);
		$ph = "";
	}
}
else GetTDirList($path);
echo "GetTDirList:",getmicrotime()-$s,"<br>";$s=getmicrotime();

// формируем навигационную цепочку
$arrChain = array();
if ($path!="/") $arrChain[] = array("NAME" => "..", "PATH" => "/");
$arr = explode("/",$go_path);
if (is_array($arr))
{
	$arrP = array();
	TrimArr($arr);
	foreach($arr as $d)
	{
		$arrP[] = $d;
		$p = prepare_path("/".implode("/",$arrP)."/");
		if (remove_lang_id($path, $arTLangs)==$p) $p="";
		$arrChain[] = array("NAME" => $d, "PATH" => $p);
	}
}

$show_error = COption::GetOptionString("translate", "ONLY_ERRORS");
$show_error = ($show_error=="Y") ? "Y" : "";
echo "arrChain:",getmicrotime()-$s,"<br>";$s=getmicrotime();

GetLangDirs($arDirs);

echo "GetLangDirs:",getmicrotime()-$s,"<br>";$s=getmicrotime();
function files_sort($a,$b)
{
	global $arFiles;
	$va = $arFiles[$a]["FILE"];
	$vb = $arFiles[$b]["FILE"];
	if ($va==$vb) return 0;
	elseif (strtolower($va)>strtolower($vb)) return 1;
	else return -1;
}
function dirs_sort($a,$b)
{
	global $arLangDirs;
	$va = $arLangDirs[$a]["FILE"];
	$vb = $arLangDirs[$b]["FILE"];
	if ($va==$vb) return 0;
	elseif (strtolower($va)>strtolower($vb)) return 1;
	else return -1;
}

if(!is_array($arFiles)) $arFiles = Array();
else uksort($arFiles, "files_sort");

if(!is_array($arLangDirs)) $arLangDirs = Array();
else uksort($arLangDirs, "dirs_sort");

$arLangDirFiles = array_merge($arLangDirs, $arFiles);

$lAdmin->BeginPrologContent();
	?>
	<p><?
	for ($i=0; $i<=sizeof($arrChain)-1; $i++) :
		if ($i>0) :
			?> / <?
		endif;
		if (strlen($arrChain[$i]["PATH"])>0):
			$last_path = $arrChain[$i]["PATH"];
			?><a href="?lang=<?=LANG?>&path=<?=$last_path?>"  title="<?=GetMessage("TR_FOLDER_TITLE")?>"><?=$arrChain[$i]["NAME"]?></a><?
		else:
			?><?=$arrChain[$i]["NAME"]?><?
		endif;
	endfor;
	?></p>
<?
$lAdmin->EndPrologContent();


$header[] = array("id"=>"TRANS_FILE_NAME", "content"=>GetMessage("TRANS_FILE_NAME"),	"default"=>true, "align"=>"left");
$header[] = array("id"=>"TRANS_TOTAL_MESSAGES", "content"=>GetMessage("TRANS_TOTAL_MESSAGES"), "default"=>true, "align"=>"right");

reset($arTLangs);
foreach($arTLangs as $vlang)
	$header[] = array("id"=>$vlang, "content"=>$vlang, "default"=>true, "align"=>"center");

$lAdmin->AddHeaders($header);

if (strlen($path)>0)
{
	$row =& $lAdmin->AddRow("0", Array());
	$row->AddViewField("TRANS_FILE_NAME", '<a href="?lang='.LANGUAGE_ID.'&path='.$last_path.'" title="'.GetMessage("TR_UP_TITLE").'"><img src="/bitrix/images/translate/up.gif" width="11" height="13" border=0 alt=""></a>'.'&nbsp;<a href="?lang='.LANGUAGE_ID.'&path='.$last_path.'" title="'.GetMessage("TR_UP_TITLE").'">..</a>');
	$row->AddViewField("TRANS_TOTAL_MESSAGES", "&nbsp;");
	foreach($arTLangs as $vlang)
		$row->AddViewField($vlang, "&nbsp;");
}

$ORIGINAL_MESS = $MESS;

if (is_array($arLangDirFiles)) :

	if ($IS_LANG_DIR)
	{
		reset($arTLangs);
		foreach ($arTLangs as $tlang) 
			$arPath[] = add_lang_id($path, $tlang, $arTLangs);
	}
	else 
		$arPath[] = $path;

	$arShown = array();
	$arrTOTAL_NOT_TRANSLATED = array();
	$TOTAL_MESS = 0;
	reset($arLangDirFiles);
	$i = 0;
	//echo "<pre>"; print_r($arLangDirFiles); echo "</pre>";
	while (list($key, $ar)=each($arLangDirFiles)) :
		$i++;
		if (in_array($ar["PARENT"],$arPath)) :

			$is_dir = $ar["IS_DIR"];
			$fpath = $ar["PATH"];
			$ftitle = $ar["FILE"];

			if ($IS_LANG_DIR)
			{
				
				if (in_array($ftitle, $arShown)) 
					continue;
				$arShown[] = $ftitle;
			}

			// сформируем ключ
			$fkey = remove_lang_id($fpath, $arTLangs);

			// сформируем массив счетчиков
			GetPhraseCounters($arLangDirFiles, $fpath, $fkey);
			if ($is_dir=="Y") :
				$row =& $lAdmin->AddRow($i, Array(), "translate_list.php?lang=".LANGUAGE_ID."&path=".$fpath, GetMessage("TR_FOLDER_TITLE"));
				$row->AddViewField("TRANS_FILE_NAME", '<a href="?lang='.LANGUAGE_ID.'&path='.$fpath.'" title="'.GetMessage("TR_FOLDER_TITLE").'"><img src="/bitrix/images/translate/folder.gif" width="16" height="16" border=0 alt=""></a>'.'&nbsp;<a href="?lang='.LANGUAGE_ID.'&path='.$fpath.'" title="'.GetMessage("TR_FOLDER_TITLE").'">'.$ftitle.'</a>');
			else :
				$row =& $lAdmin->AddRow($i, Array(), "translate_edit.php?lang=".LANGUAGE_ID."&file=".$fpath."&show_error=".$show_error, GetMessage("TR_FILE_TITLE"));
				$row->AddViewField("TRANS_FILE_NAME", '<a href="translate_edit.php?lang='.LANGUAGE_ID.'&file='.$fpath.'&show_error='.$show_error.'" title="'.GetMessage("TR_FILE_TITLE").'"><img src="/bitrix/images/translate/file.gif" width="16" height="16" border=0 alt=""></a>'.'&nbsp;<a href="translate_edit.php?lang='.LANGUAGE_ID.'&file='.$fpath.'&show_error='.$show_error.'" title="'.GetMessage("TR_FILE_TITLE").'">'.$ftitle.'</a>');
			endif;

			$arr = array();
			reset($arTLangs);
			foreach($arTLangs as $vlang)
				$arr[] = intval($arCommonCounter[$fkey][$vlang]["TOTAL"]);
			$total_messages = max($arr);
			$TOTAL_MESS += $total_messages;
			$row->AddViewField("TRANS_TOTAL_MESSAGES", $total_messages);

			reset($arTLangs);
			foreach($arTLangs as $vlang):
				$lang_not_translated = intval($arCommonCounter[$fkey][$vlang]["DIFF"]);
				$lang_total = intval($arCommonCounter[$fkey][$vlang]["TOTAL"]);
				$diff_total = $total_messages - $lang_total;

				if (intval($lang_not_translated)>0):
					$arrTOTAL_NOT_TRANSLATED[$vlang] += $lang_not_translated;
					$row->AddViewField($vlang, "<span class='required'>".$lang_not_translated."</span>");
				elseif (intval($diff_total)>0):
					$arrTOTAL_NOT_TRANSLATED[$vlang] += $diff_total;
					$row->AddViewField($vlang, "<span class='required'>".$diff_total."</span>");
				else:
					$row->AddViewField($vlang, "&nbsp;");
				endif;
			endforeach;
		endif;
	endwhile;
endif;

$MESS = $ORIGINAL_MESS;
$row =& $lAdmin->AddRow($i++, Array());
$row->AddViewField("TRANS_FILE_NAME", "<b>".GetMessage("TRANS_TOTAL").":</b>");
$row->AddViewField("TRANS_TOTAL_MESSAGES", "<b>".$TOTAL_MESS."</b>");
reset($arTLangs);
foreach($arTLangs as $vlang):
	if (intval($arrTOTAL_NOT_TRANSLATED[$vlang])>0)
	{
		$row->AddViewField($vlang, "<b>".$arrTOTAL_NOT_TRANSLATED[$vlang]."</b>");
	}
endforeach;

//$rsData = CDBResult::InitFromArray();
$lAdmin->BeginEpilogContent();
?>
	<input type="hidden" name="go_path" id="go_path" value="">
<?
$lAdmin->EndEpilogContent();

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("TRANS_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<script>
function __FTranslateSubmit()
{
		document.getElementById('go_path').value = document.getElementById('path_to').value;
		<?=$lAdmin->ActionPost()?>
}
</script>
<p><form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<table border="0" cellpadding="3" width="0%" cellspacing="1">
	<tr>
		<td><p><?=GetMessage("TRANS_PATH")?></p></td>
		<td><input type="text" name="path" id="path_to" value="<?=htmlspecialchars($path)?>" size="60"></td>
		<td><input type="submit" value="<?=GetMessage("TRANS_GO")?>" onclick="__FTranslateSubmit(); return false;"></td>
	</tr>
</table>
</form>
</p>
<?$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
