   /*
  *  Additional jQuery code require for the Barcode Letter-Size plugin to run
  *
  *   - HP Gong
  */

(function( $ ) {
// Print Barcode
$(document).ready(function(){
function printData(){
   var divToPrint=document.getElementById("printBar");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}

$('.btn_blues').on('click',function(){
printData();
})

});
})( jQuery );
