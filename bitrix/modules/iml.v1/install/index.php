<?
#################################################
#        Company developer: IPOL
#        Developer: Nikta Egorov
#        Site: http://www.ipol.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2012 IPOL
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);

if(class_exists("iml_v1")) 
    return;
	
Class iml_v1 extends CModule
{
    var $MODULE_ID = "iml.v1";
    var $MODULE_NAME;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "N";
        var $errors;

	function iml_v1()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php"); // Создать версию!

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("IPOLIML_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IPOLIML_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "Ipol";
        $this->PARTNER_URI = "http://www.ipolh.com";
	}
	
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		if(!$DB->Query("SELECT 'x' FROM ipol_iml", true))
		{    $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/".ToLower($DBType)."/install.sql");
		}
		
		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}
		
		return true;
	}


	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/".ToLower($DBType)."/uninstall.sql");

		if(!empty($this->errors))
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}
		
		return true;
	}
	
	function InstallEvents() {
		//события устанавливаются в файле /classes/general/imlhelper.php функция auth
		return true;
	}
	function UnInstallEvents() {
		UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "imldriver", "onEpilog");		
		UnRegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "CDeliveryIML", "onBufferContent");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $this->MODULE_ID, "CDeliveryIML", "pickupLoader");
		// UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CDeliveryIML", "onOEPageLoad");
		UnRegisterModuleDependences("main", "OnAdminListDisplay", $this->MODULE_ID, "imldriver", "displayActPrint");
		UnRegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, "imldriver", "OnBeforePrologHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", $this->MODULE_ID, "CDeliveryIML", "checkNalD2P"); // проверка платежных систем
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", $this->MODULE_ID, "CDeliveryIML", "checkNalP2D"); // проверка платежных систем

		return true;
	}

	function InstallFiles() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true);
		//файл доставки копируется в  файле /classes/general/imlhelper.php функция auth
		$fileOfActs = $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID."/printActs.php";
		if(file_exists($fileOfActs) && LANG_CHARSET === 'UTF-8')
			file_put_contents($fileOfActs,$GLOBALS['APPLICATION']->ConvertCharset(file_get_contents($fileOfActs),'windows-1251','UTF-8'));
		return true;
	}
	function UnInstallFiles()	{
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_delivery/delivery_iml.php");
		DeleteDirFilesEx("/bitrix/components/ipol/ipol.imlPickup");
		$arrayOfFiles=scandir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ipol');
		$flagForDelete=true;
		foreach($arrayOfFiles as $element)
		{
			if(strlen($element)>2)
				$flagForDelete=false;
		}
		if($flagForDelete)
			DeleteDirFilesEx("/bitrix/components/ipol");
		return true;
	}
	
    function DoInstall()
    {
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		if (cmodule::includeModule('ipol.iml')) {
			$GLOBALS['IPOLIML_INSTALL_ERROR'] = GetMessage('INSTALL_ERROR_ANOTHERMODULEHERE');
			$GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('IPOLIML_INSTALL_ERROR_TITLE'), __DIR__ .'/error.php');

			return;
		}

		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
		
		RegisterModule($this->MODULE_ID);
		
        $APPLICATION->IncludeAdminFile(GetMessage("IPOLIML_INSTALL"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
    }

    function DoUninstall()
    {
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		if($_REQUEST['step'] < 2){
			$this->ShowDataSaveForm();
		}elseif($_REQUEST['step'] == 2){		
			COption::SetOptionString($this->MODULE_ID,'logIml','');
			COption::SetOptionString($this->MODULE_ID,'pasIml','');
			COption::SetOptionString($this->MODULE_ID,'logged',false);
			
			if(!$_REQUEST['savedata'])
				$this->UnInstallDB();
			$this->UnInstallFiles();
			$this->UnInstallEvents();
			
			CAgent::RemoveModuleAgents('iml.v1');
			
			UnRegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(GetMessage("IPOLIML_DEL"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
		}
	}

	private function ShowDataSaveForm() {
		$keys = array_keys($GLOBALS);
		for ($i = 0; $i < count($keys); $i++) {
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath') {
				global ${$keys[$i]};
			}
		}

		$APPLICATION->SetTitle(GetMessage('IPOLIML_DEL'));
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
		?>
		<form action="<?= $APPLICATION->GetCurPage() ?>" method="get">
			<?= bitrix_sessid_post();?>
			<input type="hidden" name="lang" value="<?= LANG ?>" />
			<input type="hidden" name="id" value="<?= $this->MODULE_ID ?>" />
			<input type="hidden" name="uninstall" value="Y" />
			<input type="hidden" name="step" value="2" />
			<? CAdminMessage::ShowMessage(GetMessage('IPOLIML_PRESERVE_TABLES')) ?>
			<p><?echo GetMessage('MOD_UNINST_SAVE')?></p>
			 <p><input type="checkbox" name="savedata" id="savedata" value="Y" checked="checked" /><label for="savedata"><?echo GetMessage('MOD_UNINST_SAVE_TABLES')?></label><br /></p>
			<input type="submit" name="inst" value="<?echo GetMessage('MOD_UNINST_DEL');?>" />
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}
?>
