<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?>
<div class="registration_page">
	<div class="registration">
	    <h1>Восстановить пароль</h1>
	    <div class="form_block">
	    	<?$APPLICATION->IncludeComponent( "itsfera:system.auth.forgotpasswd",
				".default",
				Array()
			);?> 
	    </div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>