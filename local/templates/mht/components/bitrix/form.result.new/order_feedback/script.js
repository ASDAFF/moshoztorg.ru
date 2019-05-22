$.fn.initWebForm = function(){
	var form = $(this);
	var fields = $(this).find('[data-fieldname]');

	var MESS = {
		'browser': 'Браузер',
		'windowsize': 'Размер окна',
		'utm_source': 'UTM источник',
		'utm_campaign': 'UTM кампания',
		'utm_medium': 'UTM медиа',
		'utm_keyword': 'UTM ключевые слова',
		'location': 'Текущая страница',
		'message_sent': 'Сообщение отправлено'
	}

	function formatError(value){
		value = value.replace("Не заполнены следующие обязательные поля:", "Не заполнено поле");
		return value;
	}

	function getFieldsData(){
		var data = {};
		form.find('[data-fieldname]').each(function(){
			data[$(this).data('fieldname')] = $(this).val();
		});
		return data;
	}

	function getCookie(name) {
	  var matches = document.cookie.match(new RegExp(
	    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	  ));
	  return matches ? decodeURIComponent(matches[1]) : undefined;
	}

	function getUtms() {
		var utm = getCookie('__utmz');
		utms = {};
		if(utm){
			eval(utm.replace(/.*?(utm.*?)=([^|]*)[|]?/g, "utms['$1'] = '$2';\n"));
		}
		return utms;
	}

	function getUserInfo(){
		var userinfo = {};
		userinfo['browser'] = navigator.userAgent;
		userinfo['windowsize'] = $(window).width() + "×" + $(window).height();
		userinfo['location'] = document.location.href;
		utms = getUtms();
		if(utms.utmcsr){
			userinfo['utm_source'] = utms.utmcsr;
		}
		if(utms.utmccn){
			userinfo['utm_campaign'] = utms.utmccn;
		}
		if(utms.utmcmd){
			userinfo['utm_medium'] = utms.utmcmd;
		}
		if(utms.utmcmd){
			userinfo['utm_keyword'] = utms.utmctr;
		}

		var stringresult = "";
		$.each(userinfo, function(key, value){
			stringresult += MESS[key] + ": " + value + "\n";
		});
		return stringresult;
	}

	$(this).ajaxForm({
		beforeSerialize: function(){
			form.find('input[name="confirm"]').remove();
			form.find('input[data-fieldname="_utm"]').val(getUserInfo());
		},
		beforeSubmit: function(){
			form.find('input[type="submit"]').prop( "disabled", true).addClass('disabled');
		},
		success: function(data){
			form.find('input[type="submit"]').prop( "disabled", false).removeClass('disabled');
			form.find('.error-text').remove();
			form.find('.error').removeClass('error');

			var isFancybox = (form.parents('.fancybox-inner').length > 0);

			if(data.status == 'error'){
				$.each(data.errors, function(key, value){
					var container = form.find('[data-fieldname="' + key + '"]').parents('.field');
					if(container.length > 0){
						container.addClass('error');
					} else {
						container = form;
					}
					container.append('<div class="error-text">' + formatError(value) + '</div>');
				});
				$(document).trigger('webform.error', [form.attr('name'), data, getFieldsData()]);
			} else if(data.status == 'success'){
				$(document).trigger('webform.success', [form.attr('name'), getFieldsData()]);
				if(data.redirect){
					document.location = data.redirect;
				} else if(data.message) {
					form.parent().html('<div class="success-message">' + data.message + '</div>');
					if(isFancybox)
						$.fancybox.update();
				} else {
					form.parent().html('<div class="success-message">' + MESS['message_sent'] + '</div>');
					if(isFancybox)
						$.fancybox.update();
				}
			}
		},
		error: function(){
			form.find('input[type="submit"]').prop( "disabled", false).removeClass('disabled');
			alert("Server not available");
		}
	});
}