
(function(){
	'use strict';

	var varname = BX.message('BITRIX_CONVERSION_VARNAME');
	if (varname)
	{
		var getCookie = function (name) {
			var value = "; " + document.cookie;
			var parts = value.split("; " + name + "=");
			if (parts.length == 2) return parts.pop().split(";").shift();
		};

		try
		{
			var counters = JSON.parse(decodeURIComponent(getCookie(varname))).COUNTERS,
				i = 0, length = counters.length;

			for (; i < length; i++)
			{
				if (counters[i] == 'conversion_visit_day')
				{
					return;
				}
			}

			throw 'count';
		}
		catch (e)
		{
			BX.ajax.post(
				'/bitrix/tools/conversion/ajax_counter.php',
				{
					SITE_ID: BX.message('SITE_ID'),
					sessid: BX.message('bitrix_sessid')
				},
				function ()
				{

				}
			);
		}
	}
})();
