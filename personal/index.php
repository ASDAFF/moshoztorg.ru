<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
if(!$USER->GetID()){
	LocalRedirect('/personal/auth/');
	return;
}
?><div class="personal">
	<div class="bx_page">
		<h1>Личный кабинет</h1>
		 <?$APPLICATION->IncludeComponent(
	"itsfera:discounts.cards",
	".default",
Array()
);?>
		<div>
			<h2>Личная информация</h2>
			 <?/*<a href="/personal/profile/">Изменить регистрационные данные</a>*/?> <a href="/personal/profile/">Личные данные</a><br>
 <a href="/personal/favorite/">Избранные товары</a><br>
		</div>
		<div>
			<h2>Заказы</h2>
 <a href="/personal/order/">Ознакомиться с состоянием заказов</a><br>
 <a href="/catalog/basket/">Посмотреть содержимое корзины</a><br>
 <a href="/personal/orders-completed/">Выполненные заказы</a><br>
		</div>
		<div>
			<h2>Подписка</h2>
 <a href="/personal/subscribe/">Изменить подписку</a>
		</div>
		<div>
			<h2>Инструкции</h2>

 <a href="/informatsiya/instruktsii/">Очистка кеша браузера и сохранение файлов cookie</a>
		</div>


        <?$APPLICATION->IncludeComponent(
            "itsfera:gift.certificate",
            "",
            array(
            ),
            false
        );?>

	</div>
 <br>
	 <?
		/********************
		**black friday 2016**
		*******************
		
		\Bitrix\Main\Page\Asset::getInstance()->addJs("/banners/countdown.js");
		\Bitrix\Main\Page\Asset::getInstance()->addCss("/banners/countdown.css");
		
		$seconds = strtotime("2016-11-24 21:00:00") - time();
		$days = str_pad(floor($seconds / 86400), 2, '0', STR_PAD_LEFT);
		$seconds %= 86400;
		$hours = str_pad(floor($seconds / 3600), 2, '0', STR_PAD_LEFT);
		$seconds %= 3600;
		$minutes = str_pad(floor($seconds / 60), 2, '0', STR_PAD_LEFT);
		$seconds %= 60;			
		$seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
		//echo $days.':'.$hours.':'.$minutes.':'.$seconds;
		?>
		
		<script>
		  $(document).ready(function(){
			$(".digits").countdown({
			  image: "/banners/digits.png",
			  format: "dd:hh:mm:ss",
			  startTime: "<?=$days.':'.$hours.':'.$minutes.':'.$seconds?>"
			});
		  });
		</script>
		
		<a href="/o_kompanii/novosti/664791/">
		<div class="banner_friday">
			<div class="wrapper_friday">
			  <div class="cell_friday">
				<div id="holder_friday">
				  <div class="digits"></div>
				</div>
			  </div>
			</div>
		</div>		
		</a>
		
		<?
		*******************
		**black friday 2016**
		********************/
		?> <br>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>