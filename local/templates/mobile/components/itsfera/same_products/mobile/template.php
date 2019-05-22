<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?if(!empty($arResult['SAME_PRODUCTS'])){?>


	<div class="catalogitempoh">
		<div class="headingline">
			<div class="poharrowleft">
				<i class="flaticon-left"></i>
			</div>
			<p class="heading">Похожие товары</p>
			<div class="poharrowright">
				<i class="flaticon-right"></i>
			</div>
		</div>
		<div class="itemscarousel">
			<?/*<div>
				<div class="productitem">
					<div class="photoblock">
						<a href="#">
							<img src="/html/images/demo/evian.png" alt="">
						</a>
					</div>
					<div class="informationblock">
						<a href="#"><h3>EVIAN</h3></a>
						<div class="articulblock">
							<p class="articul">55533695</p>
							<div class="divstars">
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
							</div>
						</div>
						<p class="fullname">Вода минеральная негазированная EVIAN 0.33л 6 бут/упаковка</p>
						<div class="priceblock">
							<p class="price">368</p>
							<a href="#" class="tocart"><i class="flaticon-shopping-basket-1"></i></a>
						</div>
					</div>
				</div>
				<div class="productitem">
					<div class="photoblock">
						<a href="#">
							<img src="/html/images/demo/essentuko.png" alt="">
						</a>
					</div>
					<div class="informationblock">
						<a href="#"><h3>Ессентуки (Длинное название товара в три строки)</h3></a>
						<div class="articulblock">
							<p class="articul">55533695</p>
							<div class="divstars">
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
							</div>
						</div>
						<p class="fullname">Вода минеральная газированная Ессентуки №17 0.54д 20 бут/упаковка</p>
						<div class="priceblock">
							<p class="price">36800</p>
							<a href="#" class="tocart"><i class="flaticon-shopping-basket-1"></i></a>
						</div>
					</div>
				</div>
			</div>*/?>
			<div>
				<?
				foreach($arResult['SAME_PRODUCTS'] as $i => $product){
					echo $product->html('catalog', array(
						'i' => $i
					));

					if (($i+1)%2==0 && isset($arResult['SAME_PRODUCTS'][$i+1])) echo '</div><div>';
				}
				?>
				<?/*<div class="productitem">
					<div class="photoblock">
						<a href="#">
							<img src="images/demo/evian.png" alt="">
						</a>
					</div>
					<div class="informationblock">
						<a href="#"><h3>EVIAN</h3></a>
						<div class="articulblock">
							<p class="articul">55533695</p>
							<div class="divstars">
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
							</div>
						</div>
						<p class="fullname">Вода минеральная негазированная EVIAN 0.33л 6 бут/упаковка</p>
						<div class="priceblock">
							<p class="price">368</p>
							<a href="#" class="tocart"><i class="flaticon-shopping-basket-1"></i></a>
						</div>
					</div>
				</div>
				<div class="productitem">
					<div class="photoblock">
						<a href="#">
							<img src="images/demo/essentuko.png" alt="">
						</a>
					</div>
					<div class="informationblock">
						<a href="#"><h3>Ессентуки (Длинное название товара в три строки)</h3></a>
						<div class="articulblock">
							<p class="articul">55533695</p>
							<div class="divstars">
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
								<i class="flaticon-star"></i>
							</div>
						</div>
						<p class="fullname">Вода минеральная газированная Ессентуки №17 0.54д 20 бут/упаковка</p>
						<div class="priceblock">
							<p class="price">36800</p>
							<a href="#" class="tocart"><i class="flaticon-shopping-basket-1"></i></a>
						</div>
					</div>
				</div>*/?>
			</div>
		</div>
	</div>



	<?/*
	<div class="similar_products">
		<h2>похожие товары</h2>
	</div>
	<div class="catalog_page">
		<div class="catalog_block">
			<div class="catalog wide">
				<div class="products_block js-fit">

				</div>
			</div>
		</div>
	</div>*/?>
<?}?>