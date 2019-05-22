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

	.ipol_smallHeader{
		cursor: pointer;
		display:block;
		color:#2E569C;
	}

	.ipol_subFaq{
		margin-bottom:10px;
	}
	
	img{border: 1px dotted black;}
	.IPOLIML_optName{
		font-weight: bold;
	}
	.IML_warning{
		color:red;
	}
	.IML_converted{
		<?=($converted)?'':'display:none;'?>
	}
	.IML_notConverted{
		<?=($converted)?'display:none;':''?>
	}
	.ipol_phpCode{
		color:#AC12B1
	}
	.ipol_comment{
		color:#008000;
	}
</style>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLIML_FAQ_HDR_SETUP')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?imlOption::placeFAQ('WTF')?>
	<?imlOption::placeFAQ('HIW')?>
</td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLIML_FAQ_HDR_ABOUT')?></td></tr> 
<tr><td style="color:#555;" colspan="2">
	<?imlOption::placeFAQ('TURNON')?>
	<?imlOption::placeFAQ('DELSYS')?>
</td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLIML_FAQ_HDR_WORK')?></td></tr>
<tr><td style="color:#555; " colspan="2">
	<?imlOption::placeFAQ('DELIVERYPRICESERVICE')?>
	<?imlOption::placeFAQ('SEND')?>
	<?imlOption::placeFAQ('PELENG')?>
	<?imlOption::placeFAQ('PRINT')?>
	<?imlOption::placeFAQ('GOODS')?>
	
	<?imlOption::placeFAQ('MULTISITE')?>
	<?imlOption::placeFAQ('COMPONENT')?>
	<?imlOption::placeFAQ('DELIVERYPRICE')?>
	<?imlOption::placeFAQ('ERRORS')?>
	<?imlOption::placeFAQ('ADD')?>
</td></tr>