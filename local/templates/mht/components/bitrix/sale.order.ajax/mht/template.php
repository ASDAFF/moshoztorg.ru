<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			$APPLICATION->RestartBuffer();
			?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}

	}
}

$APPLICATION->SetAdditionalCSS($templateFolder."/style_cart.css");
$APPLICATION->SetAdditionalCSS($templateFolder."/style.css");

CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
?>
<div class="ajax_order_page">
<div class="wrapper">
<a name="order_form"></a>
<div id="order_form_div" class="order-checkout">


<NOSCRIPT>
	<div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>

<?
if (!function_exists("getColumnName"))
{
	function getColumnName($arHeader)
	{
		return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
	}
}

if (!function_exists("cmpBySort"))
{
	function cmpBySort($array1, $array2)
	{
		if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
			return -1;

		if ($array1["SORT"] > $array2["SORT"])
			return 1;

		if ($array1["SORT"] < $array2["SORT"])
			return -1;

		if ($array1["SORT"] == $array2["SORT"])
			return 0;
	}
}
?>

<div class="bx_order_make">
	<?
	if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
	{
		if(!empty($arResult["ERROR"]))
		{
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);
		}
		elseif(!empty($arResult["OK_MESSAGE"]))
		{
			foreach($arResult["OK_MESSAGE"] as $v)
				echo ShowNote($v);
		}

		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
	}
	else
	{
		if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
		{
			if(strlen($arResult["REDIRECT_URL"]) == 0)
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
			}
		}
		else
		{
			?>
			<script type="text/javascript">
			function setLoadingOrderCheckout(v){
				$('.order-checkout').css({
					opacity : v ? 0.3 : 1
				});

				if(!v){ //в оформлении заказа при сохраненном профиле и при заполнении нового профиля

                    var PersonTypeID = ["3", "14", "15"];
                    PersonTypeID.forEach(function(entry) {

                        var value = $('#ORDER_PROP_'+entry).val();

                        if ( typeof value !== "undefined" ) {
                            $('#ORDER_PROP_'+entry).inputmask('+9 (999) 999-99-99');
                        }
                    });

				}
			}

			$(function(){
				setLoadingOrderCheckout(false);
			});

			var submitFormActive = false;

			function submitForm(val)
			{
				if(submitFormActive){
					return;
				}
				submitFormActive = true;
				setTimeout(function(){
					submitFormActive = false;
				}, 1000);
				if(val != 'Y')
					BX('confirmorder').value = 'N';

				var orderForm = BX('ORDER_FORM');
				BX.showWait();
				setLoadingOrderCheckout(true);

				BX.ajax.submit(orderForm, ajaxResult);

				return true;
			}

			function ajaxResult(res)
			{
				try
				{

					var json = JSON.parse(res);
					BX.closeWait();

					if (json.error)
					{
						return;
					}
					else if (json.redirect)
					{
						window.top.location.href = json.redirect;
					}
				}
				catch (e)
				{
					$('#order_form_content').html(res);
					if(localStorage.getItem("userexist") && localStorage.getItem("userexist") == "true"){
						$("#auth_holder input[name='login']").val(localStorage.getItem("useremail"));
						mht.modal({
							element : $("#auth_holder").detach(),
							title : "Войти на сайт",
							id: "#auth_holder",
							note: "Пользователь с указанным email уже существует.<br/>Авторизуйтесь или укажите другой email."
						});
						localStorage.setItem("userexist",false);
					}
				}
				setLoadingOrderCheckout(false);
				BX.closeWait();
			}

			function SetContact(profileId)
			{
				BX("profile_change").value = "Y";
				submitForm();
			}
			</script>
			<?if($_POST["is_ajax_post"] != "Y")
			{
				?><form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
				<?=bitrix_sessid_post()?>
				<div id="order_form_content">
				<?
			}
			else
			{
				$APPLICATION->RestartBuffer();
			}
			if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
			{
				foreach($arResult["ERROR"] as $v)
					if(preg_match("/Пользователь с таким e-mail \(([^\)]*)\) уже существует./iu",$v,$m)){
					?>
					<script type="text/javascript">
						localStorage.setItem("userexist",true);
						localStorage.setItem("useremail","<?=$m[1]?>");
					</script>
					<?					
					}else{
						echo ShowError($v);
					}
				?>
				<script type="text/javascript">
					top.BX.scrollToNode(top.BX('ORDER_FORM'));
				</script>
				<?
			}


			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");

			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
			if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
			}
			else
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
			}

			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/comment.php");

			if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
				echo $arResult["PREPAY_ADIT_FIELDS"];
			?>
			<div style="color:#ea0505; margin-top:50px; padding-top:50px;"></div>	
			<?if($_POST["is_ajax_post"] != "Y")
			{
				?>
					</div>
					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
					<input type="hidden" name="json" value="Y">
					<div class="bx_ordercart_order_pay_center">
						<?/*<div class="price-result">
							<div class="delivery-info">
								Итого с доставкой <?=$arResult["DELIVERY_PRICE_FORMATED"]?>
							</div>
							<div class="price-total">
								<?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?>
							</div>
						</div>*/?>
                        <?$APPLICATION->IncludeComponent(
                            "itsfera:agreement",
                            "2018_order",
                            Array()
                        );?>
						<a href="javascript:void();" onclick="if( !$(this).hasClass('disabled') ) { submitForm('Y'); (window['rrApiOnReady'] = window['rrApiOnReady'] || []).push(function() { rrApi.setEmail($('#ORDER_PROP_2').val()); }); } return false;" class="checkout"><?=GetMessage("SOA_TEMPL_BUTTON")?></a>
						
						
						
						
					    </div>
                        <?/*
					    <div class="agreement-link">
                            Нажимая кнопку «<?=GetMessage("SOA_TEMPL_BUTTON")?>» — вы соглашаетесь
                            <a href="#agreement-content" data-action="pupop"> с Условиями продажи товаров</a>
                        </div>
                        <div id="agreement-content">
                            <?
                            $APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR.'/include/agreement.php'
                            ));
                            ?>
                        </div>
                        */?>
				</form>
				<?
				if($arParams["DELIVERY_NO_AJAX"] == "N")
				{
					?>
					<div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
					<?
				}
			}
			else
			{
				?>
				<script type="text/javascript">
					top.BX('confirmorder').value = 'Y';
					top.BX('profile_change').value = 'N';
				</script>
				<?
				die();
			}
		}
	}
	?>


    <?$APPLICATION->IncludeComponent(
        "itsfera:action_items",
        "",
        array(),
        false
    );?>


	</div>
</div>
</div>
</div>