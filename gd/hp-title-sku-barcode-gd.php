<?php
/*
 *  Author	David S. Tufts
 *  Company	davidscotttufts.com
 *	  
 *  Date:	05/25/2003
 *  Usage:	<img src="/barcode.php?text=testing" alt="testing" />
 */
 
/* 
 *  Added new code and modify the current code for the Barcode Letter-Size plugin 
 *  This is where the barcodes are created. Once the products are selected from the wordpress database,
 *  it will created the barcode from the selected codetype and also add the title & sku onto the barcodes.
 *
 *   - HP Gong 
 */

if(! defined('ABSPATH')){exit;} 
 
function hp_barcode_img_ts($title, $sku) {
	// Get pararameters that are passed in through $_GET or set to the default value
	$orientation = (isset($_GET["orientation"])?$_GET["orientation"]:"horizontal");
	$code_type = (isset($_POST["codetype"])?$_POST["codetype"]:""); 
	$c_string= "";
	$sku = get_post_meta(get_the_ID(), '_sku', true);	
	
	if ( strtolower($code_type) == "code128a" ) { 	// Code128a 
		$text = (isset($_GET["text"])?$_GET["text"]:"11111111111"); // length of the barcode
	    $size = (isset($_GET["size"])?$_GET["size"]:"35"); // height of the barcode
		$chksum = 103; // checksum 103
		$text = strtoupper($text); // Code 128A doesn't support lower case
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$b_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\""=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","nul"=>"111422","soh"=>"121124","stx"=>"121421","etx"=>"141122","eot"=>"141221","eno"=>"112214","ack"=>"112412","bel"=>"122114","bs"=>"122411","ht"=>"142112","lf"=>"142211","vt"=>"241211","ff"=>"221114","cr"=>"413111","s0"=>"241112","s1"=>"134111","dle"=>"111242","dc1"=>"121142","dc2"=>"121241","dc3"=>"114212","dc4"=>"124112","nak"=>"124211","syn"=>"411212","etb"=>"421112","can"=>"421211","em"=>"212141","sub"=>"214121","esc"=>"412121","fs"=>"111143","gs"=>"111341","rs"=>"131141","us"=>"114113","fnc 3"=>"114311","fnc 2"=>"411113","Shift"=>"411311","code C"=>"113141","code B"=>"114131","fnc 4"=>"311141","fnc 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
		
		$c_keys = array_keys($b_array);
		$c_values = array_flip($c_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$c_string.= $b_array[$activeKey];
			$chksum=($chksum + ($c_values[$activeKey] * $X));
		}
		$c_string.= $b_array[$c_keys[($chksum - (intval($chksum / 102) * 102))]];

		$c_string= "211412" . $c_string. "2331112";
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 80
		$y1 = 3; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 80	
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 18, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img;
		
	} elseif( strtolower($code_type) == "code128b" ) { 	// Code128b 
		$text = (isset($_GET["text"])?$_GET["text"]:"11111111111"); // length of the barcode
	    $size = (isset($_GET["size"])?$_GET["size"]:"35"); // height of the barcode
		$chksum = 104; // checksum 104
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$b_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\""=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","del"=>"114113","fnc 3"=>"114311","fnc 2"=>"411113","Shift"=>"411311","code C"=>"113141","fnc 4"=>"114131","code A"=>"311141","fnc 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
		$c_keys = array_keys($b_array);
		$c_values = array_flip($c_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$c_string.= $b_array[$activeKey];
			$chksum=($chksum + ($c_values[$activeKey] * $X));
		}
		$c_string.= $b_array[$c_keys[($chksum - (intval($chksum / 103) * 103))]];

		$c_string= "211214" . $c_string. "2331112"; 
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 80
		$y1 = 3; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 80	
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 18, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img; 
		
	} elseif( strtolower($code_type) == "code128c" ) { 	// Code128c
		$text = (isset($_GET["text"])?$_GET["text"]:"11111111111"); // length of the barcode
	    $size = (isset($_GET["size"])?$_GET["size"]:"35"); // height of the barcode
		$chksum = 105; // checksum 105
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$b_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\""=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","del"=>"114113","fnc 3"=>"114311","fnc 2"=>"411113","Shift"=>"411311","code C"=>"113141","fnc 4"=>"114131","code A"=>"311141","fnc 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
		$c_keys = array_keys($b_array);
		$c_values = array_flip($c_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$c_string.= $b_array[$activeKey];
			$chksum=($chksum + ($c_values[$activeKey] * $X));
		}
		$c_string.= $b_array[$c_keys[($chksum - (intval($chksum / 104) * 104))]];

		$c_string= "211214" . $c_string. "2331112";
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 80
		$y1 = 3; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 80	
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 18, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img; 
		
	} elseif ( strtolower($code_type) == "code39" ) { 	// Code39 
		 $text = (isset($_GET["text"])?$_GET["text"]:"11111111"); // length of the barcode
	     $size = (isset($_GET["size"])?$_GET["size"]:"40"); // height of the barcode
	     $b_array1 = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","x","y","Z","-","."," ","?","/","+","%","*");
		 $b_array2 = array("111331311","311311113","113311113","313311111","111331113","311331111","113331111","111311313","311311311","113311311","311113113","311311113","113113113","313113111","111133113","311133111","113133111","111113313","311113311","113113311","111133311","311111133","113111133","313111131","111131133","311131131","111111333","311111331","113111331","111131331","331111113","133111113","333111111","131131113","331131111","131111313","331131111","133131111","131111313","331111311","133111311","131313111","131311131","131113131","111313131","131131311");

		// Convert to uppercase
		$upper_text = strtoupper($text);

		for ( $x = 1; $x<=strlen($upper_text); $x++ ) {
			for ( $y = 0; $y<count($b_array1); $y++ ) {
				if ( substr($upper_text, ($x-1), 1) == $b_array1[$y] )
					$c_string.= $b_array2[$y] . "1";
			}
		}
         
		$c_string= "1311313111" . $c_string. "131131311";
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 82
		$y1 = 2; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 82		
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 15, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img; 

	} elseif ( strtolower($code_type) == "code25" ) { 	// Code25 
		$text = (isset($_GET["text"])?$_GET["text"]:"1111111111111111"); // length of the barcode
	    $size = (isset($_GET["size"])?$_GET["size"]:"40"); // height of the barcode
		$b_array1 = array("1","2","3","4","5","6","7","8","9","0");
		$b_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

		for ( $x = 1; $x <= strlen($text); $x++ ) {
			for ( $y = 0; $y < count($b_array1); $y++ ) {
				if ( substr($text, ($x-1), 1) == $b_array1[$y] )
					$temp[$x] = $b_array2[$y];
			}
		}

		for ( $x=1; $x<=strlen($text); $x+=2 ) {
			if ( isset($temp[$x]) && isset($temp[($x + 1)]) ) {
				$temp1 = explode( "-", $temp[$x] );
				$temp2 = explode( "-", $temp[($x + 1)] );
				for ( $y = 0; $y < count($temp1); $y++ )
					$c_string.= $temp1[$y] . $temp2[$y];
			}
		}

		$c_string= "1111" . $c_string. "311";
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 82
		$y1 = 2; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 82		
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 15, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img; 
		
	} elseif ( strtolower($code_type) == "codabar" ) { 	// Codabar
		$text = (isset($_GET["text"])?$_GET["text"]:"111111111111"); // length of the barcode
	    $size = (isset($_GET["size"])?$_GET["size"]:"40"); // height of the barcode
		$b_array1 = array("0","1","2","3","4","5","6","7","8","9","-","$",":","/",".","+","A","B","C","D");
		$b_array2 = array("11111221","11112211","11121121","22111111","11211211","21111211","12111121","12112111","12211111","21121111","11122111","11221111","21112121","21211121","21212111","11222221","11221211","12121121","11121221","11122211");
		// Convert to uppercase
		$upper_text = strtoupper($text);

		for ( $x = 1; $x<=strlen($upper_text); $x++ ) {
			for ( $y = 0; $y<count($b_array1); $y++ ) {
				if ( substr($upper_text, ($x-1), 1) == $b_array1[$y] )
					$c_string.= $b_array2[$y] . "1";
			}
		}
		$c_string= "11112211" . $c_string. "11221211";
		
		$c_length = 15;
		for ( $i=1; $i <= strlen($c_string); $i++ )
		$c_length = $c_length + (integer)(substr($c_string,($i-1),1));
		
		if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $c_length;
		$img_height = $size;
		} else {
		$img_width = $size;
		$img_height = $c_length;
		}
		
		$bars = imagecreate($img_width, $img_height);
		$black = imagecolorallocate ($bars, 0, 0, 0);
		$white = imagecolorallocate ($bars, 255, 255, 255);
		
		imagefill( $bars, 0, 0, $white );
		
		$location = 2;
		for ( $position = 1 ; $position <= strlen($c_string); $position++ ) {
		$cur_size = $location + ( substr($c_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
		imagefilledrectangle( $bars, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
		imagefilledrectangle( $bars, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
		}
		$bar_img = imagecreate(($cur_size+2) < 0 ? 0 : ($cur_size+2), 70);
		$black = imagecolorallocate ($bar_img, 0, 0, 0);
		$white = imagecolorallocate ($bar_img, 255, 255, 255);
		
		$font = 2;	
		
		$xMax1 = 80;
		$xMin1 = 80;  // x Location of imagestring 82
		$y1 = 2; // y Location of imagestring
		$txtW1 = imagefontwidth( $font ) * strlen( $title );
		$xL1 = ( $xMax1 - $xMin1 - $txtW1 ) / 2 + $xMin1 + $font;
		
		$xMax2 = 80;
		$xMin2 = 80;// x Location of imagestring 82		
		$y2 = 55; // y Location of imagestring
		$txtW2 = imagefontwidth( $font ) * strlen( $sku );
		$xL2 = ( $xMax2 - $xMin2 - $txtW2 ) / 2 + $xMin2 + $font;
		
		imagefill( $bar_img, 0, 0, $white );
		imagecopyresized($bar_img, $bars, 0, 15, 0, 0, $cur_size, $img_height, $cur_size, $img_height);	 
		imagestring($bar_img, $font, $xL1, $y1, $title, $black); 
		imagestring($bar_img, $font, $xL2, $y2, $sku, $black); 
		
		return $bar_img; 
	}
}
?>
