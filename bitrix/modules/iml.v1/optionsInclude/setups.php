<?
//платежные системы
$PayDefault = COption::GetOptionString($module_id,'paySystems','Y');
if($PayDefault != 'Y')
	$tmpPaySys=unserialize($PayDefault);

$paySysS=CSalePaySystem::GetList(array(),array('ACTIVE'=>'Y'));
$paySysHtml='<select name="paySystems[]" multiple size="5">';
while($paySys=$paySysS->Fetch()){
	$paySysHtml.='<option value="'.$paySys['ID'].'" ';
	if($PayDefault == 'Y') {
		$name = strtolower($paySys['NAME']);
		if( strpos($name, GetMessage('IPOLIML_cashe')) === false && 
			strpos($name, GetMessage('IPOLIML_cashe2')) === false && 
			strpos($name, GetMessage('IPOLIML_cashe3')) === false)
			$paySysHtml.='selected';
	}elseif(in_array($paySys['ID'],$tmpPaySys))
		$paySysHtml.='selected';
	$paySysHtml.='>'.$paySys['NAME'].'</option>';
}
$paySysHtml.="</select>";

$addHold='<select name="addHoldCity[]"><option value=""></option>';
foreach($IPOLIML_list['Region'] as $regionCode => $region)
	$addHold.='<option value="'.$regionCode.'">'.$region.'</option>';
$addHold.='</select>';

$strReadyHold='';
$arReadyHold = unserialize(COption::GetOptionString($module_id,'addHold','a:0:{}'));
foreach($arReadyHold as $cityCode => $dayHold){
	$strReadyHold.='<tr><td><select name="addHoldCity[]"><option value=""></option>';
		foreach($IPOLIML_list['Region'] as $regionCode => $region){
			if($regionCode == $cityCode)
				$strReadyHold.='<option value="'.$regionCode.'" selected>'.$region.'</option>';
			else
				$strReadyHold.='<option value="'.$regionCode.'">'.$region.'</option>';
		}
	$strReadyHold.='</select></td><td><input type="text" name="addHoldTerm[]" value="'.$dayHold.'"></td></tr>';
}
?>
<style>
	.PropHint { 
		background: url("/bitrix/images/ipol.iml/hint.gif") no-repeat transparent;
		display: inline-block;
		height: 12px;
		position: relative;
		width: 12px;
	}
	.b-popup { 
		background-color: #FEFEFE;
		border: 1px solid #9A9B9B;
		box-shadow: 0px 0px 10px #B9B9B9;
		display: none;
		font-size: 12px;
		padding: 19px 13px 15px;
		position: absolute;
		top: 38px;
		width: 300px;
		z-index: 50;
	}
	.b-popup .pop-text { 
		margin-bottom: 10px;
		color:#000;
	}
	.pop-text i {color:#AC12B1;}
	.b-popup .close { 
		background: url("/bitrix/images/<?=$module_id?>/popup_close.gif") no-repeat transparent;
		cursor: pointer;
		height: 10px;
		position: absolute;
		right: 4px;
		top: 4px;
		width: 10px;
	}
	.IPOLIML_clz{
		background:url(/bitrix/panel/main/images/bx-admin-sprite-small.png) 0px -2989px no-repeat; 
		width:18px; 
		height:18px;
		cursor: pointer;
		margin-left:100%;
	}
	.IPOLIML_clz:hover{
		background-position: -0px -3016px;
	}
	#IPOLIML_serviceTable{
		width: 100%;
	}
	#IPOLIML_serviceTable td:first-child{
		text-align: left;
	}
</style>
<script>	
	function ipol_popup_virt(code, info){
		var offset = $(info).position().top;
		var LEFT = $(info).offset().left;		
		
		var obj;
		if(code == 'next') 	obj = $(info).next();
		else  				obj = $('#'+code);
		
		LEFT -= parseInt( parseInt(obj.css('width'))/2 );
		
		obj.css({
			top: (offset+15)+'px',
			left: LEFT,
			display: 'block'
		});	
		return false;
	}
	
	function IPOLIML_serverShow(){
		$('.IPOLIML_service').each(function(){
			$(this).css('display','table-row');
		});
		$('[onclick^="IPOLIML_serverShow("]').css('cursor','auto');
		$('[onclick^="IPOLIML_serverShow("]').css('textDecoration','none');
	}
	
	function IPOLIML_sbrosSchet(){
		if(confirm('<?=GetMessage('IPOLIML_OTHR_schet_ALERT')?>'))
			$.ajax({
				url:'/bitrix/js/<?=$module_id?>/ajax.php',
				type:'POST',
				data: 'action=killSchet',
				success: function(data){
					if(data=='1')
					{
						alert('<?=GetMessage("IPOLIML_OTHR_schet_DONE")?>');
						$("[onclick^='IPOLIML_sbrosSchet(']").parent().html('0');
					}
					else
						alert('<?=GetMessage("IPOLIML_OTHR_schet_NONE")?>'+data);
				}
			});
	}
	
	function IPOLIML_clrUpdt(){
		if(confirm('<?=GetMessage('IPOLIML_OPT_clrUpdt_ALERT')?>'))
		{
			$('.IPOLIML_clz').css('display','none');
			$.ajax({
				url:'/bitrix/js/<?=$module_id?>/ajax.php',
				type:'POST',
				data: 'action=killUpdt',
				success: function(data){
					if(data=='done')
						$("#IPOLIML_updtPlc").replaceWith('');
					else
					{
						$('.IPOLIML_clz').css('display','');
						alert('<?=GetMessage("IPOLIML_OPT_clrUpdt_ERR")?>');
					}
				}
			});
		}
	}
	
	function IPOLIML_syncList()
	{
		$("[onclick='IPOLIML_syncList()']").css('display','none');
		$.post(
			"/bitrix/js/<?=$module_id?>/ajax.php",
			{'action':'callUpdateList'},
			function(data){
				if(data.indexOf('bad')===0)
					alert(data.substr(3));
				else
				{
					$('#IPOLIML_updtTime').html(data.substr(data.indexOf('-')+1));
					alert(data);
				}
			}
		);
	}
	
	function IPOLIML_syncOutb()
	{
		$('[onclick="IPOLIML_syncOutb()"]').css('display','none');
		$.post(
			"/bitrix/js/<?=$module_id?>/ajax.php",
			{'action':'optiondGetOutbox'},
			function(data){
				$('#IPOLIML_suncOutb').parent().html(data);
				$('[onclick="IPOLIML_syncOutb()"]').css('display','');
				IPOLIML_getTable();
			}
		);
	}
	
	function IPOLIML_logoff(){
		$("[onclick='IPOLIML_logoff()']").attr('disabled','disabled');
		if(confirm('<?=GetMessage("IPOLIML_LBL_ISLOGOFF")?>'))
			$.post(
				"/bitrix/js/<?=$module_id?>/ajax.php",
				{'action':'logoff'},
				function(data){
					window.location.reload();
				}
			);
		else
			$("[onclick='IPOLIML_logoff()']").removeAttr('disabled');
	}
	
	function IPOLIML_clearCache(){
		$('#IPOLIML_cacheKiller').attr('disabled','disabled');
		$.post(
			"/bitrix/js/<?=$module_id?>/ajax.php",
			{'action':'clearCache'},
			function(){
				alert("<?=GetMessage('IPOLIML_OTHR_CACHEKILLED')?>");
				$('#IPOLIML_cacheKiller').removeAttr('disabled');
			}
		);
	}
	
	function IPOLIML_addCityHold(){
		var maxCityCnt = parseInt('<?=count($IPOLIML_list['Region'])?>');
		var ttlCity    = $('[name="addHoldTerm[]"]').length;
		if(ttlCity>=maxCityCnt)
			return;
		
		$('[name="addHoldTerm[]"]:last').closest('tr').after('<tr><td class="adm-detail-content-cell-l"><?=$addHold?></td><td class="adm-detail-content-cell-r"><input type="text" name="addHoldTerm[]"></td></tr>');
		
		if(ttlCity+1>=maxCityCnt)
			$("[onclick='IPOLIML_addCityHold()']").css('display','none');
	}
</script>
<?
foreach(array("statusOK","statusFAIL","statusSTORE","orderParams","itemProps","NDSUseCatalog","timeSend","commonHold","depature","loadGoods","articul","addJQ","noPVZnoOrder","hideNal","pvzID","pvzPicker","autoSelOne","prntActOrdr","orderIdMode","showInOrders","noVats","editStatisticalValue","countType","serverToTable","useOldAPI","turnOffRestrictsOS") as $code)
	imlOption::placeHint($code);

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt")){
	$errorStr=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt");
	if(strlen($errorStr)>0){?>
		<tr><td colspan='2'>
			<div class="adm-info-message-wrap adm-info-message-red">
			  <div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage('IPOLIML_FNDD_ERR_HEADER')?></div>
					<?=GetMessage('IPOLIML_FNDD_ERR_TITLE')?>
				<div class="adm-info-message-icon"></div>
			  </div>
			</div>
		</td></tr>
	<?}
}
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/hint.txt")){
	$updateStr=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/hint.txt");
	if(strlen($updateStr)>0){?>
		<tr id='IPOLIML_updtPlc'><td colspan='2'>
			<div class="adm-info-message-wrap">
				<div class="adm-info-message" style='color: #000000'>
					<div class='IPOLIML_clz' onclick='IPOLIML_clrUpdt()'></div>
					<div style='max-height:400px; overflow:auto;'>
						<?=$updateStr?>
					</div>
				</div>
			</div>
		</td></tr>
	<?}
}

$dost = imldriver::getDelivery();
if($dost){
	if($dost['ACTIVE'] != 'Y'){?>
	<tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<div class="adm-info-message-title"><?=GetMessage('IPOLIML_NO_ADOST_HEADER')?></div>
				<?=GetMessage('IPOLIML_NO_ADOST_TITLE')?>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr>
	<?}
}else{?>
	<tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<?if($converted){?>
				<div class="adm-info-message-title"><?=GetMessage('IPOLIML_NOT_CRTD_HEADER')?></div>
					<?=GetMessage('IPOLIML_NOT_CRTD_TITLE')?>				
			<?}else{?>
				<div class="adm-info-message-title"><?=GetMessage('IPOLIML_NO_DOST_HEADER')?></div>
					<?=GetMessage('IPOLIML_NO_DOST_TITLE')?>
			<?}?>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr>
<?}?>
<tr >
	<td align="center"><?=GetMessage("IPOLIML_LBL_YLOGIN")?>: <strong><?=COption::GetOptionString($module_id,'logIml','If you see this, something really bad have happend.')?></strong></td>
	<td align="center"><input type='button' onclick='IPOLIML_logoff()' value='<?=GetMessage('IPOLIML_LBL_DOLOGOFF')?>'></td>
</tr>
<tr><td></td><td align="center"><input type='button' id='IPOLIML_cacheKiller' onclick='IPOLIML_clearCache()' value='<?=GetMessage('IPOLIML_OTHR_CLRCACHE')?>'></td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_common")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["common"]);?>	

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_status")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["status"]);?>	
	
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_orderParams")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["orderParams"]);?>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_itemProps")?></td></tr>

<tr><td style="color:#555;" colspan="2">
	<?imlOption::placeFAQ('OPTS_GOODS')?>
</td></tr>

<?ShowParamsHTMLByArray($arAllOptions["itemProps"]);?>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_basket")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["basket"]);?>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_delivery")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["deliverySys"]);?>
<tr><td colspan="2"><?=GetMessage("IPOLIML_FAQ_DELIVERY")?></td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_OPT_paySystems")?></td></tr>
<tr><td colspan="2" style='text-align:center'><?=$paySysHtml?></td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_termDeliv")?></td></tr>
<tr>
	<td><?=GetMessage('IPOLIML_OPT_timeSend')?> <a href='javascript:void(0)' class='PropHint' onclick='return ipol_popup_virt("pop-timeSend", this);'></a></td>
	<td><select name='timeSend'>
		<?$tmpVal=COption::GetOptionString($module_id,'timeSend',false);
		for($i=9;$i<19;$i++){?><option value='<?=$i?>' <?=($tmpVal==$i)?'selected':''?>><?=$i?></option><?}?>
	</select></td>
</tr>
<tr>
	<td><?=GetMessage('IPOLIML_OPT_commonHold')?> <a href='javascript:void(0)' class='PropHint' onclick='return ipol_popup_virt("pop-commonHold", this);'></a></td>
	<td><input type='text' name='commonHold' value='<?=COption::GetOptionString($module_id,'commonHold','')?>'></td>
</tr>
<tr><td colspan="2" align="center"><?=GetMessage("IPOLIML_OPT_addHold")?></td></tr>
<?=$strReadyHold?>
<?if(count($arReadyHold)<count($IPOLIML_list['Region'])){?>
<tr><td><?=$addHold?></td><td><input type='text' name='addHoldTerm[]' ></td></tr>
	<?if(count($arReadyHold)+1<count($IPOLIML_list['Region'])){?>
		<tr><td colspan="2" align="center"><a href='javascript:void(0)' onclick='IPOLIML_addCityHold()'><?=GetMessage('IPOLIML_OTHR_ADDCITY')?></a></td></tr>
	<?}
}?>

<tr class="heading" ><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_OPT_services")?></td></tr>

<tr><td style="color:#555;" colspan="2">
	<?imlOption::placeFAQ('OPTS_SERVICES')?>
</td></tr>

<tr><td colspan="2"><table id='IPOLIML_serviceTable'>
<tr><th><?=GetMessage("IPOLIML_AS_NAME")?></th><th><?=GetMessage("IPOLIML_AS_ONTSHOW")?></th><th><?=GetMessage("IPOLIML_AS_BLOCK")?></th></tr>
<?
	$as = unserialize(COption::GetOptionString($module_id,"services",'a:0:{}'));
	$at = unserialize(COption::GetOptionString($module_id,"blockedServices",'a:0:{}'));
	foreach($IPOLIML_list['Service'] as $code => $descr){
		$sign = (is_array($descr)) ? $descr['Description'] : $descr;
		if(strpos(imldriver::toUpper($sign),GetMessage('IPOLIML_JS_SOD_VOZVRAT'))===false && strpos(imldriver::toUpper($sign),GetMessage('IPOLIML_JS_SOD_ZABOR'))===false && strpos(imldriver::toUpper($code),GetMessage('IPOLIML_JS_SOD_VOZVRAT'))===false && strpos(imldriver::toUpper($code),GetMessage('IPOLIML_JS_SOD_ZABOR'))===false){
		?>
			<tr>
				<td align='center'><?=$sign." [".$code."]"?></td>
				<td align='center'><input type='checkbox' name='services[<?=$code?>]' value='Y' <?=($as[$code])?"checked":""?> /></td>
				<td align='center'><input type='checkbox' name='blockedServices[<?=$code?>]' value='Y' <?=($at[$code])?"checked":""?> /></td>
			</tr>
		<?}
	}
?>
</table></td></tr>

<tr class="heading" onclick='IPOLIML_serverShow()' style='cursor:pointer;text-decoration:underline'>
	<td colspan="2" valign="top" align="center"><?=GetMessage("IPOLIML_HDR_service")?></td>
</tr> 
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OTHR_schet')?></td>
	<td>
	<?
		$tmpVal=COption::GetOptionString($module_id,'schet',0);
		echo $tmpVal;
		if($tmpVal>0){
	?> <input type='button' value='<?=GetMessage('IPOLIML_OTHR_schet_BUTTON')?>' onclick='IPOLIML_sbrosSchet()'/>
	<?}?>
	</td>
</tr>
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OTHR_lastModList')?></td>
	<td>
		<span id='IPOLIML_updtTime'><?=date("d.m.Y H:i:s",filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/references/PVZ.json"));?></span>
		<input type='button' value='<?=GetMessage('IPOLIML_OTHR_lastModList_BUTTON')?>' onclick='IPOLIML_syncList()'/>
	</td>
</tr>
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OPT_getOutLst')?></td>
	<td>
		<?	$optVal = COption::GetOptionString($module_id,'getOutLst',0);
			if($optVal>0) echo date("d.m.Y H:i:s",$optVal);
			else echo GetMessage('IPOLIML_OTHR_NOTCOMMITED');
		?>
		<input type='button' value='<?=GetMessage('IPOLIML_OTHR_getOutLst_BUTTON')?>' id='IPOLIML_suncOutb' onclick='IPOLIML_syncOutb()'/>
	</td>
</tr>
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OPT_lstShtPr')?></td>
	<td>
		<?	$optVal = COption::GetOptionString($module_id,'lstShtPr',0);
			if($optVal>0) echo date("d.m.Y H:i:s",$optVal);
			else echo GetMessage('IPOLIML_OTHR_NOTCOMMITED');
		?>
	</td>
</tr>
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OPT_useOldAPI')?></td>
	<td>
		<?	$optVal = COption::GetOptionString($module_id,'useOldAPI',0);
		?>
		<input type='checkbox' name='useOldAPI' id='useOldAPI' value='Y' <?=($optVal==='Y') ? 'checked' : ''?>>
	</td>
</tr>
<tr style='display:none' class='IPOLIML_service'>
	<td><?=GetMessage('IPOLIML_OPT_turnOffRestrictsOS')?></td>
	<td>
		<?	$optVal = COption::GetOptionString($module_id,'turnOffRestrictsOS',0);
		?>
		<input type='checkbox' name='turnOffRestrictsOS' id='turnOffRestrictsOS' value='Y' <?=($optVal==='Y') ? 'checked' : ''?>>
	</td>
</tr>