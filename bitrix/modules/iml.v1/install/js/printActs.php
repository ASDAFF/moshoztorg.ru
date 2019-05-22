<?
/*
	Это пример шаблона печати акта приема-передачи и заказов.
	Его можно использовать для создания своего шаблона, однако стоит учитывать, что он не расчитан на большой список заказов (может просто не уместиться на одном листе).
	Печать заказов заключается в печати штрихкодов для каждого заказа.
	
	Перед использованием проверьте, чтобы файл был в той же кодировке, что и сайт, иначе русские символы будут искажены. В первую очередь это относится к сайтам с UTF-кодировкой.
	
	В рамках техподдержки не рассматриваются вопросы по верстке, программированию и подстраиванию этого шаблона под сайты.
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
if($SALE_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$module_id = "iml.v1";
CModule::IncludeModule($module_id);

$shpName=COption::GetOptionString($module_id,'strName','');

if(CModule::IncludeModule("sale")){
	$arOrders = imldriver::getBK(explode(":", $ORDER_ID));
	unset($ORDER_ID);

	if(is_array($arOrders['ORDERS']) && count($arOrders['ORDERS']) > 0){?>
		<!-- Будущий шаблон акта --> 					
			<style type="text/css">
			/* акт */
			<!--
				@page { size: 21cm 29.7cm; margin-left: 1cm; margin-right: 1cm; margin-top: 1cm; margin-bottom: 1cm }
				P { margin-bottom: 0.21cm; direction: ltr; color: #000000; widows: 2; orphans: 2 }
			-->
			div.block {
				width: 100%;
				clear: right;
				min-height: 100px;
				page-break-after:always;
			}
			div.block:last-child{
				page-break-after:auto;
			}
			.header{
				text-align:center;
				font-weight:bold;
			}
			.right{
				text-align:right;
			}
			.text8{
				font-size: 8pt;
			}
			.breaker{
				height: 1cm;
			}
			</style>
			<div class="block">			
				<p class='header'>АКТ ПРИЕМА-ПЕРЕДАЧИ</p>
				<p class='header'>По агентскому договору №НОМЕР_ДОГОВОРА от ДАТА.</p>
				<p class='text8'>Г. Москва</p>
				<p class='right text8'><?=date("d.m.Y")?></p>
				<p><br></p>
				<p>Индивидуальный предприниматель Иванов Иван Иполович, именуемое в дальнейшем &laquo;Принципал&raquo;, с одной стороны, и ООО &laquo;Ай Эм Логистикс&raquo;, в лице Заведующего склада Емельянова А., действующего на основании Доверенности, именуемые в дальнейшем &laquo;Стороны&raquo;, настоящим Актом удостоверяют, что в соответствии с условиями Агентского договора №НОМЕР_ДОГОВОРА от ДАТА. Принципал передал, а Агент принял заказы согласно нижеследующему:</p>
				<p><br></p>
				<table BORDER='1' BORDERCOLOR="#00000a" CELLPADDING='2' CELLSPACING='0'>
					<tr VALIGN='TOP'>
						<td><p>№ п/п</p></td>
						<td><p>Номер Заказа</p></td>
						<td><p>Кол-во Мест, ШТ</p></td>
						<td><p>Город&nbsp;получателя</p></td>
					</tr>
					<?
						$ttlCnt=0;
						foreach($arOrders['ORDERS'] as $key => $arOrder){?>
						<TR VALIGN='TOP'>
							<td><p><?=$key+1?></p></td>
							<td><p><?=$arOrder['orderId']?></p></td>
							<td><p><?=$arOrder['cnt']?></p></td>
							<td WIDTH=113><p><?=($arOrder['city']) ? $arOrder['city'] : "Не указан"?></p></td>
						</TR>
					<?
						$ttlCnt+=$arOrder['cnt'];
					}?>
					<tr VALIGN='TOP'>
						<td COLSPAN=2><p>Итого:</p></td>
						<td><p><?=$ttlCnt?></p></td>
						<td><p><BR></p></td>
					</tr>
				</TABLE>
				<p>Итого передано <?=$ttlCnt?> единиц.</p>
				<p>+</p>
				<p>Передаваемые Заказы в ненарушенных индивидуальных упаковках, заклеенные фирменным скотчем, бумагой, исключающих доступ третьих лиц, без видимых механических повреждений.</p>
				<p>Заказы промаркированы городом и нумерованы уникальными номерами, соответствующими номерам, указанным в электронной Заявке.</p>
				<p><br></p>
				<table BORDER=0 CELLPADDING=9 CELLSPACING=0>
					<tr VALIGN=TOP>
						<td WIDTH=381><p class='text8'>Принципал</p></td>
						<td WIDTH=381><p class='text8'>Агент</p></td>
					</tr>
					<tr VALIGN=TOP>
						<td><p class='text8'>Отпустил от индивидуальный предприниматель Иванов Иван Иполович</p></td>
						<td><p class='text8'>Принял от ООО &laquo;Эй Ам Логистикс&raquo;</p></td>
					</tr>
					<tr VALIGN=TOP>
						<td>
							<p class='text8'>___________________________/__________________________/</p>
							<p class='text8'>______________________________________________________</p>
							<p class='text8'>/должность сотрудника/</p>
							<p><BR></p>
							<p class='text8'>МП</p>
						</td>
						<td>
							<p class='text8'>_____________________________________________________</p>
							<p class='text8'>_____________________________________________________</p>
							<p class='text8'>/должность сотрудника склада/</p>
							<p><BR></p>
							<p class='text8'>МП</p>
						</td>
					</tr>
				</table>
				<p class='breaker'></p>
			</div>
			<?if($_REQUEST['ORDERS']){// печатаем заказы?>
				<? // пример распечатки только штрихкодов?>
				<div class='block'><?imldriver::printBKs($arOrders);?></div>
				<? // Пример распечатки заказов по шаблонам (закоменчено, чтобы по умолчанию все работало. Кому надо - тот полезет разбираться и все поймет. Я очень на это надеюсь.)
					/*
						Суть в том, что здесь просто-напросто берется файл-шаблон печати заказа, и растягивается на несколько заказов. Предполагается, что в файл-шаблон печати УЖЕ вставлен функционал штрихкодов.
						Получить HTML штрихкода можно одной функцией: imldriver::printBKs($orderIds,$template), где $orderIds - id-шники заказов (массив или одиночный int), а $template - путь к шаблону печати штрихкодов, в котором установлены все стили. По умолчанию это - /bitrix/js/iml.v1/bkTemplate.php. 
				?>
				<?
					$templatePath = '/bitrix/admin/reports/bill.php'; // путь к файлу-шаблону распечатки
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$templatePath)){
						// предполагается, что необходимые шаблону массивы будут заполнены стараниями ваших доблестных программистов
						$strOfTemplate = '';
						foreach($arOrders['ORDERS'] as $ordMass){
							$ORDER_ID = $ordMass['orderId'];
							$arOrder  = CSaleOrder::GetById($ordMass['orderId']);
							ob_start();
							include($_SERVER['DOCUMENT_ROOT'].$templatePath);
							$strOfTemplate .= ob_get_contents();
							ob_end_clean();
						}
						// достаем стили шаблона
						preg_match_all("|<style[^>]*>(.*?)</style>|s", $strOfTemplate, $matches);?>
						<style><?=$matches[0][0]?></style>
						<?
						// достаем содержимое шаблона
						preg_match_all("|<body[^>]*>(.*?)</body>|s", $strOfTemplate, $matches, PREG_PATTERN_ORDER);
						foreach($matches[1] as $page){?>
							<div class='block'>
								<?=$page?>
							</div>
						<?}
					}*/
				?>
				<? // END ?>
			<?}
		}
	}
?>