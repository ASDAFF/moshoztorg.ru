<?
    file_put_contents('/home/bitrix/www/yandex_market_bot_coming_in.log',$_SERVER['HTTP_USER_AGENT'].' | '.date('d.m.Y H:i:s')."\n\r",FILE_APPEND);
	$re = '/^.*(yml_catalog>).*$/m';
	$str = file_get_contents('/home/bitrix/www/moshoztorg_export.xml');
	preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

	if (sizeof($matches[0]) != 2) {
		//восстановить из бэкапа, тут файл-калека
		unlink('/home/bitrix/www/moshoztorg_export.xml');
		copy('/home/bitrix/www/moshoztorg_export.xml.backup', '/home/bitrix/www/moshoztorg_export.xml');
		header('Location: https://moshoztorg.ru/moshoztorg_export.xml');
		exit;		
	} else {
		//забэкапить
		unlink('/home/bitrix/www/moshoztorg_export.xml.backup');
		copy('/home/bitrix/www/moshoztorg_export.xml', '/home/bitrix/www/moshoztorg_export.xml.backup');		
		header('Location: https://moshoztorg.ru/moshoztorg_export.xml');
		exit;
		/*
		header('Content-type: application/xml');
		header("Content-Type: text/xml; charset=windows-1251");    
		file_put_contents('/home/bitrix/www/yandex_market_bot_coming_in.log',$_SERVER['HTTP_USER_AGENT'].' | '.date('d.m.Y H:i:s')."\n\r",FILE_APPEND);		
		echo( file_get_contents('/home/bitrix/www/moshoztorg_export.xml') );		*/
	}	
	/*
	file_put_contents('/home/bitrix/www/yandex_market_bot_coming_in.log',$_SERVER['HTTP_USER_AGENT'].' | '.date('d.m.Y H:i:s')."\n\r",FILE_APPEND);
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'YandexMarket') !== false) {
        require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
        file_put_contents('/home/bitrix/www/yandex_market_bot_was_there.log','yandex_market_bot_was_there '.date('d.m.Y H:i:s'));
    }*/
?>