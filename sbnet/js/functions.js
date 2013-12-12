function XHTMLTarget(){
if (!document.getElementsByTagName) return;
var anchors = document.getElementsByTagName("a");
for (var i=0; i<anchors.length; i++){
		var anchor = anchors[i];
		if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external" )
			{anchor.target = "_blank";}
		else if(anchor.getAttribute("href") && anchor.getAttribute("rel") == "lines")
			{anchor.target = "linesframe";}
		else if(anchor.getAttribute("href") && anchor.getAttribute("rel") == "top")
			{anchor.target = "_top";}
	}
}

function DropDownMenu(entered)
{
	with (entered)
	{
	ref=options[selectedIndex].value;
	splitcharacter=ref.lastIndexOf("&");
	if (splitcharacter!=-1) {loc=ref.substring(0,splitcharacter); target=ref.substring(splitcharacter+1,1000).toLowerCase();}
	else {loc=ref; target="_self";};
	lowloc=loc.toLowerCase();
	if (lowloc=="false") {return;}
	if (target=="_self") {document.location=loc;}
	else {
		if (target=="_top") {top.location=loc;}
		else {if (target=="_blank") {window.open(loc);}
			else{if (target=="_parent") {parent.location=loc;}
				else {parent.frames[target].location=loc;};
				}
			}
		}
	}
}
function checkForm(theform){
  if(theform.userid.value == "" || document.loginform.passwd.value == ""){alert("Please make sure both Username and Password are entered.");
  theform.userid.focus();
  return false;
  }else{
  return true;}
}
function bookmark() {
	if ((navigator.appName == "Microsoft Internet Explorer") && (parseInt(navigator.appVersion) >= 4)) {
	var url="http://www.sportsbetting.ag";
	var title="sportsbetting.ag";
	window.external.AddFavorite(url,title);
	}
	else {
	var msg = "Don't forget to bookmark us!";
	if(navigator.appName == "Netscape") msg += "  (CTRL-D)";
	alert(msg);
	}
}
function newWin2() {
	if (navigator.appName.indexOf("Microsoft") != -1){
	win =
	window.open("http://www.sportsbetting.ag/scoreboard3.html","scores","height=250,width=132");
	win.focus();
	}
	else if (navigator.appName.indexOf("Netscape") != -1){
	win =
	window.open("http://www.sportsbetting.ag/scoreboard4.html","scores","height=240,width=132");
	win.focus();
	}
}
/* Cashier */
function isEmpty(s) { return ((s == null) || (s.length == 0)); }
var whitespace = " \t\n\r";
function isWhitespace (s) {
  var i;
  if (isEmpty(s)) return true;
  for (i = 0; i < s.length; i++) {
    var c = s.charAt(i);
    if (whitespace.indexOf(c) == -1) return false;
  }
  return true;
}
function doesExist (s) { return ( ! isEmpty(s) && ! isWhitespace (s) ); }




function validateFormsportsbook(){
  var form = document.sportsbook;
  if ( form.USERID  ) {
    if ( ! doesExist ( form.USERID.value ) ) {
      alert ( "You left the Username field blank." );
      form.USERID.focus();
      return ( false );
    }
  }
  if ( form.PASSWORD  ) {
    if ( ! doesExist ( form.PASSWORD.value ) ) {
      alert ( "You left the Password field blank." );
      form.PASSWORD.focus();
      return ( false );
    }
  }
  return ( true );
}



function validateAndSubmitsportsbook() {
  var form = document.sportsbook;
  var ok = validateForm ();
  if ( ok ) form.submit ();
  return ( ok );
}
/*	end of Cashier	*/

racepop = function(){window.open('http://196.40.45.84/BOSSWagering/Racebook/InternetWagering/?casinoid=27003','SBSWIwindow','width=960,height=720,scrollbars=0,status=0,resize=1,left=0,top=0');}

raceload = function(){/*window.location.href = '/racebook/bet_on_races.html';*/}

/*	-----------------------		*/
function emptyvalidation(entered, alertbox)
{
	with (entered)
	{
		if (value==null || value=="")
		{if (alertbox!="") {alert(alertbox);} return false;}
		else {return true;}
	}
}
function emailvalidation(entered, alertbox)
{
	with (entered)
	{
		apos=value.indexOf("@");
		dotpos=value.lastIndexOf(".");
		lastpos=value.length-1;
		if (apos<1 || dotpos-apos<2 || lastpos-dotpos>4 || lastpos-dotpos<2)
		{if (alertbox) {alert(alertbox);} return false;}
		else {return true;}
	}
}

function intvalidation(entered, alertbox)
{	with(entered)
	{	checkvalue = parseInt(value);
		if (checkvalue == "e")
		{	if (alertbox)
			{	alert(alertbox);
			}
			return false;
		}
		else
		{	return true;
		}
	}
}

function matchPassword(p1,p2, alertbox, alertbox2, alertbox3)
{
	if (p1.value=='' || p2.value=='') {if (alertbox)alert(alertbox); return false;}
	else if (p1.value!=p2.value) {if (alertbox)alert(alertbox2); return false;}
	else if (p1.value.length < 6) {if (alertbox)alert(alertbox3); return false;}
	return true;
}
/*	-----------------------		*/


function CloseMe() {
	parent.window.close();
}
var pops = new Array(
	// class, width, height, toolbars, resizable, scrollbars
  new Array("w1h2","130","256","no","no","no"),
  new Array("w2h1","240","170","no","no","no"), // customer ID reminder
  new Array("w3h2","320","222","no","no","no"),
  new Array("w4h3","500","340","no","no","no"), // about us
  new Array("w4ns","462","384","no","no","no"), // NL subscription  
  new Array("w4h4","495","400","no","no","no"),
	new Array("w4h5","450","518","no","no","no"), // swimsuits
  new Array("w4h6","490","680","no","no","no"),
  new Array("w5h3","520","320","no","yes","no"),  // about
  new Array("w5h42","500","400","no","yes","yes"),
  new Array("w5h6","530","576","no","no","yes"),   // referral v2
  new Array("w6h41","650","400","no","yes","yes"), // help pages
  new Array("p6","650","400","no","yes","yes"),
  new Array("w6h42","656","546","no","yes","yes"), // x18 scratchcards
  new Array("w6h43","622","480","no","yes","yes"), // referral v1
  new Array("w6h5rf","650","500","no","no","yes"), // server config for referral
  new Array("w6h5","600","560","no","yes","yes"),
  new Array("w7h580","900","600","no","no","yes"), // cashier
  new Array("w7h31","717","360","no","yes","yes"),
  new Array("w7h32","717","360","no","no","no"),
  new Array("w7h41","740","450","no","yes","yes"),
  new Array("w7h42","708","475","no","no","yes"),
  new Array("w7h5","748","590","no","yes","yes"),
  new Array("w7h6","700","600","no","yes","yes"),
  new Array("hts76","690","580","no","no","no"),  // how to start
  new Array("w8h61","800","600","no","yes","no"),
  new Array("w8h62","800","600","no","yes","yes"), // casino games popups
  new Array("w8h63","800","646","no","yes","yes"), // x18 popups
  new Array("w8h64","800","650","no","yes","no"),
  new Array("w9h7","960","720","no","no","no"), // 3D slots for fun
  new Array("cdo","688","740","no","yes","yes"), // cashier deposit options
  new Array("w8s20","760","646","no","yes","no"),  // for  new slots Games
  new Array("w12h60","1230","650","no","yes","yes")  // for ncaa bracket
	);
// Jorge's magic:
function PopEm(){
	if (!document.getElementsByTagName) return false;
	var links = document.getElementsByTagName("a");
	for (var i=0; i < links.length; i++){
		for (var j=0;j< pops.length; j++){
			if (links[i].className.match(pops[j][0])){ 
				links[i].onclick =  function(e){
					var obj;
					if (e && e.currentTarget ){
						obj = e.currentTarget; }
					else if (e && e.target){
					obj = e.target;}
					else if (event && event.srcElement) {
						if (event.srcElement.tagName == "IMG" && event.srcElement.parentElement && event.srcElement.parentElement.tagName == "A")
							obj = event.srcElement.parentElement; 
						else	
							obj = event.srcElement;	
						}
					for (var j=0;j< pops.length; j++) {
						if (obj.className.match(pops[j][0])){
							wn='pw'+j;
							window.open(obj.href,wn,'scrollbars='+pops[j][5]+',toolbars='+pops[j][3]+',resizable='+pops[j][4]+',width='+pops[j][1]+',height='+pops[j][2]);
							return false;
						}
					}      
				}
				break;
			}
		}
	}
}

function openNewWindow(URLtoOpen, windowName, windowFeatures){ // for clicktag processing
 newWindow=window.open(URLtoOpen, windowName, windowFeatures);
}

function checkTicket()
{
	if (window.frames['TICKET'])
	{
		st.style.visibility = "visible";
	}
	else
		setTimeout("checkTicket()", 100);
}

function ShowSecureSportsMenu(){
	var ulshere=document.getElementsByTagName('ul');
	if (ulshere.length>2){
		st = ulshere[2];
		if (st.id == "sts-secure")
		{
			setTimeout("checkTicket()", 100);		
		}
	}
}


  function validateEmail(s){var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/i;if(reg.test(s) == false){return false;}return true;}
  function validateFormJoin(){
    var form = document.join;
    if(form.USERID){if(!doesExist(form.USERID.value )){alert("Please choose your username.");form.USERID.focus();return (false);}}
    if(form.PASSWORD){if(!doesExist(form.PASSWORD.value)){alert("Please choose your password.");form.PASSWORD.focus();return(false);}}
    if(doesExist(form.PASSWORD.value) && form.PASSWORD.value !== form.REENTERPASSWORD.value){alert ( "Please enter the same password twice." );form.REENTERPASSWORD.focus();return(false);}
    if(form.FIRSTNAME){if(!doesExist ( form.FIRSTNAME.value)){alert( "Please enter your first name." );form.FIRSTNAME.focus();return(false);}}
    if(form.LASTNAME){if(!doesExist ( form.LASTNAME.value)){alert( "Please enter your last name." );form.LASTNAME.focus();return(false);}}
    if(form.STREET){if(!doesExist(form.STREET.value)){alert ( "Please enter your street address." );form.STREET.focus();return(false);}}
    if(form.CITY){if(!doesExist(form.CITY.value)){alert ( "Please enter your city." );form.CITY.focus();return(false);}}
    if(form.ZIP){if(!doesExist(form.ZIP.value)){alert ( "Please enter your zip or postal code." );form.ZIP.focus();return(false);}}
    if(form.HOMEPHONENO){
			if(!doesExist(form.HOMEPHONENO.value))
				{alert("Please enter your home phone number.");form.HOMEPHONENO.focus();return(false);}
			else {
				var f = document.join.COUNTRY;
				if (f[f.selectedIndex].value.indexOf('United States') != -1 || f[f.selectedIndex].value.indexOf('Canada') != -1)
				{
					if ( form.HOMEPHONENO.value.length > 9 &&	form.HOMEPHONENO.value.match(/^(\([2-9]\d{2}\)|[2-9]\d{2})\s?-?[2-9]\d{2}-?\d{4}$/) ){}	
					else {
						alert("Please enter a valid 10-digit phone number.\r\n\r\nYour phone number must match the one in your Banking Information.\r\n\r\nExample: 1234567890 (no dashes, spaces, or symbols).")
						return false;
					}
				}
			}	
	}
    if(form.EMAIL){if(!doesExist(form.EMAIL.value)||!validateEmail(form.EMAIL.value)){alert("Please enter a valid email address.");form.EMAIL.focus();return(false);}}
    if(document.join.BIRTHMONTH.options[document.join.BIRTHMONTH.options.selectedIndex].value == '00' || document.join.BIRTHDAY.options[document.join.BIRTHDAY.options.selectedIndex].value == '00' || document.join.BIRTHYEAR.options[document.join.BIRTHYEAR.options.selectedIndex].value == '0000' )
    {alert('Please select month, day, and year of your date of birth');document.join.BIRTHMONTH.focus();return false;}
    if (document.join.COUNTRY[document.join.COUNTRY.selectedIndex].value.indexOf('United States') == 0 && document.join.STATE.options[document.join.STATE.selectedIndex].value == 'nostate'){alert('Please select your state');return false;}
    if (document.join.COUNTRY[document.join.COUNTRY.selectedIndex].value.indexOf('Canada') == 0 && document.join.STATE.options[document.join.STATE.selectedIndex].value == 'nostate'){alert('Please select your province');return false;}
    document.join.BIRTHDATE.value = document.join.BIRTHMONTH.options[document.join.BIRTHMONTH.options.selectedIndex].value+document.join.BIRTHDAY.options[document.join.BIRTHDAY.options.selectedIndex].value+document.join.BIRTHYEAR.options[document.join.BIRTHYEAR.options.selectedIndex].value; 
    makeRequest();
    return false;}


function initTabs(){
	$('.tabset').each(function() {
		var _tablinks = $(this).find('a.tab');
		_tablinks.each(function(){
			_curtab = $(this).attr('href');
			if($(_curtab).length) {
				$(_curtab).addClass('inactive').removeClass('active').css('display', 'none');
				if($(this).hasClass('active')) $(_curtab).addClass('active').css('display', 'block');
			}

			this.onclick = function(){
				_url = $(this).attr('href');
				_tablinks.each(function() {
					$($(this).attr('href')).removeClass("active").addClass("inactive").css({'display' : 'none', 'opacity' : '0'});
					$(this).removeClass('active');
				});
				$(_url).removeClass("inactive").addClass("active").css('display', 'block').animate({ opacity: "1"}, 0);;
				$(this).addClass('active');
				return false;
			}
		});
	});
}

function sharedFn(){
  $(".subnav .last, #contact-methods").click(function(){
   $("#contact-methods").slideToggle("fast");
  });
  /*   free contests */
  $(".recent-hold .more-link").click(function(){
     $(this).toggleClass("less-link");
     $(".side-box .extra").slideToggle("slow");
  });
  /*   recent winners */
  $(".recent-hold .recent-link").click(function(){
     document.location.href='/contest/winners/2010/hoops/?WT.svl=rw_r2c3_jq';
  });
  PopEm();
}

function sportsbookSpecific(){
  if(!(jQuery.support.cssFloat)){
       $(".col3 .side-box:first-child").css("margin-top","32px");
  }
}

function addOptionsToJoin(){
  $.ajax({
    url:"/j/options-countries.php",
    success:function(data){$("#COUNTRY").html(data);}
  });
  $.ajax({
    url:"/j/options-birth-year.php",
    success:function(data){$("#BIRTHYEAR").html(data);}
  });
}