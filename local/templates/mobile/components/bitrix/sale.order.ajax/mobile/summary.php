<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];
$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column
?>
<div class="formline">
	<p class="formheading"><?=GetMessage("SALE_PRODUCTS_SUMMARY");?></p>
	<?
		foreach($arResult['GRID']['ROWS'] as $row){
			if($row["data"]["MODULE"] == "catalog" && $row["data"]["PRICE"] > 0){
				$product = MobileCatalog::byID($row['data']['PRODUCT_ID']);
				if(!$product){
					continue;
				}
				echo $product->moreFields($row['data'])->html('order');
			}else{
				CSaleBasket::Delete(intval($row["id"]));
			}
		}
	?>

	<?
	/*
	 <div class="orderformitem">
		<div class="orderformitemimg">
			<img src="images/demo/cart1.png" alt="">
		</div>
		<div class="orderformiteminfo">
			<div class="orderformiteminfotop">
				<a href="#" target="_blank">
					<p class="cartitemname">Шлепанцы мужские Fashy Sport Line 41 пластик черный голубой</p>
				</a>
			</div>
			<div class="orderformiteminfobot">
				<p class="cartitempriceforone">205</p>
				<p class="cartitemquantity">7 шт</p>
				<p class="cartitempriceforall">1435</p>
			</div>
		</div>
	</div>
	 */
	?>
</div>
<div class="formline">
	<p class="formheading"><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></p>
	<textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style="max-width:100%;min-height:120px"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
	<input type="hidden" name="" value="">

	<a href="javascript:void();" onclick="submitForm('Y'); return false;" class="checkout greenbutton full"><?=GetMessage("SOA_TEMPL_BUTTON")?></a>

	<p class="user_license">Нажимая кнопку «<?=GetMessage("SOA_TEMPL_BUTTON")?>»- вы соглашаетесь с <a href="#agreement-content"  data-action="pupop">Условиями продажи товаров</a></p>

	<div id="agreement-content">
		<?
		$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => SITE_DIR.'/include/agreement.php'
		));
		?>
	</div> 
</div>