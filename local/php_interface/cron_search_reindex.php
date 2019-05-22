<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('BX_NO_ACCELERATOR_RESET', true);
define('CHK_EVENT', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0);
@ignore_user_abort(true);

$lockfile = $_SERVER["DOCUMENT_ROOT"]."/local/cron_search_lock.tmp";
$locktime = 7200;

if(file_exists($lockfile) && (time() - filemtime($lockfile)) < $locktime){
	AddMessage2Log("Search Reindex Parallel PROHIBITED");
	exit();
}

file_put_contents($lockfile, "".time());
try {
	Reindex_Search();
	AddMessage2Log("Search Reindex COMPLETE");
} catch(Exception $e) {
	AddMessage2Log("Search Reindex FAILED: ".$e->getMessage());
}
unlink($lockfile);


?>