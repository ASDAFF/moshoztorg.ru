<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arFields = CASDiblockTools::GetIBUF( $arParams['IBLOCK_ID'] );

$arResult['SEO_TEXT'] = '';
$arResult['CUR_SECTION']  = array();
if ( isset($arResult['VARIABLES']['SECTION_ID']) && $arResult['VARIABLES']['SECTION_ID']>0){

    $arFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
    );
    if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
    {
        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
    }
    elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
    {
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
    }

    $arFilter['renew'] = 7;  //для обновления кэша

    $obCache = new CPHPCache();
    if ($obCache->InitCache(0, serialize($arFilter), "/iblock/catalog")) // 36000
    {
        $arCurSection = $obCache->GetVars();
    }
    elseif ($obCache->StartDataCache())
    {
        $arCurSection = array();
        if (\Bitrix\Main\Loader::includeModule("iblock"))
        {
            $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID","IBLOCK_SECTION_ID","DEPTH_LEVEL","NAME","DESCRIPTION"));

            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                if ($arCurSection = $dbRes->Fetch())
                {

                    //определяем наличие у раздела подразделов
                    $arFilter = Array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'SECTION_ID'=>$arCurSection['ID'],'ACTIVE'=>'Y');
                    $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array("ID","IBLOCK_SECTION_ID"));
                    $arCurSection['HAS_SUBSECTIONS'] = $db_list->GetNext();
                    




                    $CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
                }
                $CACHE_MANAGER->EndTagCache();
            }
            else
            {
                if(!$arCurSection = $dbRes->Fetch())
                    $arCurSection = array();
            }
        }
        $obCache->EndDataCache($arCurSection);
    }
    if (!isset($arCurSection))
    {
        $arCurSection = array();
    }
    $arResult['CUR_SECTION'] = $arCurSection;


    if (!empty($arCurSection['DESCRIPTION'])){
        $arResult['SEO_TEXT'] = $arCurSection['DESCRIPTION'];
    }


}else {
    $arResult['SEO_TEXT'] = isset($arFields['UF_SEO_TEXT'])?$arFields['UF_SEO_TEXT']:'';


    $arRes = CIblock::GetByID($arParams['IBLOCK_ID']);
    $arResult['IBLOCK'] = $arRes->Fetch();

}

