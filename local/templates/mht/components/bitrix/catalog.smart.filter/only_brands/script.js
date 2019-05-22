
$(function() {

	window.makeFilter = function(){

        var href = '?';

        $("a.filter-page", $("li.active")).each(function(index, value) {
			href += $(this).data('filter')+"=Y&";
        });

        $("input",$(".filter-numbers-fields")).each(function(index, value) {
            href += $(this).attr('name')+"="+$(this).val()+"&";
        });

        $("input",$(".filter-prices")).each(function(index, value) {
			if ( ($(this).attr('name') != 'set_filter' ) && ($(this).attr('name') != 'del_filter'))
			{
                href += $(this).attr('name')+"="+$(this).val()+"&";
			}
        });

        href += "set_filter=Подобрать";

        window.location.href = window.location.pathname + href;
	}

    $("a.filter-page").on("click",function(e){

        e.preventDefault();

        $(this).parent().addClass("active");

		window.makeFilter();

        return false;

    });


    $('.filter-numbers-fields').each(function(){
        var $holder = $(this),
            $range = $holder.find('.cost_range'),
            $min = $holder.find('.cost-min'),
            $max = $holder.find('.cost-max'),
            disable = function(v){
                v = v ? 'disabled' : false
                $min.attr('disabled', v);
                $max.attr('disabled', v);
            };

        disable(true);
        $holder.click(function(){
            disable(false);
        });

        $range.slider({
            change: function( event, ui ) {

                setTimeout( window.makeFilter() ,2000 );

            },
            range: true,
            min: parseInt($min.attr('data-sel-value')),
            max: parseInt($max.attr('data-sel-value')),
            values: [
                parseInt($min.val()),
                parseInt($max.val()),
            ],
            slide: function( event, ui ) {
                $min.val(ui.values[0]).attr('value',ui.values[0]);
                $max.val(ui.values[1]).attr('value',ui.values[1]);
                //console.log(ui.values[0]);
                //console.log( ui.values[1] );
            }
        });
    });


});




