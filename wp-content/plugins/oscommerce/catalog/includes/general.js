/*
  $Id: general.js,v 1.3 2003/02/10 22:30:55 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

function SetFocus(TargetFormName) {
  var target = 0;
  if (TargetFormName != "") {
    for (i=0; i<document.forms.length; i++) {
      if (document.forms[i].name == TargetFormName) {
        target = i;
        break;
      }
    }
  }

  var TargetForm = document.forms[target];
    
  for (i=0; i<TargetForm.length; i++) {
    if ( (TargetForm.elements[i].type != "image") && (TargetForm.elements[i].type != "hidden") && (TargetForm.elements[i].type != "reset") && (TargetForm.elements[i].type != "submit") ) {
      TargetForm.elements[i].focus();

      if ( (TargetForm.elements[i].type == "text") || (TargetForm.elements[i].type == "password") ) {
        TargetForm.elements[i].select();
      }

      break;
    }
  }
}

function RemoveFormatString(TargetElement, FormatString) {
  if (TargetElement.value == FormatString) {
    TargetElement.value = "";
  }

  TargetElement.select();
}

function CheckDateRange(from, to) {
  if (Date.parse(from.value) <= Date.parse(to.value)) {
    return true;
  } else {
    return false;
  }
}

function IsValidDate(DateToCheck, FormatString) {
  var strDateToCheck;
  var strDateToCheckArray;
  var strFormatArray;
  var strFormatString;
  var strDay;
  var strMonth;
  var strYear;
  var intday;
  var intMonth;
  var intYear;
  var intDateSeparatorIdx = -1;
  var intFormatSeparatorIdx = -1;
  var strSeparatorArray = new Array("-"," ","/",".");
  var strMonthArray = new Array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
  var intDaysArray = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

  strDateToCheck = DateToCheck.toLowerCase();
  strFormatString = FormatString.toLowerCase();
  
  if (strDateToCheck.length != strFormatString.length) {
    return false;
  }

  for (i=0; i<strSeparatorArray.length; i++) {
    if (strFormatString.indexOf(strSeparatorArray[i]) != -1) {
      intFormatSeparatorIdx = i;
      break;
    }
  }

  for (i=0; i<strSeparatorArray.length; i++) {
    if (strDateToCheck.indexOf(strSeparatorArray[i]) != -1) {
      intDateSeparatorIdx = i;
      break;
    }
  }

  if (intDateSeparatorIdx != intFormatSeparatorIdx) {
    return false;
  }

  if (intDateSeparatorIdx != -1) {
    strFormatArray = strFormatString.split(strSeparatorArray[intFormatSeparatorIdx]);
    if (strFormatArray.length != 3) {
      return false;
    }

    strDateToCheckArray = strDateToCheck.split(strSeparatorArray[intDateSeparatorIdx]);
    if (strDateToCheckArray.length != 3) {
      return false;
    }

    for (i=0; i<strFormatArray.length; i++) {
      if (strFormatArray[i] == 'mm' || strFormatArray[i] == 'mmm') {
        strMonth = strDateToCheckArray[i];
      }

      if (strFormatArray[i] == 'dd') {
        strDay = strDateToCheckArray[i];
      }

      if (strFormatArray[i] == 'yyyy') {
        strYear = strDateToCheckArray[i];
      }
    }
  } else {
    if (FormatString.length > 7) {
      if (strFormatString.indexOf('mmm') == -1) {
        strMonth = strDateToCheck.substring(strFormatString.indexOf('mm'), 2);
      } else {
        strMonth = strDateToCheck.substring(strFormatString.indexOf('mmm'), 3);
      }

      strDay = strDateToCheck.substring(strFormatString.indexOf('dd'), 2);
      strYear = strDateToCheck.substring(strFormatString.indexOf('yyyy'), 2);
    } else {
      return false;
    }
  }

  if (strYear.length != 4) {
    return false;
  }

  intday = parseInt(strDay, 10);
  if (isNaN(intday)) {
    return false;
  }
  if (intday < 1) {
    return false;
  }

  intMonth = parseInt(strMonth, 10);
  if (isNaN(intMonth)) {
    for (i=0; i<strMonthArray.length; i++) {
      if (strMonth == strMonthArray[i]) {
        intMonth = i+1;
        break;
      }
    }
    if (isNaN(intMonth)) {
      return false;
    }
  }
  if (intMonth > 12 || intMonth < 1) {
    return false;
  }

  intYear = parseInt(strYear, 10);
  if (isNaN(intYear)) {
    return false;
  }
  if (IsLeapYear(intYear) == true) {
    intDaysArray[1] = 29;
  }

  if (intday > intDaysArray[intMonth - 1]) {
    return false;
  }
  
  return true;
}

function IsLeapYear(intYear) {
  if (intYear % 100 == 0) {
    if (intYear % 400 == 0) {
      return true;
    }
  } else {
    if ((intYear % 4) == 0) {
      return true;
    }
  }

  return false;
}


function searchfield(feld,foc){
var field = document.getElementById(feld);
if(foc=='in'){
	field.value='';
	}else{
	if(field.value==''){
		field.value='search';
	}else{
	  field.value = field.value;
	}
}
}

function klapps(who1,who2){
var detitel = document.getElementById(who1);
var delayer = document.getElementById(who2);

if (delayer && delayer.style.display == 'none'){
if(detitel != document.getElementById('categoryboxtitel')){
document.getElementById('catinfocontainer').style.display = 'none';
document.getElementById('categoryboxtitel').style.backgroundImage="url(includes/sts_templates/shop/images/dropdown.gif)";
}
if(detitel != document.getElementById('manuboxtitel')){
document.getElementById('manuoutcontainer').style.display = 'none';
document.getElementById('manuboxtitel').style.backgroundImage="url(includes/sts_templates/shop/images/dropdown.gif)";
}
if(detitel != document.getElementById('formatboxtitel')){
document.getElementById('formatscontainer').style.display = 'none';
document.getElementById('formatboxtitel').style.backgroundImage="url(includes/sts_templates/shop/images/dropdown.gif)";
}
delayer.style.display = 'block';
detitel.style.backgroundImage="url(includes/sts_templates/shop/images/dropup.gif)";
}else{
delayer.style.display = 'none';
detitel.style.backgroundImage="url(includes/sts_templates/shop/images/dropdown.gif)";
} 
}

function check_sb(sbid){
chckz=0;
var rows = document.getElementsByName('sb_chck');
for (var i=0; i<rows.length; i++) {
   if (rows[i].checked == true) {
      chckz++;
   }
 }
 for (var i=0; i<=rows.length; i++) {
 	 	if(document.getElementById('sb_'+i)){
 	    document.getElementById('sb_'+i).style.display = 'none';
 	   }
 	 }
 if(document.getElementById('sb_'+chckz)){
      document.getElementById('sb_'+chckz).style.display = 'block';
      dewert='';
      document.getElementById('sb_productsids'+chckz).value = '';
      for (var i=0; i<rows.length; i++) {
         if (rows[i].checked == true) {
         	  dewert += rows[i].value+'|';
         }
       }
      document.getElementById('sb_productsids'+chckz).value = dewert.substr(0,dewert.length-1);
  }
}
