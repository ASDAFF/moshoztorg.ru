
	window.BX_YMapAddPlacemark = function(map, arPlacemark)
	{
		if (null == map)
			return false;

		if(!arPlacemark.LAT || !arPlacemark.LON)
			return false;

		var props = {};
		if (null != arPlacemark.TEXT && arPlacemark.TEXT.length > 0)
		{
			var value_view = '';

			if (arPlacemark.TEXT.length > 0)
			{
				var rnpos = arPlacemark.TEXT.indexOf("\n");
				value_view = rnpos <= 0 ? arPlacemark.TEXT : arPlacemark.TEXT.substring(0, rnpos);
			}

			props.balloonContent = arPlacemark.TEXT.replace(/\n/g, '<br />');
			props.hintContent = value_view;
			props.ID = arPlacemark.ID;
		}

		var obPlacemark = new ymaps.Placemark(
			[arPlacemark.LAT, arPlacemark.LON],
			props,
			{
				hideIconOnBalloonOpen: false,
				iconLayout: 'default#image',
				iconImageSize: [20, 24],
				iconImageOffset: [-10, -24],
				iconImageHref: '/img/contacts/map_mark@2x.png',				
			}
		);
		
		obPlacemark.events.add('mouseenter', function (e) {
			e.get('target').options.set({
				iconImageHref: '/img/contacts/map_mark_hover@2x.png'	
			});
		});
		
		obPlacemark.events.add('mouseleave', function (e) {
			e.get('target').options.set({
				iconImageHref: '/img/contacts/map_mark@2x.png'	
			});
		});

		obPlacemark.events.add('click', function (e) {
			var ID = e.get('target').properties.get("ID");
			setChangeStore(ID);
			$(".data .ora-storelist").animate({"scrollTop":$(".data .ora-storelist #row_"+ID).offset().top-$(".data .ora-storelist").offset().top+$(".data .ora-storelist").scrollTop()},500);
			return false;
		});
		
		map.geoObjects.add(obPlacemark);

		return obPlacemark;
	}


	window.BX_YMapAddPolyline = function(map, arPolyline)
	{
		if (null == map)
			return false;

		if (null != arPolyline.POINTS && arPolyline.POINTS.length > 1)
		{
			var arPoints = [];
			for (var i = 0, len = arPolyline.POINTS.length; i < len; i++)
			{
				arPoints.push([arPolyline.POINTS[i].LAT, arPolyline.POINTS[i].LON]);
			}
		}
		else
		{
			return false;
		}

		var obParams = {clickable: true};
		if (null != arPolyline.STYLE)
		{
			obParams.strokeColor = arPolyline.STYLE.strokeColor;
			obParams.strokeWidth = arPolyline.STYLE.strokeWidth;
		}
		var obPolyline = new ymaps.Polyline(
			arPoints, {balloonContent: arPolyline.TITLE}, obParams
		);

		map.geoObjects.add(obPolyline);

		return obPolyline;
	}
