<?if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
    {    
        require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
              
   if($_POST['captcha'] == 'yes')
   {
      echo $code = $APPLICATION->CaptchaGetCode();
   }
   else
      die();                                       
} 
else 
   die();

?>
