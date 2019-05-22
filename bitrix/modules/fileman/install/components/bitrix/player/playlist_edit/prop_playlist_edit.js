function ComponentPropsEditPlaylistDialog(arParams)
{
	var oBut = document.createElement("INPUT");
	oBut.setAttribute('type', 'button');
	arParams.oCont.appendChild(oBut);
	var arData = arParams.data.split('||');

	oBut.value = arData[0] || '';
	oBut.onclick = function()
	{
		var arElements = arParams.getElements();
		if (!arElements || !arElements.PATH)
			return;
		var path = arElements.PATH.value;
		if (!path)
		{
			alert(arData[1] || 'Incorrect Path to playlist');
			return;
		}
		path = jsUtils.urlencode(path);
		var oCallBack = function()
		{
			if (!window.jsPopup_playlist)
				jsPopup_playlist = new JCPopup({suffix: "playlist", zIndex: 2000});
			jsPopup_playlist.ShowDialog('/bitrix/components/bitrix/player/player_playlist_edit.php?lang=ru&site=ru&path=' + path + '&target=editor&bxpiheight=227', {width:'800', height:'346'});
			if (!jsPopup_playlist._CloseDialog)
			{
				jsPopup_playlist._CloseDialog = jsPopup_playlist.CloseDialog;
				jsPopup_playlist.CloseDialog = function()
				{
					if (window.style_1 && window.style_1.parentNode)
						window.style_1.parentNode.removeChild(window.style_1);
					if (window.style_2 && window.style_2.parentNode)
						window.style_2.parentNode.removeChild(window.style_2);
					jsPopup_playlist._CloseDialog();
				};
			}
		};
		window.style_1 = jsUtils.loadCSSFile('/bitrix/themes/.default/pubstyles.css');
		if (!window.JCPopup)
			jsUtils.loadJSFile('/bitrix/js/main/public_tools.js', oCallBack);
		else
			oCallBack();
	}
}