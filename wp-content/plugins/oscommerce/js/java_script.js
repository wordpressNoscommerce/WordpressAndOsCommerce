// JavaScript Document
function frm_validate(oF) 
{
	var why = "";

	why = checkinfo("Shop Name", oF.vchShopName.value);
	
	if(why != "")
	{
		alert(why);
		oF.vchShopName.focus();
		return false;
	}
	
	why = checkinfo("Url", oF.vchUrl.value);
	
	if(why != "")
	{
		alert(why);
		oF.vchUrl.focus();
		return false;
	}
	else
	{
		why = checkurl("Url", oF.vchUrl.value);

		if(why != "")
		{
			alert(why);
			oF.vchUrl.focus();
			return false;
		}
	}
	
	return true;
}

function checkinfo(strname, strvalue) 
{
	var error = "";
	
	if(strvalue == "" || strvalue == "0")
		error = strname + " must be filled in.\n";
	
	return error;
}

function checkurl(url)
{
	var error = "";

	var illegalChars = /[\(\)\<\>\,\'\#\$\*\!\+\^\~\;\:\\\"\[\]]/;
												
	if(url.match(illegalChars)) 
		error = "The url contains illegal characters.\n";
		
	return error;
}

function confirm_delete(url)
{
	if(confirm("Are you sure you want to delete this shop?"))
		document.location.href = url;
	else
		return false;
}