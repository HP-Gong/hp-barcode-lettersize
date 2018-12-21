     /*
  *  Additional jQuery code require for the Barcode Letter-Size plugin to run
  *
  *   - HP Gong
  */

(function( $ ) {
// Display codetype option in div using show() and hide() function,
// and also display additional messages and max and min for the input boxes
$(document).ready(function(){
$("#symbology").change(function(){
$(this).find("option:selected").each(function(){
$('.message0').hide();
if($(this).attr("value")=="upc-a"){
$(".box").not(".upc-a").hide();
$(".upc-a").show();
$('.message0').show();
}
else if($(this).attr("value")=="ean-13"){
$(".box").not(".ean-13").hide();
$(".ean-13").show();
$('.message0').show();
}
else if($(this).attr("value")=="ean-8"){
$(".box").not(".ean-8").hide();
$(".ean-8").show();
$('.message0').show();
}
else if($(this).attr("value")=="code-39"){
$(".box").not(".code-39").hide();
$(".code-39").show();
$('.message0').show();
}
else if($(this).attr("value")=="code-93"){
$(".box").not(".code-93").hide();
$(".code-93").show();
$('.message0').show();
}
else if($(this).attr("value")=="ean-128"){
$(".box").not(".ean-128").hide();
$(".ean-128").show();
$('.message0').show();
}
else if($(this).attr("value")=="code-128"){
$(".box").not(".code-128").hide();
$(".code-128").show();
$('.message0').show();
}
else if($(this).attr("value")=="itf"){
$(".box").not(".itf").hide();
$(".itf").show();
$('.message0').show();
}
else if($(this).attr("value")=="qr"){
$(".box").not(".qr").hide();
$(".qr").show();
$('.message0').show();
}
else if($(this).attr("value")=="dmtx"){
$(".box").not(".dmtx").hide();
$(".dmtx").show();
$('.message0').show();
}
else{
$(".box").hide();
}
});
}).change();
});

});
})( jQuery );
