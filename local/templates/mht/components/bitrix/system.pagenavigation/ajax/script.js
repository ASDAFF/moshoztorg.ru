
$(function() {

   BX.addCustomEvent('onAjaxInsertToNode', function(params) {

        var Loader = $('.page-loader');

        var newOffset = $(Loader).data('offset') + 1;

        var url = params.url.replace(/PAGEN_1=\d+/, "PAGEN_1="+newOffset);

        var separatorHtml = '' +
            '<div class="bTileSeparator">\n' +
            '            <span class="eTileSeparator_Text">\n' +
            '                Страница ' + newOffset +' из <span class="eTileSeparator_Total">'+ $(Loader).data('pages') +'</span>\n' +
            '            </span>\n' +
            '        </div>';

        var separator = $(separatorHtml);

        $(Loader).data('offset', newOffset);

        $.ajax({
            type: 'POST',
            url: url,
            async: true,
            dataType: "html",
            success: function (data) {

                //копируем строку навигации в блок с товарами, чтобы не удалился
                $('.js-upload-to-add').append(separator);

                $('.js-upload-to-add').append(data);

                $(".product_cart", document).unbind("click");

                $(document).on("click",".product_cart",
                    function(e){

                        var $this = $(this),
                            href = $this.attr('href');

                        if ( !$this.hasClass('isEvent')) {
                            mht.animateToBasket($this.closest(".product").find(".product_image_original"));
                            $.post(href, function(){
                                mht.updateBasket();
                            });

                            $this.addClass('isEvent');

                            setTimeout(function(){
                                $this.removeClass('isEvent');
                            },1);

                        }

                        return false;
                    }
                );

                //от верстальщика
                setTimeout(function(){
                    if(typeof window.gtresize == 'function') {
                        gtresize ();
                    }
                    addProductHoverButtons();
                    },1);
            }
        });

        if ( (newOffset + 1) > $(Loader).data('pages') ) {
            $(Loader).hide();
        }

        params.eventArgs.cancel = true;

   });

});

