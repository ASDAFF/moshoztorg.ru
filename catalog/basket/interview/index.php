<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<div class="styles_page">
	<div class="styles">
		<?if($_REQUEST['id']):?>
		<h1>Анкета покупателя. Заказ №<?=$_REQUEST['id']?></h1>
		<?else:?>
		<h1>Анкета покупателя</h1>
		<?endif;?>
		<section class="quality-interview">
			<?
			// Надстройка для MHT
			$res = CIBlock::GetList(
				Array(), 
				Array(
					"TYPE"=>'mht_products'
				), true
			);
			while($ar_res = $res->Fetch()) {
				$CATALOG_IDs[] = $ar_res['ID'];
			}
			$APPLICATION->IncludeComponent(
				"webprofy:sale.order.interview",
				"",
				array(
					'CATALOG_IBLOCK_ID' => $CATALOG_IDs, // ID инфоблока Каталога
					'COMMENTS_IBLOCK_ID' => 505,
					'PROP_GROUP' => 5, // Группа свойст товара, используемых для анкеты /bitrix/admin/sale_order_props_group.php?lang=ru
					"BLOG_URL" => "catalog_comments"
				),
				false
			);
			?>
		</section>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>