<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	if(empty($arResult)){
		return;
	}
?><div class="similar_products">
		<h2>вы смотрели</h2>
	</div>
	<div class="catalog_page">
	<div class="catalog_block">
	<div class="catalog wide">
	<div class="products_block slick_block js-fit prod-slider">

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
	</div>
	</div>
	</div>