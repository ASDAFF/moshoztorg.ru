
var sbbl = {

	toggleExpandCollapseCart: function ()
	{
		if (sbbl.bClosed)
		{
			BX.removeClass(sbbl.elemBlock, "close");
			sbbl.elemStatus.innerHTML = sbbl.strCollapse;
			sbbl.bClosed = false;
		}
		else // Opened
		{
			BX.addClass(sbbl.elemBlock, "close");
			sbbl.elemStatus.innerHTML = sbbl.strExpand;
			sbbl.bClosed = true;
		}
		setTimeout(sbbl.fixCart, 100);
	},

	fixCartAfterAjax: function ()
	{
		if (sbbl.elemBlock)
		{
			sbbl.elemStatus = BX("bx_cart_block_status");
			if (sbbl.bClosed)
				sbbl.elemStatus.innerHTML = sbbl.strExpand;
			else // Opened
				sbbl.elemStatus.innerHTML = sbbl.strCollapse;

			sbbl.elemProducts = BX('bx_cart_block_products');
		}
	},

	fixCartVerticalPosition: function()
	{
		if (sbbl.strVertical == 'vcenter')
		{
			var top = sbbl.windowHeight/2 - (sbbl.elemBlock.offsetHeight/2);
			if (top < 5)
				top = 5;
			sbbl.elemBlock.style.top = top + 'px';
		}
	},

	fixCart: function()
	{
		sbbl.windowHeight = 'innerHeight' in window
			? window.innerHeight
			: document.documentElement.offsetHeight;

		// set position
		if (sbbl.strVertical == 'top')
		{
			var elemPanel = BX("bx-panel");
			if (elemPanel)
				sbbl.elemBlock.style.top = elemPanel.offsetHeight + 5 + 'px';
		}
		else
			sbbl.fixCartVerticalPosition();

		if (sbbl.strHorizontal == 'hcenter')
		{
			var windowWidth = 'innerWidth' in window
				? window.innerWidth
				: document.documentElement.offsetWidth;
			var left = windowWidth/2 - (sbbl.elemBlock.offsetWidth/2);
			if (left < 5)
				left = 5;
			sbbl.elemBlock.style.left = left + 'px';
		}

		// toggle max height
		if (! sbbl.elemProducts)
			return;

		if (sbbl.bClosed)
		{
			if (sbbl.bMaxHeight)
			{
				BX.removeClass(sbbl.elemBlock, 'max_height');
				sbbl.bMaxHeight = false;
			}
		}
		else // Opened
		{
			if (sbbl.bMaxHeight)
			{
				if (sbbl.elemProducts.scrollHeight == sbbl.elemProducts.clientHeight)
				{
					BX.removeClass(sbbl.elemBlock, 'max_height');
					sbbl.bMaxHeight = false;
				}
			}
			else
			{
				if (sbbl.strVertical == 'top' || sbbl.strVertical == 'vcenter')
				{
					if (sbbl.elemBlock.offsetTop + sbbl.elemBlock.offsetHeight >= sbbl.windowHeight)
					{
						BX.addClass(sbbl.elemBlock, 'max_height');
						sbbl.bMaxHeight = true;
					}
				}
				else
				{
					if (sbbl.elemBlock.offsetHeight >= sbbl.windowHeight)
					{
						BX.addClass(sbbl.elemBlock, 'max_height');
						sbbl.bMaxHeight = true;
					}
				}
			}
		}

		sbbl.fixCartVerticalPosition();
	},

	refreshCart: function (data)
	{
		if (! data)
			data = {};

		data.sessid = BX.bitrix_sessid();
		data.siteId = sbbl.siteId;
		data.templateName = sbbl.templateName;
		data.arParams = sbbl.arParams;

		BX.ajax({
			url: sbbl.ajaxPath,
			method: 'POST',
			dataType: 'html',
			data: data,
			onsuccess: function(result)
			{
				if (sbbl.elemBlock)
					sbbl.elemBlock.innerHTML = result;

				setTimeout(sbbl.fixCart, 100);
			}
		});
	},

	removeItemFromCart: function (id)
	{
		sbbl.refreshCart ({sbblRemoveItemFromCart: id});
	}
};


