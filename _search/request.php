<?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

define('SEARCH_URL','http://moshoztorg.demo.detectum.com:9423/search_plain');
define('SEARCH_WITH_FILTER','http://moshoztorg.demo.detectum.com:9423/search');
define('SEARCH_SUGGEST_URL','http://suggest.moshoztorg.demo.detectum.com/prefix');

define('SEARCH_ITEMS_COUNT',15);

error_reporting(E_ALL);
ini_set('display_errors', 1);



/*

    q      запрос<br>
    offset номер товара, до которого товары в выдаче надо пропустить <br>
    limit  количество товаров в выдаче, 0 - все товары<br>
    order  price_asc, price_desc, id_asc, id_desc - по цене и id, вверх и вниз<br>

*/


if ( check_bitrix_sessid() ) {

    if ( isset($_POST['q']) ) {




        $arGetArgs = [
            'q' => $_POST['q'],
            'limit' => SEARCH_ITEMS_COUNT
        ];


        if (intval($_POST['offset'])>0){
            $arGetArgs['offset'] = intval($_POST['offset']);
        }

        if ( isset( $_POST['set_filter'] ) ){

            $arFilterParams = [];

            $iParamsCount = 0;
            foreach ($_POST as $name=>$value){

                if ( !in_array($name,['q','sessid','set_filter','category','offset'])){
                    $arFilterParams[ $name ] = $value;
                    $iParamsCount += count( $arFilterParams[ $name ] );
                }
            }

            $arGetArgs['params'] = $iParamsCount;
            $iParamsCounter = 0;
            foreach ($arFilterParams as $sParamName=>$arParamValue){


                if (is_array($arParamValue)){

                    foreach ($arParamValue as $sValue){
                        $iParamsCounter++;
                        $arGetArgs['param_'.$iParamsCounter] = $sParamName;
                        $arGetArgs['value_'.$iParamsCounter] =  $sValue;


                    }

                }else {
                    $iParamsCounter++;
                    $arGetArgs['param_'.$iParamsCounter] = $sParamName;
                    $arGetArgs['value_'.$iParamsCounter] =  $arParamValue;

                }

            }

            if ( isset($_POST['category'][0]) ){
                $arGetArgs['category_id'] = implode(',',$_POST['category']);
            }

            //params=2&param_1=vendor&value_1=LG&param_2=vendor&value_2=Samsung
            /*echo '<pre>';
            print_r($arFilterParams);
            echo '</pre>';*/

        }
        //echo http_build_query($arGetArgs);



        $resJson = file_get_contents( SEARCH_URL . '?' . http_build_query($arGetArgs));



        if ($res =  \Bitrix\Main\Web\Json::decode($resJson) ){

            if( !isset($res['results']['items']) || empty($res['results']['items']) ){

                echo SEARCH_URL . '?' . http_build_query($arGetArgs);
                echo '<pre>';
                print_r($res);
                echo '</pre>';
            }

        }


        echo $resJson;



    }elseif( isset($_GET['term'])  ) {

        $arGetArgs = [
            'term' => $_GET['term'],
            //'limit' => SEARCH_ITEMS_COUNT
        ];
        echo file_get_contents(SEARCH_SUGGEST_URL . '?' . http_build_query($arGetArgs));

        //echo SEARCH_SUGGEST_URL . '?' . http_build_query($arGetArgs);

    }


}else {
    echo 'session error';
}