<?
	IncludeModuleLangFile(__FILE__);

	class imlOption extends imlHelper{
		/*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
													Отображение опций
		== placeFAQ ==  == placeHint ==  == makeSelect ==
		()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


		function placeFAQ($code){?>
				<a class="ipol_header" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLIML_FAQ_'.$code.'_TITLE')?></a>
				<div class="ipol_inst"><?=GetMessage('IPOLIML_FAQ_'.$code.'_DESCR')?></div>
		<?}

		function placeHint($code){?>
			<div id="pop-<?=$code?>" class="b-popup" style="display: none; ">
				<div class="pop-text"><?=GetMessage("IPOLIML_HELPER_".$code)?></div>
				<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
			</div>
		<?}

		function makeSelect($id,$vals,$def=false,$atrs=''){?>
			<select <?if($id){?>name='<?=$id?>' id='<?=$id?>'<?}?> <?=$atrs?>>
			<?foreach($vals as $val => $sign){?>
				<option value='<?=$val?>' <?=((string)$def == (string)$val)?'selected':''?>><?=$sign?></option>
			<?}?>
			</select>
		<?}
	}
?>