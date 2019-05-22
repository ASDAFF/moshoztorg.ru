<style>
	.sortTr
	{
		cursor:pointer;
	}
	.sortTr:hover{opacity:0.7;}
	.mdTbl{overflow:hidden;}
	.IPOLIML_TblStOk td{
		background-color:#E2FCE2!important;
	}
	.IPOLIML_TblStErr td{
		background-color:#FFEDED!important;
	}
	.IPOLIML_TblStSnd td{
		background-color:#FCFCBF!important;
	}	
	.IPOLIML_TblStRej td{
		background-color:#F76868!important;
	}	
	.IPOLIML_TblStDel td{
		background-color:#E9E9E9!important;
	}

	.IPOLIML_TblStStr td{
		background-color:#FCFFCE!important;
	}
	.IPOLIML_TblStCor td{
		background-color:#D9FFCE!important;
	}	
	.IPOLIML_TblStPVZ td{
		background-color:#D9FFCE!important;
	}	
	.IPOLIML_TblStOtk td{
		background-color:#FFCECE!important;
	}	
	.IPOLIML_TblStDvd td{
		background-color:#ABFFAB!important;
	}

	.IPOLIML_TblStOk:hover td,.IPOLIML_TblStErr:hover td, IPOLIML_TblStSnd:hover td, IPOLIML_TblStStr:hover td, IPOLIML_TblStCor:hover td, IPOLIML_TblStPVZ:hover td, IPOLIML_TblStOtk:hover td, IPOLIML_TblStDvd:hover td{
		background-color:#E0E9EC!important;
	}
	.IPOLIML_crsPnt{
		cursor:pointer;
	}
	.mdTbl{
		border-bottom: 1px solid #DCE7ED;
		border-left: 1px solid #DCE7ED;
		border-right: 1px solid #DCE7ED;
		border-top: 1px solid #C4CED2;
	}
	#IPOLIML_flrtTbl{
		background: url("/bitrix/panel/main/images/filter-bg.gif") transparent;
		border-bottom: 1px solid #A0ABB0;
		border-radius: 5px 5px 5px;
		text-overflow: ellipsis;
		text-shadow: 0px 1px rgba(255, 255, 255, 0.702);
	}
	.IPOLIML_mrPd td{
		padding: 5px;
	}
</style>
<script type='text/javascript'>
	function IPOLIML_getTable(params)
	{
		if(typeof params == 'undefined')
			params={};
		
		var fltObj=IPOLIML_setFilter();
		
		for(var i in fltObj)
			params[i]=fltObj[i];
		
		params['pgCnt']=(typeof params['pgCnt'] == 'undefined')?$('#IPOLIML_tblPgr').val():params['pgCnt'];
		params['page']=(typeof params['page'] == 'undefined')?$('#IPOLIML_crPg').html():params['page'];
		params['by']=(typeof params['by'] == 'undefined')?'ID':params['by'];
		params['sort']=(typeof params['sort'] == 'undefined')?'DESC':params['sort'];
		params['action']='tableHandler';

		$('#IPOLIML_tblPls').find('td').css('opacity','0.4');

		$.ajax({
			url:"/bitrix/js/<?=$module_id?>/ajax.php",
			data:params,
			type:'POST',
			dataType: 'json',
			success:function(data){
				if(data['ttl']==0)
					$('#IPOLIML_flrtTbl').parent().html('<?=GetMessage('IPOLIML_OTHR_NO_REQ')?>');
				else
				{
					$('[onclick="IPOLIML_nxtPg(-1)"]').css('visibility','visible');
					$('[onclick="IPOLIML_nxtPg(1)"]').css('visibility','visible');
					if(data.cP==1)
						$('[onclick="IPOLIML_nxtPg(-1)"]').css('visibility','hidden');
					if(data.cP>=data.mP)
						$('[onclick="IPOLIML_nxtPg(1)"]').css('visibility','hidden');
					$('#IPOLIML_crPg').html(data.cP);
					
					$('#IPOLIML_ttlCls').html('<?=GetMessage('IPOLIML_TABLE_COLS')?> '+((parseInt(data.cP)-1)*data.pC+1)+' - '+Math.min(parseInt(data.ttl),parseInt(data.cP)*data.pC)+' <?=GetMessage('IPOLIML_TABLE_FRM')?> '+data.ttl);
					$('#IPOLIML_tblPls').html(data.html);
				}
			}
		});
	}
	
		
	function IPOLIML_delSign(oid)
	{
		if(confirm('<?=GetMessage("IPOLIML_JSC_SOD_IFDELETE")?>'))
			$.post(
				"/bitrix/js/<?=$module_id?>/ajax.php",
				{'action':'delReq','oid':oid},
				function(data){
					if(data.indexOf('GD:')===0)
					{
						alert(data.substr(3));
						IPOLIML_getTable();
					}
					else
						alert(data.substr(3));
				}
			);
	}
	
	function IPOLIML_killSign(oid)
	{
		alert('<?=GetMessage("IPOLIML_FUUU")?>');
		/* NOT WORKING
		if(confirm('<?=GetMessage("IPOLIML_JSC_SOD_IFKILL")?>'))
			$.post(
				"/bitrix/js/<?=$module_id?>/ajax.php",
				{'action':'killReq','oid':oid},
				function(data){
					if(data.indexOf('GD:')===0)
					{
						alert(data.substr(3));
						IPOLIML_getTable();
						if(IPOLIML_wndKillReq)
							IPOLIML_wndKillReq.Close();
					}
					else
						alert(data.substr(3));
				}
			);
		*/
	}
	
	IPOLIML_wndKillReq=false;
	function IPOLIML_callKillReq()
	{
		alert('<?=GetMessage("IPOLIML_FUUU")?>');
		/* NOT WORKING
		if(!IPOLIML_wndKillReq){
			IPOLIML_wndKillReq = new BX.CDialog({
				title: '<?=GetMessage('IPOLIML_OTHR_killReq_TITLE')?>',
				content: "<div><a href='javascript:void(0)' onclick='$(this).next().toggle(); return false;'>?</a><small style='display:none'><?=GetMessage('IPOLIML_OTHR_killReq_DESCR')?></small><br><?=GetMessage('IPOLIML_OTHR_killReq_LABEL')?> <input size='3' type='text' id='IPOLIML_delDeqOrId'><br><?=GetMessage('IPOLIML_OTHR_killReq_HINT')?></div>",
				icon: 'head-block',
				resizable: false,
				draggable: true,
				height: '145',
				width: '200',
				buttons: ['<input type="button" value="<?=GetMessage('IPOLIML_OTHR_killReq_BUTTON')?>" onclick="IPOLIML_killSign($(\'#IPOLIML_delDeqOrId\').val())"/>']
			});
		}
		else
			$('#IPOLIML_delDeqOrId').val('');
		IPOLIML_wndKillReq.Show();
		*/
	}
	
	function IPOLIML_clrCls()
	{
		$('.adm-list-table-cell-sort-up').removeClass('adm-list-table-cell-sort-up');
		$('.adm-list-table-cell-sort-down').removeClass('adm-list-table-cell-sort-down');
	}
	
	function IPOLIML_sort(wat,handle)
	{
		if(handle.hasClass("adm-list-table-cell-sort-down"))
		{
			IPOLIML_clrCls();
			handle.addClass("adm-list-table-cell-sort-up");
			IPOLIML_getTable({'by':wat,'sort':'ASC'});
		}
		else
		{
			if(handle.hasClass("adm-list-table-cell-sort-up"))
			{
				IPOLIML_clrCls();
				IPOLIML_getTable();
			}
			else
			{
				IPOLIML_clrCls();
				handle.addClass("adm-list-table-cell-sort-down");
				IPOLIML_getTable({'by':wat,'sort':'DESC'});
			}
		}
	}
	
	function IPOLIML_nxtPg(cntr)
	{
		var page=parseInt($("#IPOLIML_crPg").html())+cntr;
		if(page<1)
			page=1;
			
		if(page!=parseInt($("#IPOLIML_crPg").html()))
		{
			IPOLIML_getTable({"page":page});
			$("#IPOLIML_crPg").html(page);
		}
	}
	
	function IPOLIML_shwPrms(handle)
	{
		handle.siblings('a').hide();
		handle.css('height','auto');
		var height=handle.height();
		handle.css('height','0px');
		handle.animate({'height':height},500);
	}
	
	function IPOLIML_setFilter()
	{
		var params={};
		$('[id^="IPOLIML_Fltr_"]').each(function(){
			var crVal=$(this).val();
			if(crVal)
				params['F'+$(this).attr('id').substr(13)]=crVal;
		});
		return params;
	}

	function IPOLIML_resFilter()
	{
		$('[id^="IPOLIML_Fltr_"]').each(function(){
			$(this).val('');
		});
	}
	
	function IPOLIML_printShtr()
	{
		if(confirm('<?=GetMessage('IPOLIML_OTHR_lstShtPr_ALERT').date("d.m.Y H:i:s",COption::GetOptionString($module_id,'lstShtPr',0))."?"?>'))
			$.post(
				"/bitrix/js/<?=$module_id?>/ajax.php",
				{'action':'prntShtr'},
				function(data){
					data=data.split(',');
					var quer='';
					for(var i in data)
						if(data[i])
							quer+='ORDER_ID[]='+data[i]+'&';

					if(quer)
						window.open('/bitrix/js/<?=$module_id?>/printBK.php?'+quer+'&isFromOpt=1');
				}
			);
	}
	
	$(document).ready(function(){
		IPOLIML_getTable();
	});
	
</script>

<div id="pop-statuses" class="b-popup" style="display: none; ">
	<div class="pop-text"><?=GetMessage("IPOLIML_HELPER_statuses")?></div>
	<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
</div>

<tr><td colspan='2'>
		<table id='IPOLIML_flrtTbl'>
		  <tbody>
			<tr class='IPOLIML_mrPd'>
			  <td><?=GetMessage('IPOLIML_JS_SOD_number')?></td><td><input type='text' class='adm-workarea' id='IPOLIML_Fltr_>=ORDER_ID'><span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span><input type='text' class='adm-workarea' id='IPOLIML_Fltr_<=ORDER_ID'></td>
			</tr>
			<tr class='IPOLIML_mrPd'>
				<td><?=GetMessage('IPOLIML_JS_SOD_STATUS')?> <a href='#' class='PropHint' onclick='return ipol_popup_virt("pop-statuses", this);'></a></td>
				<td>
					<select id='IPOLIML_Fltr_STATUS'>
						<option value=''></option>
						<option value='NEW'>NEW</option>
						<option value='ERROR'>ERROR</option>
						<option value='OK'>OK</option>
						<option value='STORE'>STORE</option>
						<option value='CORIER'>CORIER</option>
						<option value='PVZ'>PVZ</option>
						<option value='OTKAZ'>OTKAZ</option>
						<option value='DELIVD'>DELIVD</option>
					</select>
				</td>
			</tr>
			<tr class='IPOLIML_mrPd'>
			  <td><?=GetMessage('IPOLIML_TABLE_SHTRC')?></td><td><input type='text' class='adm-workarea' id='IPOLIML_Fltr_>=BARCODE'><span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span><input type='text' class='adm-workarea' id='IPOLIML_Fltr_<=BARCODE'></td>
			</tr>
			<tr class='IPOLIML_mrPd'>
				<td><?=GetMessage('IPOLIML_TABLE_UPTIME')?></td>
				<td>
					<div class="adm-input-wrap adm-input-wrap-calendar">
						<input type='text' class='adm-workarea' id='IPOLIML_Fltr_>=UPTIME' name='IPOLIMLupF' disabled>
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'IPOLIMLupF', form: '', bTime: true, bHideTime: false});"></span>
					</div>
					<span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span>
					<div class="adm-input-wrap adm-input-wrap-calendar">
						<input type='text' class='adm-workarea' id='IPOLIML_Fltr_<=UPTIME' name='IPOLIMLupD' disabled>
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'IPOLIMLupD', form: '', bTime: true, bHideTime: false});"></span>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan='2'><div class="adm-filter-bottom-separate" style="margin-bottom:0px;"></div></td>
			</tr>
			<tr class='IPOLIML_mrPd'>
				<td colspan='2'><input class="adm-btn" type="button" value="<?=GetMessage('MAIN_FIND')?>" onclick="IPOLIML_getTable()">&nbsp;&nbsp;&nbsp;<input class="adm-btn" type="button" value="<?=GetMessage('MAIN_RESET')?>" onclick="IPOLIML_resFilter()"></td>
			</tr>
		  </tbody>
		</table>
		<br><br>
		<table class="adm-list-table mdTbl">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell"><div></div></td>
					<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLIML_sort("ID",$(this))'><div class='adm-list-table-cell-inner'>ID</div></td>
					<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLIML_sort("ORDER_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_TABLE_ORDN')?></div></td>
					<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLIML_sort("STATUS",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_JS_SOD_STATUS')?></div></td>
					<td class="adm-list-table-cell"><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_TABLE_PARAM')?></div></td>
					<td class="adm-list-table-cell"><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_TABLE_MESS')?></div></td>
					<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLIML_sort("BARCODE",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_TABLE_SHTRC')?></div></td>
					<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLIML_sort("UPTIME",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLIML_TABLE_UPTIME')?></div></td>
				</tr>
			</thead>
			<tbody id='IPOLIML_tblPls'>
			</tbody>
		</table>
		<div class="adm-navigation">
			<div class="adm-nav-pages-block">
				<span class="adm-nav-page adm-nav-page-prev IPOLIML_crsPnt" onclick='IPOLIML_nxtPg(-1)'></span>
				<span class="adm-nav-page-active adm-nav-page" id='IPOLIML_crPg'>1</span>
				<span class="adm-nav-page adm-nav-page-next IPOLIML_crsPnt" onclick='IPOLIML_nxtPg(1)'></span>
			</div>
			<div class="adm-nav-pages-total-block" id='IPOLIML_ttlCls'><?=GetMessage('IPOLIML_TABLE_COLS?')?> 1 – 5 <?=GetMessage('IPOLIML_TABLE_FRM')?> 5</div>
			<div class="adm-nav-pages-number-block">
				<span class="adm-nav-pages-number">
					<span class="adm-nav-pages-number-text"><?=GetMessage('admin_lib_sett_rec')?></span>
					<select id='IPOLIML_tblPgr' onchange='IPOLIML_getTable()'>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20" selected="selected">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="0"><?=GetMessage('MAIN_OPTION_CLEAR_CACHE_ALL')?></option>
					</select>
				</span>
			</div>
		</div>
		
		<?/*<input type='button' style='margin-top:20px' value='<?=GetMessage('IPOLIML_OTHR_killReq_BUTTON')?>' onclick='IPOLIML_callKillReq()'>&nbsp;*/?>
		<input type='button' style='margin-top:20px' value='<?=GetMessage('IPOLIML_OTHR_lstShtPr_BUTTON')?>' onclick='IPOLIML_printShtr()'>&nbsp;
		<input type='button' style='margin-top:20px' value='<?=GetMessage('IPOLIML_OTHR_getOutLst_BUTTON_OT')?>' onclick='IPOLIML_syncOutb()'/>
	</td></tr>