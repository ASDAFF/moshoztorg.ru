<?
global $DBType;
$module_id = 'iml.v1';

CModule::AddAutoloadClasses(
    $module_id,
    array(
		'imlHelper'    => '/classes/general/imlhelper.php',
		'imldriver'    => '/classes/general/imlclass.php',
		'CDeliveryIML' => '/classes/general/imldelivery.php',
		'imlOption'	   => '/classes/general/imloption.php',
		'sqlimldriver' => '/classes/'.ToLower($DBType).'/imlclass.php',
		'imlBarcode'   => '/classes/general/imlBarcode.php'
        )
);
?>