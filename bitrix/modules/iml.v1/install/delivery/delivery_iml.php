<?
$module_id="iml.v1";
CModule::IncludeModule($module_id);

// ��������� ����� CDeliveryIML::Init � �������� ����������� �������
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/classes/general/imldelivery.php')){
	AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliveryIML', 'Init')); 
}
?>