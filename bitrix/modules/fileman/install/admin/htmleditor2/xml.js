// ========================
var xml_js = true;
// ========================

function BXXML()
{
	var pObj = this;
	var pXML;

	if(window.XMLHttpRequest)
		pXML = new XMLHttpRequest();
	else
	{
		try{pXML = new ActiveXObject("Msxml2.XMLHTTP");}catch(e){}
		if(!pXML)
			pXML = new ActiveXObject("Microsoft.XMLHTTP");
	}

	if(!pXML)
	{
		alert('XMLHttp object is not found.');
		return false;
	}
	this.pXML = pXML;
	return true;
}

BXXML.prototype.Load = function(sUrl, arParams, pAsyncFunction)
{
	this.DOMDocument = false;
	var pObj = this;
	var pXML = this.pXML;
	if(pAsyncFunction)
	{
		pXML.open("POST", sUrl, true);
		pXML.onreadystatechange = function()
		{
			if(pXML.readyState == 4)
			{
				pObj.DOMDocument = pXML.responseXML ;
				pAsyncFunction(pObj);
			}
		};
		pXML.send(null);
		return true;
	}

	try{
		if(arParams)
		{
			var data = BXPHPVal(arParams);
			var len = data.length;
		
			pXML.open("POST", sUrl, false);
			pXML.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			pXML.setRequestHeader("Content-Length", len);
			pXML.send(data);
		}
		else
		{
			pXML.open("GET", sUrl, false);
			pXML.send("");
		}
	}catch (e){return false;}
	
	if((pXML.status == 200 || (pXML.status == 0 && pXML.readyState==4)) && pXML.responseXML)
	{
		this.DOMDocument = pXML.responseXML;
		return true;
	}
	return false;
};

BXXML.prototype.Unserialize = function()
{
	var arRes = false;
	if(this.DOMDocument)
	{
		var oRootNodes = this.selectNodes("/params/variable");
		var oElement;
		for(var i=0; i<oRootNodes.length; i++)
		{
			oElement = oRootNodes[i];
			eval('arRes = '+oElement.getAttribute("value"));
			break;
		}
	}
	return arRes;
};

BXXML.prototype.selectNodes = function(xPath, oNode)
{
	if(this.DOMDocument.createNSResolver)
	{
		var oNodeTemp = false;
		var arNodes = [];
		var result = this.DOMDocument.evaluate(
			xPath,
			(oNode?oNode:this.DOMDocument),
			this.DOMDocument.createNSResolver(this.DOMDocument.documentElement),
			0,
			null
			);

		if(result)
		{
	 		while((oNodeTemp = result.iterateNext()))
	 			arNodes.push(oNodeTemp);
		}

		return arNodes;
	}

	return (oNode?oNode:this.DOMDocument).selectNodes(xPath);
};