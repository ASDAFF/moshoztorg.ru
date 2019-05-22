var shops_map;

$(function(){
    ymaps.ready(ynadex_map_init);

    var hash = window.location.hash;
    if (hash.indexOf('#shop')!==false){
        var shopId = hash.substr(5);
        if (shopId.length>0) { 
            $('.shopviewmore[data-shop-id=' + shopId + ']').trigger('click');

            $('.shop-item').each(function (index) {
                if ($(this).data('shop-id') == shopId) {
                    $('.shopslist .itemscarousel')[0].slick.slickGoTo(parseInt(index) - 1);
                }
            });
        }
    }

    $('.shopslist .itemscarousel').on('afterChange',function(event,slick, currentSlide){
        //console.log(slick);
        //console.log(currentSlide);

        var $shop = $('.shopitem').eq(currentSlide).find('.shopviewmore');
        window.location.hash = '#shop'+parseInt( $shop.data('shop-id') );

    });



    $('.shopviewmore').on('click',function(){
        $('.shopslist .itemscarousel')[0].slick.slickGoTo( parseInt( $(this).data('shop-number') ) );
        window.location.hash = '#shop'+parseInt( $(this).data('shop-id') );
        $('.accord_shops').find('.accorditemheading').trigger('click');
        return false;
    });

    $('#region-select').change(function () {
        var $selectedOption = $(this).find('option:selected');
        var regionCode = $selectedOption.attr('value');
        if (regionCode.length>0){
            $('.shopitem').hide();
            $('.region_'+regionCode).show();
        }
    });

    //

});


function setPanoram(latLng){
    var panoramaOptions = {
        position: latLng,
        addressControl:false,
        enableCloseButton:false,
        imageDateControl:false,
        linksControl:true,
        panControl:false,
        scrollwheel:false,
        zoomControl:false
    };
    panorama = new  google.maps.StreetViewPanorama(document.getElementById('panoram'),panoramaOptions);
}

function findPanoram(cord,radius){
    service = new google.maps.StreetViewService();
    service.getPanoramaByLocation(new google.maps.LatLng(cord[0],cord[1]), radius, function(result, status) {
        if (status == google.maps.StreetViewStatus.OK) {
            setPanoram(result.location.latLng);
        }else{
            findPanoram(cord,radius+10);
        }
    });
}

function add_placemark(params){

    html = 	[];
    $.each(params.coords, function(i, v){
        params.coords[i] = parseFloat(v);
    })
    html.push('<div class="balloonContent" data-coord="'+params.coords.join(",")+'">');
    html.push('<div class="contact_block">');
    html.push('<div class="dealer_street">'+params.street+'</div>');
    html.push('<div class="dealer_build">' + params.house_html + '</div>');
    html.push('<div class="dealer_phones">' + $.map(params.phones || [], function(phone){
            return '<span class="dealer_phone">' + phone + '</span>';
        }).join('') + '</div>');
    html.push('<div class="dealer_time_title">время работы</div>');
    html.push('<div class="dealer_time">'+params.time+'</div>');
    html.push('</div>');
    html.push('<div class="image">');

    if(params.images.length){
        html.push('<img src="http://mht.ru'+params.images[0]+'" ' + (params.images.length > 1 ? 'data-images="' + params.images.join(',') + '" class="js-switch-image"' : '') + '>');
        if(params.panoram){
            html.push('<span class="panoram_href" data-coord="'+params.coords.join(",")+'">Панорама местности</span>');
        }
    }else{
        if(params.panoram){
            html.push('<div class="panoram_view" id="panoram"></div>');
        }
    }

    html.push('</div>');
    html.push('</div>');


    var shops_map_placemark_layout = ymaps.templateLayoutFactory.createClass('<div class="map_mark"></div>');

    var placemark = new ymaps.Placemark(
        params.coords,
        {
            balloonContentHeader:'',
            balloonContentBody:html.join(''),
            balloonContentFooter: ''
        }, {
            hideIconOnBalloonOpen: false,
            iconLayout: 'default#image',
            iconImageSize: [20, 24],
            iconImageOffset: [-10, -30],
            iconImageHref: '/images/contacts/map_mark@2x.png',
        }
    );

    placemark.events.add(
        ["balloonopen"],
        function(event){
            $(".balloonContent").each(
                function(){
                    cord = $(this).attr("data-coord").split(",");
                    findPanoram(cord,10);


                    $('.js-switch-image').each(function(){
                        var $image = $(this)/*.removeClass('js-switch-image')*/,
                            images = $image.attr('data-images').split(','),
                            next = 1,
                            image = new Image;

                        image.src = images[next];

                        function step(){
                            if(!image.complete){
                                setTimeout(step, 100);
                                return;
                            }

                            $image.fadeOut(200, function(){
                                $image.attr('src', image.src);
                                $image.fadeIn(200);

                                next++;
                                if(next == images.length){
                                    next = 0;
                                }
                                image.src = images[next];
                                setTimeout(step, 3000);
                            });
                        };
                        setTimeout(step, 3000);
                    });

                }
            );
        }
    );

    placemark.events.add('mouseenter', function (e) {
        e.get('target').options.set({
            iconImageHref: '/images/contacts/map_mark_hover@2x.png'
        });
    })
    placemark.events.add('mouseleave', function (e) {
        e.get('target').options.set({
            iconImageHref: '/images/contacts/map_mark@2x.png'
        });
    });

    shops_map.geoObjects.add(placemark);


    function checkLocation(){
        if(location.hash == '#shop' + params.id){
            $("html,body").animate({"scrollTop":$("#shops_map").offset().top-100},500);
            setTimeout(function(){
                //placemark.balloon.open();
                shops_map.setCenter(placemark.geometry.getCoordinates(), 16 );

            }, 1);
        }
    }

    checkLocation();
    $(window).on('hashchange', function(){
        checkLocation();
    });
}

function ynadex_map_init() {
    var settings = {};

    $.each(window.mht.regions, function(i, region){
        if(region.code != mht.shopRegion){
            return;
        }
        settings = {
            center : [
                parseFloat(region.coords[0]),
                parseFloat(region.coords[1])
            ],
            zoom : parseFloat(region.zoom),
            controls : ['zoomControl']
        };
        return false;
    });

    shops_map = new ymaps.Map('shops_map', settings);
    shops_map.behaviors.disable('scrollZoom');

    $.each(mht.shops, function(i, shop){
        add_placemark(shop);
    });

    /*


     shops_map_placemark.events
     .add('mouseenter', function (e) {
     $(e).addClass("active");
     })
     .add('mouseleave', function (e) {
     $(e).removeClass("active");
     });
     */
}