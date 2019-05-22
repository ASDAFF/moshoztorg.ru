<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

//путь для подключения файлов include-ом
$CurPath = $_SERVER['DOCUMENT_ROOT'] . '/' . SITE_TEMPLATE_PATH;

?>
<div class="container">

    <div class="catalog-element_page">
        <div class="similar_products">
            <h2>Смартфоны и гаджеты</h2>
        </div>
        <div class="catalog_page">
            <div class="catalog_block">
                <div class="catalog wide">
                    <div class="products_block slick_block js-fit prod-slider">
                        <?
                        $i = 0;
                        foreach ($arResult['ITEMS'] as $key => $element) {

                            $product = MHT\Product::byId($element['ID']);
                            echo $product->moreFields($element)->html('catalog', array(
                                'tpl' => $this,
                                'i'   => $i++,
                            ));
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

