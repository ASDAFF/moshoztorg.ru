<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"] !== "N"):?>
<div id="<?= $CONTAINER_ID?>" class="title-search-container">
	<form action="<?echo $arResult["FORM_ACTION"]?>" data-ajax-action="/search/request.php">
		<input id="<?echo $INPUT_ID?>" type="text" name="q" value="" size="40" maxlength="50" autocomplete="off" class="search_field hayhopped" />
		<input name="s" type="submit" class="search_submit" value="" />
        <input type="hidden" id="sessid" value="<?=bitrix_sessid()?>">
	</form>
</div>
<div data-retailrocket-markup-block="5888700565bf19377063c1da"></div>
<?endif?>



<script>
    $(function() {

        var arFilterParams = {};

        var $form = $('#title-search').find('form');
        var sSearchAddress = $form.data('ajax-action');
        var $input = $form.find("input[name=q]");


        $input.keyup(function(e){
            if(e.keyCode == 13){
                $form[0].submit();
            }
        });

        var $submit = $form.find("input.search_submit");
        $submit.click(function(e){
                $form[0].submit();
        });


        $input.autocomplete({

            open: function(){
                $(this).autocomplete('widget').css('z-index', 5001);
                return false;
            },

            source: function (request, response) {
                $.ajax({
                    url: sSearchAddress,
                    dataType: "json",
                    data: {
                        term: request.term,
                        form: 'main',
                        sessid: $("#sessid").val(),
                        debug: 'Y'

                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },

            select: function (event, ui) {
                var prefix = $input.val();
                var selection = ui.item.label;

                $input.val(selection);
                $form[0].submit();
            }
        }).keyup(function (e) {

            arFilterParams = {};

            if (e.which === 13) {
                $(".ui-autocomplete").hide();
            }
        });
    });
</script>

<?/*
<script>
	BX.ready(function(){
		new JCTitleSearchMHT({
			'AJAX_PAGE' : '/ajax.php',
			'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
			'INPUT_ID': '<?echo $INPUT_ID?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script>*/?>
