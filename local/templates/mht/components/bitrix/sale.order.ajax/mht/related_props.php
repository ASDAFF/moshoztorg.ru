<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");

$style = (is_array($arResult["ORDER_PROP"]["RELATED"]) && count($arResult["ORDER_PROP"]["RELATED"])) ? "" : "display:none";
?>
<div class="bx_section" style="<?=$style?>">
	<h4><?=GetMessage("SOA_TEMPL_RELATED_PROPS")?></h4>
	<br />
	<?=PrintPropsForm($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"])?>
</div>
<?/* <p style="color:#f00;font-size: 18px;line-height: 1.25;font-family: 'pfdintextpro-regular';">Уважаемые покупатели! В связи с новогодними праздниками сроки доставки могут измениться.<br />Просьба уточнять сроки доставки у менеджеров магазина по телефону 8&nbsp;(800)&nbsp;550-47-47</p> */?>