<?
/*
	��� ������ ������� ������ ���� ������-�������� � �������.
	��� ����� ������������ ��� �������� ������ �������, ������ ����� ���������, ��� �� �� �������� �� ������� ������ ������� (����� ������ �� ���������� �� ����� �����).
	������ ������� ����������� � ������ ���������� ��� ������� ������.
	
	����� �������������� ���������, ����� ���� ��� � ��� �� ���������, ��� � ����, ����� ������� ������� ����� ��������. � ������ ������� ��� ��������� � ������ � UTF-����������.
	
	� ������ ������������ �� ��������������� ������� �� �������, ���������������� � ������������� ����� ������� ��� �����.
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
		<!-- ������� ������ ���� --> 					
			<style type="text/css">
			/* ��� */
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
				<p class='header'>��� ������-��������</p>
				<p class='header'>�� ���������� �������� ������_�������� �� ����.</p>
				<p class='text8'>�. ������</p>
				<p class='right text8'><?=date("d.m.Y")?></p>
				<p><br></p>
				<p>�������������� ��������������� ������ ���� ��������, ��������� � ���������� &laquo;���������&raquo;, � ����� �������, � ��� &laquo;�� �� ���������&raquo;, � ���� ����������� ������ ���������� �., ������������ �� ��������� ������������, ��������� � ���������� &laquo;�������&raquo;, ��������� ����� ������������, ��� � ������������ � ��������� ���������� �������� ������_�������� �� ����. ��������� �������, � ����� ������ ������ �������� ��������������:</p>
				<p><br></p>
				<table BORDER='1' BORDERCOLOR="#00000a" CELLPADDING='2' CELLSPACING='0'>
					<tr VALIGN='TOP'>
						<td><p>� �/�</p></td>
						<td><p>����� ������</p></td>
						<td><p>���-�� ����, ��</p></td>
						<td><p>�����&nbsp;����������</p></td>
					</tr>
					<?
						$ttlCnt=0;
						foreach($arOrders['ORDERS'] as $key => $arOrder){?>
						<TR VALIGN='TOP'>
							<td><p><?=$key+1?></p></td>
							<td><p><?=$arOrder['orderId']?></p></td>
							<td><p><?=$arOrder['cnt']?></p></td>
							<td WIDTH=113><p><?=($arOrder['city']) ? $arOrder['city'] : "�� ������"?></p></td>
						</TR>
					<?
						$ttlCnt+=$arOrder['cnt'];
					}?>
					<tr VALIGN='TOP'>
						<td COLSPAN=2><p>�����:</p></td>
						<td><p><?=$ttlCnt?></p></td>
						<td><p><BR></p></td>
					</tr>
				</TABLE>
				<p>����� �������� <?=$ttlCnt?> ������.</p>
				<p>+</p>
				<p>������������ ������ � ������������ �������������� ���������, ���������� ��������� �������, �������, ����������� ������ ������� ���, ��� ������� ������������ �����������.</p>
				<p>������ �������������� ������� � ���������� ����������� ��������, ���������������� �������, ��������� � ����������� ������.</p>
				<p><br></p>
				<table BORDER=0 CELLPADDING=9 CELLSPACING=0>
					<tr VALIGN=TOP>
						<td WIDTH=381><p class='text8'>���������</p></td>
						<td WIDTH=381><p class='text8'>�����</p></td>
					</tr>
					<tr VALIGN=TOP>
						<td><p class='text8'>�������� �� �������������� ��������������� ������ ���� ��������</p></td>
						<td><p class='text8'>������ �� ��� &laquo;�� �� ���������&raquo;</p></td>
					</tr>
					<tr VALIGN=TOP>
						<td>
							<p class='text8'>___________________________/__________________________/</p>
							<p class='text8'>______________________________________________________</p>
							<p class='text8'>/��������� ����������/</p>
							<p><BR></p>
							<p class='text8'>��</p>
						</td>
						<td>
							<p class='text8'>_____________________________________________________</p>
							<p class='text8'>_____________________________________________________</p>
							<p class='text8'>/��������� ���������� ������/</p>
							<p><BR></p>
							<p class='text8'>��</p>
						</td>
					</tr>
				</table>
				<p class='breaker'></p>
			</div>
			<?if($_REQUEST['ORDERS']){// �������� ������?>
				<? // ������ ���������� ������ ����������?>
				<div class='block'><?imldriver::printBKs($arOrders);?></div>
				<? // ������ ���������� ������� �� �������� (�����������, ����� �� ��������� ��� ��������. ���� ���� - ��� ������� ����������� � ��� ������. � ����� �� ��� �������.)
					/*
						���� � ���, ��� ����� ������-�������� ������� ����-������ ������ ������, � ������������� �� ��������� �������. ��������������, ��� � ����-������ ������ ��� �������� ���������� ����������.
						�������� HTML ��������� ����� ����� ��������: imldriver::printBKs($orderIds,$template), ��� $orderIds - id-����� ������� (������ ��� ��������� int), � $template - ���� � ������� ������ ����������, � ������� ����������� ��� �����. �� ��������� ��� - /bitrix/js/iml.v1/bkTemplate.php. 
				?>
				<?
					$templatePath = '/bitrix/admin/reports/bill.php'; // ���� � �����-������� ����������
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$templatePath)){
						// ��������������, ��� ����������� ������� ������� ����� ��������� ���������� ����� ���������� �������������
						$strOfTemplate = '';
						foreach($arOrders['ORDERS'] as $ordMass){
							$ORDER_ID = $ordMass['orderId'];
							$arOrder  = CSaleOrder::GetById($ordMass['orderId']);
							ob_start();
							include($_SERVER['DOCUMENT_ROOT'].$templatePath);
							$strOfTemplate .= ob_get_contents();
							ob_end_clean();
						}
						// ������� ����� �������
						preg_match_all("|<style[^>]*>(.*?)</style>|s", $strOfTemplate, $matches);?>
						<style><?=$matches[0][0]?></style>
						<?
						// ������� ���������� �������
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