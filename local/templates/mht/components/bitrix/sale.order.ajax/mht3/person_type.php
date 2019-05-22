<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	if(count($arResult["PERSON_TYPE"]) > 1){
		?>
	    	<div class="row title">
	        	<div class="col">
	               <h4><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></h4>
	            </div>
	        </div>
	        <div class="row">
	        	<div class="col person_types_block">
					<?
						foreach($arResult["PERSON_TYPE"] as $v){
							?>
				               <div class="person_type_block">
				                  <input
				                  	type="radio"
				                  	<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?>
				                  	value="<?=$v["ID"]?>"
				                  	onClick="submitForm()"
				                  	name="PERSON_TYPE"
				                  	id="PERSON_TYPE_<?=$v["ID"]?>"
				                >
				                  <label for="PERSON_TYPE_<?=$v["ID"]?>"><?=$v["NAME"]?></label>
				               </div>
				            <?

					?>
	        	</div>
	        </div>
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>" />
		<?
		return;
	}

	if(intval($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0){
		//for IE 8, problems with input hidden after ajax
		?>
			<span style="display:none;">
				<input type="text" name="PERSON_TYPE" value="<?=intval($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
				<input type="text" name="PERSON_TYPE_OLD" value="<?=intval($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
			</span>
		<?
		return;
	}

	foreach($arResult["PERSON_TYPE"] as $v){
		?>
			<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>" />
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>" />
		<?
	}
?>