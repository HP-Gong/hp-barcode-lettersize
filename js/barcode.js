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

})( jQuery );
