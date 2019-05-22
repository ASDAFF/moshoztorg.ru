<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$cid = trim($_REQUEST['cid']);
if (!$cid)
	die();

if ($_POST["mode"] == "upload")
{
	$arResult = array();
	if (check_bitrix_sessid())
	{
		$count = sizeof($_FILES["mfi_files"]["name"]);

		$mid = $_SESSION['MFI_MODULE_'.$cid];
		$max_file_size = $_SESSION['MFI_MAX_FILE_SIZE_'.$cid];

		if (!$mid || !IsModuleInstalled($mid))
			$mid = 'main';

		for($i = 0; $i < $count; $i++)
		{
			$arFile = array(
				"name" => $_FILES["mfi_files"]["name"][$i],
				"size" => $_FILES["mfi_files"]["size"][$i],
				"tmp_name" => $_FILES["mfi_files"]["tmp_name"][$i],
				"type" => $_FILES["mfi_files"]["type"][$i],
				"MODULE_ID" => $mid
			);

			if ($_FILES["mfi_files"]["size"][$i] <= $max_file_size || $max_file_size <= 0)
			{
				$fileID = CFile::SaveFile($arFile, $mid);
				$tmp = array(
					"fileName" => $_FILES["mfi_files"]["name"][$i],
					"fileID" => $fileID
				);
				if ($fileID)
				{
					if (!isset($_SESSION["MFI_UPLOADED_FILES_".$cid]))
					{
						$_SESSION["MFI_UPLOADED_FILES_".$cid] = array($fileID);
					}
					else
					{
						$_SESSION["MFI_UPLOADED_FILES_".$cid][] = $fileID;
					}
					$file = CFile::GetFileArray($fileID);
					if ($file)
					{
						$tmp["fileURL"] = $file["SRC"];
						$tmp["fileSize"] = CFile::FormatSize($file['FILE_SIZE']);
					}
				}
				$arResult[] = $tmp;
			}
		}
	}
	$APPLICATION->RestartBuffer();
	Header('Content-Type: text/html; charset='.LANG_CHARSET);
	$uid = intval($_POST["uniqueID"]);
?>
<script type="text/javascript">
top.FILE_UPLOADER_CALLBACK_<?=$uid?>(<?=CUtil::PhpToJsObject($arResult);?>, <?=$uid;?>);
</script>
<?
}
elseif ($_POST["mode"] == "delete" && check_bitrix_sessid())
{
	$fid = intval($_POST["fileID"]);
	if (isset($_SESSION["MFI_UPLOADED_FILES_".$cid]) && in_array($fid, $_SESSION["MFI_UPLOADED_FILES_".$cid]))
	{
		CFile::Delete($fid);
		$key = array_search(intval($fid), $_SESSION["MFI_UPLOADED_FILES"]);
		unset($_SESSION["MFI_UPLOADED_FILES"][$key]);
	}
}

die();
?>