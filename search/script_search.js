$(function(){


    var isLoadNewPage = false;

    //флаг автоматической отправки формы при перезагрузке стараницы
    var autoloadPage = false;

    function showLoader()
    {
        $('#search_result').html('<div id="search-loader"><img src="ajax-loader.gif"> loading... </div>');
    }



    var arFilterParams = {};

    if ( isCookie = getCookie('search_request') ) {
        var lastFilterParams = JSON.parse( isCookie );
        arFilterParams = lastFilterParams;
    }

    //очищаем фильтр от ненужных полей
    delete arFilterParams.sessid;
    delete arFilterParams.q;
    delete arFilterParams.offset;
    delete arFilterParams.order;
    delete arFilterParams.discount;


    var $form = $('#search_form');

    if ($form.length<=0)
        $form = $('#title-search').find('form');


    var $result = $('#search_result');
    var $filters = $('#search_filters');

    var sSearchAddress = $form.data('ajax-action');

    var $input = $form.find("input[name=q]");

    var sDefaultValue = $input.val();

    var $inputOffset = $form.find('input[name=offset]');


    $input.autocomplete({

        source: function(request, response) {
            $.ajax({
                url: sSearchAddress,
                dataType: "json",
                data: {
                    term : request.term,
                    sessid : $("#sessid").val(),
                    debug:'Y'

                },
                success: function(data) {
                    response(data);
                }
            });
        },

        select: function(event, ui) {
            var prefix = $input.val();
            var selection = ui.item.label;
            $input.val(selection);

            autoloadPage = false;

            $form.trigger("submit");
        }
    }).keyup(function (e) {

        arFilterParams = {};

        if(e.which === 13) {
            $(".ui-autocomplete").hide();
        }
    });



    $form.on('submit',function(){

        showLoader();
        doSearch();

        return false;

    });




    function doSearch( bReset , iOffset )
    {

        var requestParams = $form.serialize();

        if ( !bReset)
        {
            requestParams += '&'+jQuery.param( arFilterParams );
        }


        if ( $inputOffset.val() == '0' ) {
            showLoader();
        }


        if (autoloadPage) {
            requestParams += '&autoload'
        }

        $.ajax({
            type: 'POST',
            url: sSearchAddress,
            data: requestParams,
            async:false,
            dataType:"json",
            success: function(data){

                if (typeof data.type !== undefined && data.type=="redirect"){
                    document.location.href = data.url;
                } else {
                    showCatalogList( JSON.stringify(data), $inputOffset.val(), requestParams  );
                }
            }
        });

        //возвращаем  флаг в дефолт
        autoloadPage = false;

    }


    if ( typeof sDefaultValue !== 'undefined' ){

        if (sDefaultValue.length>0 ) {
            showLoader();

            //ставим флаг автомата
            autoloadPage = true;

            $form.trigger('submit');
        }
    }


    function showCatalogList(data, offset, params) //json , sessid
    {

        var Loader = $('.page-loader');
        var newOffset = $(Loader).data('offset');

        if ((typeof newOffset === 'undefined') || (newOffset <= $(Loader).data('pages'))) {

            var search = $('#search_form').find("input[name=q]").val();

            $.ajax({
                type: 'POST',
                url: "catalog.php",
                data: {data: data, offset: offset, params: params}, //json:json, sessid: sessid
                async: false,
                dataType: "html",
                success: function (data) {

                    if (offset == '0') {

                        if (data != 'empty') {
                            $('html, body').animate({
                                scrollTop: $form.offset().top
                            }, 500);

                            $result.html(data);

                            $(".product_cart", document).unbind("click");

                            $(document).on("click", ".product_cart",
                                function (e) {

                                    var $this = $(this),
                                        href = $this.attr('href');

                                    if (!$this.hasClass('isEvent')) {
                                        mht.animateToBasket($this.closest(".product").find(".product_image_original"));
                                        $.post(href, function () {
                                            mht.updateBasket();
                                        });

                                        $this.addClass('isEvent');

                                        setTimeout(function () {
                                            $this.removeClass('isEvent');
                                        }, 1);

                                    }

                                    return false;
                                }
                            );

                            setTimeout(function () {
                                initFilter();
                            }, 1);
                        } else {

                         $('#search_result').html('<div id="empty-search">К сожалению, по вашему запросу <strong>"' + search + '"</strong> ничего не найдено.</div><div id="akcii_preview"></div><br>');

                         $.ajax({
                            url: 'akcii.php',
                            success: function(data){
                                    $('#akcii_preview').append(data);


                                    $(".owl-carousel").owlCarousel({
                                        loop:true,
                                        items:4,
                                        autoplay:false,
                                        autoplayTimeout:7000,
                                        autoplayHoverPause:true,
                                        autoplaySpeed:1200,
                                        pagination: false,
                                        dots: false,
                                        navText: true,
                                        responsive: {
                                            0:{
                                                nav:true
                                            },
                                            480: {
                                              nav: true
                                            }
                                        }
                                    });

                                }
                            });

                        $('#search_result').append('<div data-retailrocket-markup-block="58886fc35a658842d81a0401" data-search-phrase="' + search + '" style="margin-top: 300px;"></div>');

                            retailrocket.markup.render();

                            console.log('К сожалению, по вашему запросу <strong>"' + search + '"</strong> ничего не найдено');

                        }
                    } else {


                        var separatorHtml = '' +
                            '<div class="bTileSeparator">\n' +
                            '            <span class="eTileSeparator_Text">\n' +
                            '                Страница ' + newOffset + ' из <span class="eTileSeparator_Total">' + $(Loader).data('pages') + '</span>\n' +
                            '            </span>\n' +
                            '        </div>';

                        var separator = $(separatorHtml);

                        $('.js-upload').append(separator);

                        $('.js-upload').append(data);

                        //от верстальщика
                        setTimeout(function () {
                            gtresize();
							addProductHoverButtons();
                        }, 1);

                    }
                }
            });

        }

        return '';

    }


    function initFilter()
    {


        if ( arFilterParams.set_filter =="Y"){
            $('#reset-filter-button').addClass('active');
        }else {
			$('#reset-filter-button').removeClass('active');
        }

        //от верстальщика
        setTimeout(function(){

            gtresize ();
            /*
            $('.products_block.js-fit .gtwrap').each(function(){
                prodtitle = 0;
                $(this).find('.product_brand').each(function(){
                    if ($(this).height() > prodtitle){
                        prodtitle = $(this).height();
                    };
                });
                $(this).find('.product_brand').each(function(){
                    $(this).css('height', prodtitle);
                });
                proddesc = 0;
                $(this).find('.product_description').each(function(){
                    if ($(this).height() > proddesc){
                        proddesc = $(this).height();
                    };
                });
                $(this).find('.product_description').each(function(){
                    $(this).css('height', proddesc);
                });
            });*/


        },1);




        $('li.showhidden a').bind('click', function(){
            $(this).parent('li').hide();
            $(this).parent('li').siblings('li.hidehidden').show();
            $(this).parent('li').siblings('div.hidden_list').slideDown();
            return false;
        });

        $('li.hidehidden a').bind('click', function(){
            $(this).parent('li').hide();
            $(this).parent('li').siblings('li.showhidden').show();
            $(this).parent('li').siblings('div.hidden_list').slideUp();
            return false;
        });


        setTimeout(function(){

            $(".page-loader").bind('click', function () {

                iOffset = $(this).data('offset') * $(this).data('itemsonpage');

                curPage = $(this).data('offset') + 1;
                $(this).data('offset', curPage);

                $inputOffset.val( iOffset );
                doSearch(false);

                if ( (iOffset + $(this).data('itemsonpage')) > $(this).data('items') ) {
                    $(this).hide();
                }

                return false;

            });

        },300);





        $('.filters').find('input[type=checkbox]').off('change').on('change',function(){

            checkCheckedFilters();
            $inputOffset.val( 0 );

            doSearch();


            return false;
        });





        $('#reset-filter-button').off('click').on('click',function(){

            //console.log('click reset!');

            //убираем скидку
            $('#change_discount_search').removeAttr('checked');
            $("input[name=discount]").val( 0 );


            arFilterParams = {};
            $inputOffset.val( 0 );
            doSearch( true );


            return false;

        });

        renderCurrentFilter();


    }

    function renderCurrentFilter()
    {
        //console.log('render Current Filter');
        var $current_filter_wrapper = $('#current_filter');

        $current_filter_wrapper.html('');

        $('.categories').find('label').removeClass('active');
        $('.filteritems').find('label').removeClass('active');



        $.each(arFilterParams,function(index,val){

            //console.log(index+'---'+val);

            if (index=='set_filter' ) return;


            if (index == 'category') {

                for (var i=0;i<val.length;i++) {

                    var catClass = '.category' + val[i];
                    $(catClass).find('label').addClass('active');
                }


            }else {


                for (var i=0;i<val.length;i++) {


                    //var catClass = '.category' + val[i];
                    //$(catClass).find('label').addClass('active');
                    var $inp = $('input[value="'+val[i]+'"][name="'+index+'"]');
                    $inp.closest('label').addClass('active');
                    if ($inp.closest('.hidden_list').length>0){
                        $inp.closest('ul').find('.showhidden').find('a').trigger('click');
                    }






                    var $p = $('<p>').text(val[i] + ' ').appendTo('#current_filter');
                    $('<a>').text('x').attr('href', '#').data('filter', index).data('value', val[i]).on('click', function () {


                        var dataFilter = $(this).data('filter');
                        var dataValue = $(this).data('value');


                        var newArFilterParamas = {};
                        $.each(arFilterParams, function (index, val) {

                            if (index == dataFilter ) { //&& $(this).data('value')==val
                                var removeIndex = val.indexOf(dataValue);
                                val.splice(removeIndex, 1);
                            }

                            newArFilterParamas[index] = val;


                        });
                        arFilterParams = newArFilterParamas;

                        doSearch(false);
                        return false;
                    }).appendTo($p);

                    $current_filter_wrapper.append($p)
                }

            }



        });
    }

    function checkCheckedFilters()
    {

        arFilterParams.set_filter = 'Y';

        $('.filters').find('input[type=checkbox]').each(function(){

            if ($(this).is(':checked')) {


                //console.log( typeof arFilterParams[ $(this).attr('name') ]  );

                //если повторное нажатие
                var filterName = $(this).attr('name');
                if ( typeof arFilterParams[ filterName ]!='undefined'  && arFilterParams[ filterName ].indexOf($(this).val())>=0){


                    var removeIndex = arFilterParams[ filterName ].indexOf($(this).val());
                    arFilterParams[ filterName ].splice(removeIndex, 1);
                    return false;
                }



                if (typeof arFilterParams[ filterName ] == 'string') {

                    var valueBefore = arFilterParams[$(this).attr('name')];
                    arFilterParams[ filterName ] = [];
                    arFilterParams[ filterName ].push(valueBefore);
                    arFilterParams[ filterName ].push($(this).val());

                }else if ( typeof arFilterParams[ filterName ] == 'object' ){

                    arFilterParams[ filterName ].push($(this).val());

                }else {
                    arFilterParams[ filterName ] =  [] ;
                    arFilterParams[ filterName ].push($(this).val());

                }

            }
        });
        //console.log( arFilterParams );
    }


   $(window).scroll(function () {

       var $loader = $("a.page-loader");

       if ($loader.offset()) {
           var PosScroll = $(window).height() + $(window).scrollTop();
           var PosAnchor = $loader.offset().top - 400;

           if ( ( PosScroll > PosAnchor ) && (isLoadNewPage == false) ) {
               isLoadNewPage = true;
               $loader.trigger('click');
           }

           if ( ( PosScroll < PosAnchor )  && (isLoadNewPage == true) ){
               isLoadNewPage = false;
           }
       }

   });


});
