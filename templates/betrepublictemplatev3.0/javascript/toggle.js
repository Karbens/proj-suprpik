// JavaScript Document

$(document).ready(function() {
 // hides the contact as soon as the DOM is ready
 // (a little sooner than page load)
  $('#1').show();
  $('#2').hide();
  $('#3').hide();
  $('#4').hide();
  $('#5').hide();
  $('#6').hide();
 
 
  // toggles the contact on hovering the noted link  
  $('a#sports').hover(function() {
	$('#1').hide();
  $('#2').show();
  $('#3').hide();
  $('#4').hide();
  $('#5').hide();
  $('#6').hide();
 
    return false;
  });
  
    $('a#activities').hover(function() {
		$('#1').hide();
  $('#2').hide();
  $('#3').show();
  $('#4').hide();
  $('#5').hide();
  $('#6').hide();
 
    return false;
  });


  $('a#games').hover(function() {
		$('#1').hide();
  $('#2').hide();
  $('#3').hide();
  $('#4').show();
  $('#5').hide();
  $('#6').hide();
 
    return false;
  });  

});