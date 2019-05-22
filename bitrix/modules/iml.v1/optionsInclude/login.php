<style>
	.ipol_header {
		font-size: 16px;
		cursor: pointer;
		display:block;
		color:#2E569C;
	}

	.ipol_inst {
		display:none; 
		margin-left:10px;
		margin-top:10px;
	}
	img{border: 1px dotted black;}
</style>
<script>
	function IPOLIML_auth(){
		$("[onclick='IPOLIML_auth()']").attr('disabled','disabled');
		var login    = $('#IPOLIML_login').val();
		var password = $('#IPOLIML_pass').val();
		
		if(!login){
			alert('<?=GetMessage("IPOLIML_ALRT_NOLOGIN")?>');
			$("[onclick='IPOLIML_auth()']").removeAttr('disabled');
			return;
		}
		if(!password){
			alert('<?=GetMessage("IPOLIML_ALRT_NOPASS")?>');
			$("[onclick='IPOLIML_auth()']").removeAttr('disabled');
			return;
		}
		$.post(
			"/bitrix/js/<?=$module_id?>/ajax.php",
			{
				'action'     : 'auth',
				'login'      : login,
				'password'   : password,
			},
			function(data){
				if(data.trim().indexOf('G')===0){
					alert(data.trim().substr(1));
					window.location.reload();
				}
				else{
					if(data.trim()=='404')
						alert("<?=GetMessage('IPOLIML_ALRT_404WTD')?>".replace('#akkName#',login));
					else
						alert(data);
					$("[onclick='IPOLIML_auth()']").removeAttr('disabled');
					$('.ipol_inst').css('display','block');
					$('#ipol_mistakes').css('display','block');
				}
			}
		);
	}
	function IPOLIML_doSbmt(e){
		if(e.keyCode==13)
			IPOLIML_auth();
	}
	
	$(document).ready(function(){
		$('#IPOLIML_login').on('keyup',IPOLIML_doSbmt);
		$('#IPOLIML_pass').on('keyup',IPOLIML_doSbmt);
	});
</script>
<tr><td><?=GetMessage('IPOLIML_LBL_LOGIN')?></td><td><input type='text' id='IPOLIML_login'></td></tr>
<tr><td><?=GetMessage('IPOLIML_LBL_PASSWORD')?></td><td><input type='password' id='IPOLIML_pass'></td></tr>
<tr><td></td><td><input type='button' value='<?=GetMessage('IPOLIML_LBL_AUTHORIZE')?>' onclick='IPOLIML_auth()'></td></tr>

<tr><td style="color:#555;" colspan="2">
	<a class="ipol_header" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLIML_FAQ_API_TITLE')?></a>
	<div class="ipol_inst"><?=GetMessage('IPOLIML_FAQ_API_DESCR')?></div>
</td></tr>