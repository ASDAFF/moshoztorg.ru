<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

use \Bitrix\Sale\Internals\DiscountCouponTable;

?> Делаем нового пользователя <?

GLOBAL $APPLICATION, $USER;

$login = 'new_user';
$email = 'dmitry_plus@mail.ru';

$password = randString(7);


        $userNew = new CUser;

        $arFields = Array(
            "EMAIL" => $email,
            "LOGIN" => $login,
            "LID" => "ru",
            "ACTIVE" => "Y",
            "GROUP_ID" => array(6),
            "PASSWORD" => $password,
            "CONFIRM_PASSWORD" => $password,
            "ADMIN_NOTES" => 'Регистрация за купон'
        );
        $ID = $userNew->Add($arFields);
        if (intval($ID) > 0) {
            $RESULT['erroe_code'] = 0;


            //if(Main\Loader::includeModule('sale')){

            $coupon = DiscountCouponTable::generateCoupon(true);

            dm( $coupon );

			$addDb = DiscountCouponTable::add(array(
				'DISCOUNT_ID' => 4,
                'ACTIVE' => 'Y',
				'COUPON' => $coupon,
				'TYPE' => DiscountCouponTable::TYPE_ONE_ORDER,
				'MAX_USE' => 1,
				'USER_ID' => $ID,
				'DESCRIPTION' => 'Скидка за регистрацию',
			));

		//	if($addDb->isSuccess())
		//	{
                dm( $addDb->isSuccess() );

                dm( $addDb->getErrorMessages() );
		//	}

        //    dm( $addDb->LAST_ERROR );

        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $userNew->LAST_ERROR;
        }


dm( $RESULT );


?>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>
