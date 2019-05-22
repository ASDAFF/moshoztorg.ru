<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	if(empty($arResult)){
		return;
	}
?><div class="seenitems">
	<div class="headingline">
		<div class="seearrowleft">
			<i class="flaticon-left"></i>
		</div>
		<p class="heading">Вы смотрели</p>
		<div class="seearrowright">
			<i class="flaticon-right"></i>
		</div>
	</div>
	<div class="itemscarousel">
		<div>
			<?
			$i = 0;
			foreach($arResult as $element){
				$product = MobileCatalog::byId($element['PRODUCT_ID']);
				echo $product->moreFields($element)->html('catalog', array(
					'tpl' => $this,
					'i' => $i++
				));
				if (($i+1)%2==0 && isset($arResult['PRODUCTS'][$i+1])) echo '</div><div>';
			}
			?>
		</div>
	</div>
</div>