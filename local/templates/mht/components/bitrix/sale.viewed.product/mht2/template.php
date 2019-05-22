<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	if(empty($arResult)){
		return;
	}
?>
<div class="h2">вы смотрели</div>
<div class="viewed_products">
	<?
		$i=0;
		foreach($arResult as $element){
			$product = MHT\Product::byId($element['PRODUCT_ID']);
			echo $product->moreFields($element)->html('recent', array(
				'tpl' => $this,
				'i' => $i
			));
			$i++;
		}
	?>
</div>
