<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	if(empty($arResult)){
		return;
	}
?>
<div class="recently_viewed block js-fit">
	<div class="title">Вы смотрели</div>
	<div class="products_block slick_block js-fit">
	<?
		$i = 0;
		foreach($arResult as $element){
			$product = MHT\Product::byId($element['PRODUCT_ID']);
			echo $product->moreFields($element)->html('catalog', array(
				'tpl' => $this,
				'i' => $i++
			));
		}
	?>
	</div>
</div>