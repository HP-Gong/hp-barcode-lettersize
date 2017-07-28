   /*
  *  Additional jQuery code require for the Barcode Letter-Size plugin to run
  *
  *   - HP Gong 
  */
 
(function( $ ) {
// Display codetype option in div using show() and hide() function,
// and also display additional messages for the length of the title
$(document).ready(function(){
$("#codetype").change(function(){
$(this).find("option:selected").each(function(){
$('.message0').hide();
if($(this).attr("value")=="code128a"){
$(".box").not(".code128a").hide();
$(".code128a").show();
$('.message0').show();
}
else if($(this).attr("value")=="code128b"){
$(".box").not(".code128b").hide();
$(".code128b").show();
$('.message0').show();
}
else if($(this).attr("value")=="code128c"){
$(".box").not(".code128c").hide();
$(".code128c").show();
$('.message0').show();
}
else if($(this).attr("value")=="code39"){
$(".box").not(".code39").hide();
$(".code39").show();
$('.message0').show();
}
else if($(this).attr("value")=="code25"){
$(".box").not(".code25").hide();
$(".code25").show();
$('.message0').show();
}
else if($(this).attr("value")=="codabar"){
$(".box").not(".codabar").hide();
$(".codabar").show();
$('.message0').show();
}
else{
$(".box").hide();
}
});
}).change();
});

$(function(){
$('input[id=length]').keypress(function(event){
event.preventDefault();
});
});	

$(function(){
$('#printOut').click(function(e){
e.preventDefault();
var w = window.open();
var printOne = $('.print_barcodes').html();
w.document.write('<html><head><title>Generated Barcordes Lettersize</title></head><body><style type="text/css">@media print {@page { size:8.5in 11in; margin:.2in .3in .2in .3in; size: portrait; mso-header-margin:.1in; mso-footer-margin:.1in; mso-paper-source:0; orphans:0; widows:0;}</style><p style="-o-box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;margin: 0px 0px 0px 5px;">' + printOne + '</p></body></html>');
w.document.close();
return false;
});
});	

// This function will refresh the page back to the first page.
$(function(){	
$('#back').click(function() {
location.reload();
});	
});	
})( jQuery );
