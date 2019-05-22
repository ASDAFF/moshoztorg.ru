$(function(){
    $('.photosslider').slick({
        autoplay: true,
        slidesToShow: 1,
        asNavFor: '.photossliderpager'
    });
    
    $('.photossliderpager').slick({
        autoplay: true,
        asNavFor: '.photosslider',
        slidesToShow: 3,
        arrows: false,
        focusOnSelect: true
    });
    
    var shops_map;
    ymaps.ready(ynadex_map_init);
    
    function add_placemark(lng, lat){

        var shops_map_placemark_layout = ymaps.templateLayoutFactory.createClass('<div class="map_mark"></div>');
        
        var placemark = new ymaps.Placemark(
            [lng, lat], 
            {}, {
                hideIconOnBalloonOpen: false,
                iconLayout: 'default#image',
                iconImageSize: [30, 30],
                iconImageOffset: [-15, -30],
                iconImageHref: '/img/contacts/map_mark@2x.png'
            }
        );

      
        placemark.events.add('mouseenter', function (e) {
            e.get('target').options.set({
                iconImageHref: '/img/contacts/map_mark_hover@2x.png'	
            });
        });


        placemark.events.add('mouseleave', function (e) {
            e.get('target').options.set({
                iconImageHref: '/img/contacts/map_mark@2x.png'	
            });
        });

        shops_map.geoObjects.add(placemark);
    }


    function ynadex_map_init() {
        var settings = {
                center : [
                    $('#shops_map').attr('data-lng'),
                    $('#shops_map').attr('data-lat')
                ],
                zoom : 11,
                controls : ['zoomControl']
            };

        shops_map = new ymaps.Map('shops_map', settings);
        shops_map.behaviors.disable('scrollZoom');

        
        add_placemark($('#shops_map').attr('data-lng') , $('#shops_map').attr('data-lat'));
        
    }
});
