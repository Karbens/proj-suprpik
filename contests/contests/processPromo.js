function processPromo() {
	
	//first validate the form
	var theForm = document.getElementById("contestForm");
	if (!formvalidation(theForm)) return false; 
	//check for events
	if(!$('input:checkbox[name=eventchoice]:checked').val()) {
		alert("Please select an event.");
		return false;
	}
	
	//get hidden/input fields values
    var contestID = $("#contestid").val();
	var contestDate = $("#contestdate").val();
	var cid = $.trim($("#cid").val());
	var cemail = $.trim($("#email").val());
	var eventTime = $("#eventtime_" + eventID).val();
	var eventValue = $('input:checkbox[name=eventchoice]:checked').val();
	
	var servData = 'action=addnewentry' +
				   '&contestDate=' + contestDate +
				   '&eventTime=' + eventTime +
				   '&contestID=' + contestID + 
				   '&cid=' + cid +
				   '&email=' + cemail +
				   '&entryValue=' + eventValue;
	alert(servData);return false;
	//ajax the servData
	$.ajax({
	       url: "http://superpicks.com/contests/processPromo.php",
	       type: "POST",
	       data: servData,
	       cache: false,
	       success: function (html) {
		     alert(html);
			 
		   }
	});
	
  }
  
  function processPicks() {
	
	//first validate the form
	var theForm = document.getElementById("contestForm");
	if (!formvalidation(theForm)) return false; 
	
	//get hidden/input fields values
    var contestID = $("#contestid").val();
	var contestDate = $("#contestdate").val();
	var cid = $.trim($("#cid").val());
	var cemail = $.trim($("#email").val());
	
	var servData = 'action=seemypicks' +
				   '&contestDate=' + contestDate +
				   '&contestID=' + contestID + 
				   '&cid=' + cid +
				   '&email=' + cemail;
	//alert(servData);return false;
	//ajax the servData
	$.ajax({
	       url: "http://superpicks.com/contests/processPromo.php",
	       type: "POST",
	       data: servData,
	       cache: false,
	       success: function (html) {
		     alert(html);
			 
		   }
	});
	
  }
  
function emptyvalidationN(entered, alertbox) {
    with(entered) {
        if (value == null || value == "") {
            if (alertbox != "") {
                alert(alertbox);
            }
            return false;
        } else {
            return true;
        }
    }
}

function checknumber(num, al) {
    var anum = /(^\d+$)|(^\d+\.\d+$)/
    if (anum.test(num.value)) return true;
    else {
        alert(al);
        return false;
    }
}

function checkMail(alertbox)
{
   var filter = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9][a-zA-Z0-9.-]*[\.]{1}[a-zA-Z]{2,4}$/;

   if (!filter.test( $.trim($("input#email").val()) )) 
   {
	 if(alertbox != "") 
	 {	
		alert(alertbox);
     }
	 return false;
   }
   return true;
}

function formvalidation(thisform) {
    with(thisform) {
		if (emptyvalidationN(cid, " Please enter your Username. ") == false) {
            cid.focus();
            return false;
        };
		if (checkMail(" Please enter valid Email. ") == false) {
            email.focus();
            return false;
        };
    }
    return true;
}