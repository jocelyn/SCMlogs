////////////////////////////////////
// script to collpase/expand block
// 


var iCBToogleOpen = 1;
var iCBToogleClosed = -1;
var iCBToogleNotDefined = 0;

function BC_toggleCookieName (strName)
{
	return escape ("__" + strName + "__ToggleState");
}
function BC_storeToogleState (strName, fDisplayed)
{
	try
	{
		var strCookieName = BC_toggleCookieName (strName);
		var dateNow = new Date();
		var iState = iCBToogleNotDefined;

		if (fDisplayed){
			iState = iCBToogleOpen;
		}else{
			iState = iCBToogleClosed;
		}

		dateNow.setYear ((dateNow.getUTCFullYear() + 2));
		document.cookie = strCookieName + "=" + iState.toString() + "; expires=" + dateNow.toGMTString();
	}
	catch (e)
	{
	}
}
function BC_retrieveToogleState (strName)
{
	try
	{
		var strCookieName = BC_toggleCookieName (strName);
		var arrItems = document.cookie.split (';');

		for (var i = 0; i < arrItems.length; i++)
		{
			var arrPair = arrItems[i].split ('=')
				if (' ' == arrPair[0].charAt (0)){
					// Remove leading whitespace
					arrPair[0] = arrPair[0].substring (1, arrPair[0].length)
				}
			if (strCookieName == arrPair[0])
			{
				if (arrPair.length > 1)
				{
					var iRes = parseInt (arrPair[1]);
					if (!isNaN (iRes)){
						return iRes;
					}
				}
			}
		}
		return iCBToogleNotDefined;
	}
	catch (e)
	{
		return iCBToogleNotDefined;
	}
}

function BC_BlockToggle(id, elt, t_open, t_close)
{
	vista = 'block';
	if (document.layers)
	{
		vista = (document.layers[id].display == 'none') ? 'block' : 'none';
		document.layers[id].display = vista;
	}
	else if (document.all)
	{
		vista = (document.all[id].style.display == 'none') ? 'block' : 'none';
		document.all[id].style.display = vista;
	}
	else if (document.getElementById)
	{
		vista = (document.getElementById(id).style.display == 'none') ? 'block' : 'none';
		document.getElementById(id).style.display = vista;
	}
	if (vista == 'none') {
		elt.innerHTML= t_open;
		BC_storeToogleState(id, false);
	} else {
		elt.innerHTML= t_close;
		BC_storeToogleState(id, true);
	}
}

function BC_BlockControlIsOpened(id, expanded)
{
	var iState = BC_retrieveToogleState (id);
	var fOpen = expanded;
	switch (iState)
	{
		case iCBToogleOpen:
			fOpen = true;
			break;
		case iCBToogleClosed:
			fOpen = false;
			break;
		case iCBToogleNotDefined:
			break
	}
	return fOpen;
}

function BC_BlockId(id)
{
	return '_' + id;
}

function StartBlockControl(id,t_open,t_close,expanded)
{
	var bl_id= BC_BlockId(id);
	var fOpen = BC_BlockControlIsOpened(bl_id, expanded);
	document.write('(<a href="#" onclick="BC_BlockToggle(\'' + bl_id + '\',this,\'' + t_open + '\',\''+ t_close + '\');return true;" class="_block_control" >');
			document.write(fOpen ? t_close : t_open);
			document.write('</a>)<br/>');
	document.write('<div id="' + bl_id + '" style="display: ' + (fOpen ? 'block' : 'none') + '">');
	return true;
}
function EndBlockControl(id) {
	var bl_id= BC_BlockId(id);
	document.write('</div> <!-- end ' + bl_id + ' -->');
}

function StartBoxBlockControl(title,id,t_open,t_close,expanded)
{
	var bl_id= BC_BlockId(id);
	var fOpen = BC_BlockControlIsOpened(bl_id, expanded);
	document.write('<h1>');
	document.write(' <a href="#" onclick="BC_BlockToggle(\'' + bl_id + '\',this,\'' + t_open + '\',\''+ t_close + '\');return true;" class="_block_control" >');
	document.write(fOpen ? t_close : t_open);
	document.write('</a>');
	document.write(title);
	document.write('</h1>');
	document.write('<div id="' + bl_id + '" style="display: ' + (fOpen ? 'block' : 'none') + '">');
	return true;
}
function EndBoxBlockControl() {
	document.write('</div>');
	return true;
}

