<?// $_REQUEST['isFromOpt'] - если печать идет из таблицы штрихкодов, делаем возможность скрыть ненужные при клике?>
<style>
body,div{margin:0px;padding:0px;}
.shtrohCod{
	height: 5cm;
	width: 5.5cm;
	border: 1px solid black;
	text-align: center;
	line-height: 5.7mm;
	margin-left:1mm;
	margin-bottom:1mm;
	float:left;
	cursor:pointer;
}
.shpName{
	background-color: #C4D79B;
	font-weight: bold;
	padding: 0.1mm 5mm;
}
.barcode{
	font-size:60px;
	margin-left:-10mm;
}
.logImg{
	height: 6mm;
	margin-top:2mm;
}
@media print{#hint{display:none;}}
</style>
<?
$j=0;
foreach($arBKs['ORDERS'] as $arBK){?>
	<div class='shtrohCod' <?if($_REQUEST['isFromOpt']){?> onclick='this.parentNode.removeChild(this);' <?}?>>
	
		<span class='shpName'><?=$arBKs['shopName']?></span><br>
		<span><?=$arBK['orderId']?></span><br>
		<span><?=$arBK['city']?></span><br>
		<span><?=$arBK['date']?> / <?=GetMessage('IPOLIML_SIGN_PLACE')?> <?=$arBK['place']?> <?=GetMessage('IPOLIML_SIGN_FROM')?> <?=$arBK['ttl']?></span>
		<br>
		<img style="width:45mm" src='/bitrix/js/<?=$arBKs['module_id']?>/ajax.php?action=getBarcode&barcode=<?=$arBK['barcode']?>'><br>
		<img src='/bitrix/images/<?=$arBKs['module_id']?>/IMLogo.png' class='logImg'>
	</div>
<?
	$j++;
	if($j%3==0)
		echo "<div style='clear:both'></div>";
	if($j%15==0)
		echo '<div style="page-break-before:always"/></div>';
}?>
<?if($_REQUEST['isFromOpt']){?>
<div id='hint' style='clear:both'><?=GetMessage('IPOLIML_SHTIHCOD')?></div>
<?}?>