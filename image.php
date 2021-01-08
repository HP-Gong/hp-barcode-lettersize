<?php

require 'bar/barcode.php';

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
	if (isset($_POST['s']) && isset($_POST['d'])) {
		$generator = new barcode_generator();
		$format = (isset($_POST['f']) ? $_POST['f'] : 'png');
		$generator->output_image($format, $_POST['s'], $_POST['d'], $_POST);
		exit(0);
	}
	if (isset($_GET['s']) && isset($_GET['d'])) {
		$generator = new barcode_generator();
		$format = (isset($_GET['f']) ? $_GET['f'] : 'png');
		$generator->output_image($format, $_GET['s'], $_GET['d'], $_GET);
		exit(0);
	}
}

?>