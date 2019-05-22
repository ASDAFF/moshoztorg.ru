<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?><div class="registration_page">
	<div class="registration">
		<h1>регистрация</h1>
		<div class="form_block">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.register",
				"2018",
				Array(
					"AUTH" => "Y",
					"REQUIRED_FIELDS" => array("EMAIL"),
					"SET_TITLE" => "Y",
					"SHOW_FIELDS" => array("LOGIN","EMAIL","NAME","LAST_NAME"),
					"SUCCESS_PAGE" => "/",
					"USER_PROPERTY" => array(),
					"USER_PROPERTY_NAME" => "",
					"USE_BACKURL" => "N"
				)
			);?>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>