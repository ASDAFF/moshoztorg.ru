var currentLink = -1;
var currentRow = null;

var GLOBAL_bDisableActions = false;
var GLOBAL_bDisableDD = false;

function BXOpenFD(index, type)
{
	window.GLOBAL_FD_PLAYLIST_IND = index;
	if (type == 'VIDEO')
		OpenFD_playlist_video();
	else
		OpenFD_playlist_image();
}

function BXSaveVideoPath(filename, filepath)
{
	var id = 'edit_area_location_' + window.GLOBAL_FD_PLAYLIST_IND;
	var input = document.getElementById(id).firstChild;
	input.value =  filepath + '/' + filename;
	input.onblur();
}

function BXSaveImagePath(filename, filepath)
{
	var id = 'edit_area_image_' + window.GLOBAL_FD_PLAYLIST_IND;
	var input = document.getElementById(id).firstChild;
	input.value =  filepath + '/' + filename;
	input.onblur();
}


function menuCheckIcons()
{
	var obLayout = document.getElementById('bx_playlist_layout');

	for (var i = 0, num = obLayout.childNodes.length; i < num; i++)
	{
		if (
			obLayout.childNodes[i].tagName
			&& obLayout.childNodes[i].tagName == 'DIV'
			&& obLayout.childNodes[i].className == 'bx-menu-placement'
		)
		{
			var obTable = obLayout.childNodes[i].firstChild.firstChild;
			obTable.rows[0].cells[6].firstChild.style.visibility = (i == 0 ? 'hidden' : 'visible'); // Up button
			obTable.rows[0].cells[7].firstChild.style.visibility = (i == num-1 ? 'hidden' : 'visible'); // Down button
		}
	}
}

function itemMoveUp(i)
{
	if (GLOBAL_bDisableActions)
		return;
	var obRow = document.getElementById('bx_item_row_' + i);
	var obPlacement = obRow.parentNode;

	var index = obPlacement.id.substring(18);
	if (index <= 1)
		return;
	var obNewPlacement = obPlacement.previousSibling;
	var obSwap = obNewPlacement.firstChild;
	obPlacement.appendChild(obSwap);
	obNewPlacement.appendChild(obRow);
	setCurrentRow(obRow);
	menuCheckIcons();
}

function itemMoveDown(i)
{
	if (GLOBAL_bDisableActions)
		return;

	var obRow = document.getElementById('bx_item_row_' + i);
	var obPlacement = obRow.parentNode;
	var obNewPlacement = obPlacement.nextSibling;
	if (null == obNewPlacement)
		return;
	var obSwap = obNewPlacement.firstChild;
	obPlacement.appendChild(obSwap);
	obNewPlacement.appendChild(obRow);
	setCurrentRow(obRow);
	menuCheckIcons();
}

function itemDelete(i)
{
	if (GLOBAL_bDisableActions)
		return;
	var obPlacement = document.getElementById('bx_item_row_' + i).parentNode;
	if (obPlacement.firstChild == currentRow)
		currentRow = null;
	obPlacement.parentNode.removeChild(obPlacement);
	menuCheckIcons();
}

function getAreaHTML(area, value, title)
{
	if (null === value) value = '';
	return '<div onmouseout="rowMouseOut(this)" onmouseover="rowMouseOver(this)" class="edit-field view-area" style="width: 220px; padding: 2px; display: block; border: 1px solid white; cursor: text; -moz-box-sizing: border-box; background-position: right center; background-repeat: no-repeat;" id="view_area_' + area + '" onclick="editArea(\'' + area + '\')" title="' + title + '">' + (value ? value : jsMenuMess.noname) + '</div>' +
		'<div class="edit-area" id="edit_area_' + area + '" style="display: none;"><input type="text" style="width: 220px;" name="' + area + '" value="' + value + '" onblur="viewArea(\'' + area + '\')" /></div>';
}

var currentEditingRow = null;

function editArea(area, bSilent)
{
	if (GLOBAL_bDisableActions)
		return;
	jsDD.Disable();
	GLOBAL_bDisableDD = true;

	jsDD.allowSelection();
	l = document.getElementById('bx_playlist_layout');
	l.ondrag = l.onselectstart = null;
	l.style.MozUserSelect = '';

	if (bSilent == null)
		bSilent = false;

	var obEditArea = document.getElementById('edit_area_' + area);
	var obViewArea = document.getElementById('view_area_' + area);
	obEditArea.style.display = 'block';
	obViewArea.style.display = 'none';
	obEditArea.firstChild.select();
	obEditArea.onkeydown = OnKeyDown;
	if (!bSilent)
	{
		obEditArea.firstChild.focus();
		if (jsUtils.IsIE())
			setTimeout(function () {setCurrentRow(obViewArea.parentNode.parentNode.parentNode.parentNode.parentNode)}, 30);
		else
			setCurrentRow(obViewArea.parentNode.parentNode.parentNode.parentNode.parentNode);
	}
	return obEditArea;
}

function viewArea(area)
{
	if (GLOBAL_bDisableActions)
		return;

	jsDD.Enable();
	GLOBAL_bDisableDD = false;

	l = document.getElementById('bx_playlist_layout');
	l.ondrag = l.onselectstart = jsUtils.False;
	l.style.MozUserSelect = 'none';

	var obEditArea = document.getElementById('edit_area_' + area);
	var obViewArea = document.getElementById('view_area_' + area);

	var val = jsUtils.trim(obEditArea.firstChild.value);
	obEditArea.firstChild.value = val;
	obEditArea.onkeydown = null;

	val = bxhtmlspecialchars(val);
	obViewArea.firstChild.innerHTML = val || jsMess.noname;
	//obViewArea.appendChild(document.createTextNode(obEditArea.firstChild.value.length > 0 ? obEditArea.firstChild.value : jsMenuMess.noname));

	obEditArea.style.display = 'none';
	obViewArea.style.display = 'block';

	currentEditingRow = null;
	setCurrentRow(obViewArea.parentNode.parentNode.parentNode.parentNode.parentNode);
	return obViewArea;
}

function setCurrentRow(i)
{
	if (typeof i != 'object')
		i = document.getElementById('bx_item_row_' + i);

	if (null != currentRow)
		currentRow.className = 'bx-edit-menu-item';

	i.className = 'bx-edit-menu-item bx-menu-current-row';
	currentRow = i;
}


function rowMouseOut(obArea)
{
	obArea.className = 'edit-field view-area va_playlist';
	obArea.style.backgroundColor = 'transparent';
}

function rowMouseOver (obArea, bFd)
{
	if (GLOBAL_bDisableActions || jsDD.bPreStarted)
		return;
	obArea.className = 'edit-field-active view-area' + (bFd ? ' va_playlist_fd_over' : ' va_playlist');
	obArea.style.backgroundColor = 'white';
}


/* DD handlers */
function BXDD_DragStart()
{
	if (GLOBAL_bDisableDD)
		return false;

	this.BXOldPlacement = this.parentNode;
	var id = this.id.substring(12);
	rowMouseOut(viewArea('title_' + id));
	rowMouseOut(viewArea('author_' + id));
	rowMouseOut(viewArea('duration_' + id));
	rowMouseOut(viewArea('location_' + id));
	rowMouseOut(viewArea('image_' + id));
	GLOBAL_bDisableActions = true;
	return true;
}

function BXDD_DragStop()
{
	this.BXOldPlacement = false;
	setTimeout('GLOBAL_bDisableActions = false', 50);
	return true;
}

function BXDD_DragHover(obPlacement, x, y)
{
	if (GLOBAL_bDisableDD)
		return false;

	// dirty hack. never code anything like this!
	y += jsPopup_playlist.div_inner.scrollTop;
	//y += document.getElementById('bx_popup_content').scrollTop;
	var index = jsDD.searchDest(x, y);
	if (index === false)
		return false;
	obPlacement = jsDD.arDestinations[index];

	if (obPlacement == this.BXOldPlacement)
		return false;

	var obSwap = obPlacement.firstChild;
	this.BXOldPlacement.appendChild(obSwap);
	obPlacement.appendChild(this);
	this.BXOldPlacement = obPlacement;
	menuCheckIcons();
	return true;
}

function getRowInnerHTML(id, val, i, width, fd)
{
	width = parseInt(width);
	val = val || jsMess.noname;
	var js_fd_par = fd ? ', true' : '';
	var res =  '<td valign="top">' +
	'<div onmouseout="rowMouseOut(this)" onmouseover="rowMouseOver(this' + js_fd_par + ')" class="edit-field view-area va_playlist" id="view_area_' + id + '_' + i + '" style="width: ' + width + 'px;" onclick="editArea(\'' + id + '_' + i + '\')" title="' + jsMess.clickToEdit + '"><div class="playlist_text">' + val + '</div>';
	if (fd)
		res += '<span onclick="BXOpenFD(\'' + i + '\', \'' + fd + '\');" class="rowcontrol folder fd_icon" title="' + jsMess.openFDTitle + '"></span>'
	res += '</div>' +
	'<div class="edit-area" id="edit_area_' + id + '_' + i + '" style="display:none;"><input type="text" style="width: ' + width + 'px;" name="' + id + '_' + i + '" value="' + val + '" onblur="viewArea(\'' + id + '_' + i + '\')" /></div>' +
	'</td>';
	return res;
}

function itemAdd()
{
	var obCounter = document.forms[jsPopup_playlist.form_name].itemcnt;
	var i = parseInt(obCounter.value);
	obCounter.value = ++i;

	var obPlacement = document.createElement('DIV');
	obPlacement.className = 'bx-menu-placement';
	obPlacement.id = 'bx_item_placement_' + i;
	document.getElementById('bx_playlist_layout').appendChild(obPlacement);

	var obRow = document.createElement('DIV');
	obRow.className = 'bx-edit-menu-item';
	obRow.id = 'bx_item_row_' + i;
	obPlacement.appendChild(obRow);

	var innerHTML = '<table border="0" cellpadding="2" cellspacing="0" class="bx-width100 internal playlist-table"><tbody><tr>' +
	'	<td>' +
	'	<input type="hidden" name="ids[]" value="' + i + '" />' +
	'	<span class="rowcontrol drag" title="' + jsMess.itemDrag + '"></span></td>' +
		getRowInnerHTML('title', jsMess.noname, i, 150) +
		getRowInnerHTML('author', jsMess.noname, i, 150) +
		getRowInnerHTML('duration', jsMess.noname, i, 100) +
		getRowInnerHTML('location', jsMess.noname, i, 110, 'VIDEO') +
		getRowInnerHTML('image', jsMess.noname, i, 110, 'IMAGE') +
	'	<td><span onclick="itemMoveUp(' + i + ')" class="rowcontrol up" style="visibility: ' + (i == 1 ? 'hidden' : 'visible') + '" title="' + jsMess.itemUp + '"></span></td>' +
	'	<td><span onclick="itemMoveDown(' + i + ')" class="rowcontrol down" style="visibility: hidden" title="' + jsMess.itemDown + '"></span></td>' +
	'	<td><span onclick="itemDelete(' + i + ')" class="rowcontrol delete" title="' + jsMess.itemDel + '"></span></td>' +
	'</tr></tbody></table>';

	obRow.innerHTML = innerHTML;

	jsDD.registerDest(obPlacement);
	obRow.onbxdragstart = BXDD_DragStart;
	obRow.onbxdragstop = BXDD_DragStop;
	obRow.onbxdraghover = BXDD_DragHover;
	jsDD.registerObject(obRow);

	setCurrentRow(i);
	menuCheckIcons();
	jsPopup_playlist.div_inner.scrollTop = 10000;
	//document.getElementById('bx_popup_content').scrollTop = 10000;
	setTimeout(function () {editArea('title_' + i);}, 30);
}

function bxhtmlspecialchars(str)
{
	if(typeof(str)!='string')
		return str;
	str = str.replace(/&/g, '&amp;');
	str = str.replace(/"/g, '&quot;');
	str = str.replace(/</g, '&lt;');
	str = str.replace(/>/g, '&gt;');
	return str;
}


function OnKeyDown(e)
{
	if(!e) e = window.event;
	if (e.ctrlKey || e.altKey)
		return true;

	if (e.which == 9)
	{
		var Fields = ['title', 'author', 'duration', 'location', 'image'];
		var len = Fields.length;
		var target = e.target || e.srcElement;
		var maxRows = parseInt(document.forms[jsPopup_playlist.form_name].itemcnt.value);

		var dest_type, dest_row;
		var name = target.name;
		var _ind = name.indexOf('_');
		var type = name.substr(0, _ind).toLowerCase();
		dest_row = parseInt(name.substr(_ind + 1));

		if (!e.shiftKey)
		{
			for (var i = 0; i < len; i++)
			{
				if (Fields[i] == type)
				{
					if (i == len - 1)
					{
						if (maxRows == dest_row)
							return itemAdd();
						dest_type = Fields[0];
						dest_row++;
						break;
					}
					else
					{
						dest_type = Fields[i + 1];
						break;
					}
				}
			}
		}
		else
		{
			for (var i = 0; i < len; i++)
			{
				if (Fields[i] == type)
				{
					if (i == 0)
					{
						if (dest_row <= 1)
							return;
						dest_row--;
						dest_type = Fields[len - 1];
						break;
					}
					else
					{	
						dest_type = Fields[i - 1];
						break;
					}
				}
			}
		}
		viewArea(name);
		if (dest_type && dest_row)
			setTimeout(function () {editArea(dest_type + '_' + dest_row);}, 30);
	}
};
