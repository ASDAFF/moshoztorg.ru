<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];
$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column
?>
<div class="bx_ordercart product-list">
	<h4><?=GetMessage("SALE_PRODUCTS_SUMMARY");?></h4>
	<div>
		<?
			foreach($arResult['GRID']['ROWS'] as $row){
				if($row["data"]["MODULE"] == "catalog" && $row["data"]["PRICE"] > 0){
					$product = MHT\Product::byID($row['data']['PRODUCT_ID']);
					if(!$product){
						continue; 
					}
					echo $product->moreFields($row['data'])->html('order');
				}else{
					CSaleBasket::Delete(intval($row["id"]));
				}
			}
		?>
	</div>

    <a href="<?=$arParams['PATH_TO_BASKET']?>" class="back_to_basket">Вернуться в корзину</a>
    <div class="clear"></div>

</div>

