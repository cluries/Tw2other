//
function $(idname)
{
	return document.getElementById(idname);
}

function addCloseIcon(idname) 
{
	var element = $(idname);
	if (!element) {
		return false;
	};

	var template = '<a href="#" onclick="closeParentElement(this);return false;" style="float:right;"><img src="template/default/images/close.png" style="margin:0;padding:0" /></a>';
	element.innerHTML+=template;
	return element;
}

function closeParentElement(element) 
{
	element.parentNode.style.display = 'none';
}