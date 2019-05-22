$(function(){

    var arFilterParams = {};

    var $form = $('#search_form');
    var $result = $('#search_result');
    var $filters = $('#search_filters');

    var sSearchAddress = $form.attr('action');

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
                    sessid : $("#sessid").val()
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
            $form.trigger("submit");
        }
    }).keyup(function (e) {

        arFilterParams = {};

        if(e.which === 13) {
            $(".ui-autocomplete").hide();
        }
    });



    $form.on('submit',function(){

        doSearch();

        return false;

    });

    $form.find('input[name=reset]').on('click',function(){

        arFilterParams = {};
        $inputOffset.val( 0 );
        doSearch( true );

    });



    function doSearch( bReset , iOffset )
    {

        var requestParams = $form.serialize();

        if ( !bReset)
        {
            requestParams += '&'+jQuery.param( arFilterParams );
        }


        $result.html('Поиск...');


        $.ajax({
            type: 'POST',
            url: sSearchAddress,
            data: requestParams,
            async:false,
            dataType:"json",
            success: function(data){

                showCatalogList( JSON.stringify(data), $inputOffset.val()  );

            }
        });
    }


    if ( sDefaultValue.length>0 ){
        $form.trigger('submit');
    }


    function showCatalogList( data , offset) //json , sessid
    {

         $.ajax({
             type: 'POST',
             url: "catalog.php",
             data: { data: data, offset:offset}, //json:json, sessid: sessid
             async:false,
             dataType:"html",
             success: function(data){

                 $('html, body').animate({
                     scrollTop: $form.offset().top
                 }, 500);

                 $result.html( data );

                 setTimeout(function(){
                     initFilter();
                 },1);

                 return data;
             }
         });
         return '';

    }


    function initFilter()
    {

        $('.filters').find('input[type=checkbox]').off('change').on('change',function(){

            checkCheckedFilters();
            $inputOffset.val( 0 );

            doSearch();


            return false;
        });


        $('#search_pagination').find('a').on('click',function(){

            var iOffset = $(this).data('offset');
            $inputOffset.val( iOffset );

            arFilterParams = {};

            doSearch();
            return false;

        });


    }

    function checkCheckedFilters()
    {
        arFilterParams = {'set_filter':'Y'};
        $('.filters').find('input[type=checkbox]').each(function(){

            if ($(this).is(':checked')) {

                //console.log( typeof arFilterParams[ $(this).attr('name') ]  );
                if (typeof arFilterParams[ $(this).attr('name') ] == 'string') {

                    var valueBefore = arFilterParams[$(this).attr('name')];
                    arFilterParams[$(this).attr('name')] = [];
                    arFilterParams[$(this).attr('name')].push(valueBefore);
                    arFilterParams[$(this).attr('name')].push($(this).val());

                }else if ( typeof arFilterParams[ $(this).attr('name') ] == 'object' ){

                    arFilterParams[$(this).attr('name')].push($(this).val());

                }else {
                    arFilterParams[ $(this).attr('name') ] =  [] ;
                    arFilterParams[ $(this).attr('name') ].push($(this).val());

                }

            }
        });
        //console.log( arFilterParams );
    }



});