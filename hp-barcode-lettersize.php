<?php
/**
 * Barcode Letter-Size
 *
 * Plugin Name: Barcode Letter-Size
 * Plugin URI: https://wordpress.org/plugins/barcode-lettersize
 * Description: Creating and Printing Barcodes on Letter-Size Papers.
 * Version: 1.1.2
 * Author: H.P. Gong
 * Author URI: https://github.com/hp-gong/
 * GitHub Plugin URI: https://github.com/hp-gong/hp-barcode-lettersize
 * GitHub Branch: master
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 */

// Exit if accessed directly.
if(! defined('ABSPATH')){exit;}

// Define urls
define('hp_barcode_lettersize_url_p', plugin_dir_url( __FILE__ ));
//define('hp_barcode_lettersize_url_i', includes_url( __FILE__ ));

// Check is HP_Barcode exists
if(!class_exists('HP_Barcode')){

// Class HP_Barcode
  class HP_Barcode{

	 // Function __construct
   public function __construct(){
	  add_action('admin_menu', array($this, 'add_admin_menu'));
	  add_action('admin_init', array($this, 'create_barcode_scripts'));
	  add_action('init', array($this, 'validate_form'));
	  add_action('init', array($this, 'check_if_woo_install'));
	  add_action('init', array($this, 'check_versions'));
	  }

		// Activation Function & installed woo_sku tables
	   public static function activate_hp_barcode_lettersize(){
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name1 = $wpdb->prefix . 'woo_bar1';
		$table_name2 = $wpdb->prefix . 'woo_bar2';
		$table_name3 = $wpdb->prefix . 'woo_bar3';

		$sql1 = "CREATE TABLE $table_name1 (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`img_base64` VARCHAR(800) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		$sql2 = "CREATE TABLE $table_name2 (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`img_base64` VARCHAR(800) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		$sql3 = "CREATE TABLE $table_name3 (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`img_base64` VARCHAR(800) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);
		}

		// Deactivation Function
	   public static function deactivate_hp_barcode_lettersize(){
		global $wpdb;
		}

		// Uninstall Function & Remove woo_bar tables from the databases
	   public static function uninstall_hp_barcode_lettersize(){
		global $wpdb;
		$woo_bar1 = $wpdb->prefix."woo_bar1";
		$woo_bar2 = $wpdb->prefix."woo_bar2";
		$woo_bar3 = $wpdb->prefix."woo_bar3";
		$sql1 = "DROP TABLE IF EXISTS $woo_bar1;";
		$sql2 = "DROP TABLE IF EXISTS $woo_bar2;";
		$sql3 = "DROP TABLE IF EXISTS $woo_bar3;";
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$wpdb->query($sql3);
		}

	   // Check if WooCommerce plugin is install and activated
	   // in order for Barcode Letter-Size plugin to run
	   public static function check_if_woo_install(){
	    if (! class_exists('WooCommerce')){
	    $url = admin_url('/plugins.php');
	    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    deactivate_plugins( plugin_basename( __FILE__ ));
	    wp_die( __('Barcode Letter-Size requires WooCommerce to run. <br>Please install WooCommerce and activate before attempting to activate again.<br><a href="'.$url.'">Return to the Plugins section</a>'));
	    }
      }

       // Check if WooCommerce plugin has the current version and
	     // activated in order for Barcode Letter-Size plugin to run
	   public static function check_versions(){
	    global $woocommerce;
	    if (version_compare($woocommerce->version, '3.1.1', '<')){
	    $url = admin_url('/plugins.php');
	    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    deactivate_plugins( plugin_basename( __FILE__ ));
	    wp_die( __('Barcode Letter-Size is disabled.<br>Barcode Letter-Size requires a minimum of WooCommerce v3.1.1.<br><a href="'.$url.'">Return to the Plugins section</a>'));
	    }
	    }

	   // Add Menu Button/Menu Page & Submenu Buttons/Submenu Pages
	   public static function add_admin_menu(){
		add_menu_page('Create Barcodes', 'Create Barcodes', 'administrator', 'hp_ts_barcode_products', array($this, 'plugin_settings'), hp_barcode_lettersize_url_p . 'img/icon.png', 59);
		add_submenu_page('hp_ts_barcode_products', 'Title & Sku', 'Title & Sku', 'manage_options', 'hp_ts_barcode_products', 'hp_ts_barcode_products', 'hp_ts_barcode_products1');
		add_submenu_page('hp_ts_barcode_products', 'Display Title & Sku', 'Display Title & Sku', 'manage_options', 'hp_ts_display_barcode', 'hp_ts_display_barcode', 'hp_ts_barcode_products2');
		add_submenu_page('hp_ts_barcode_products', 'Title', 'Title', 'manage_options', 'hp_t_barcode_products', 'hp_t_barcode_products', 'hp_ts_barcode_products3');
		add_submenu_page('hp_ts_barcode_products', 'Display Title', 'Display Title', 'manage_options', 'hp_t_display_barcode', 'hp_t_display_barcode', 'hp_ts_barcode_products4');
		add_submenu_page('hp_ts_barcode_products', 'Sku', 'Sku', 'manage_options', 'hp_s_barcode_products', 'hp_s_barcode_products', 'hp_ts_barcode_products5');
		add_submenu_page('hp_ts_barcode_products', 'Display Sku', 'Display Sku', 'manage_options', 'hp_s_display_barcode', 'hp_s_display_barcode', 'hp_ts_barcode_products6');
		 }

		 // Only Administrator have permissions to access this page
	   public static function plugin_settings() {
	    if (!current_user_can('administrator')){
	    wp_die('You do not have sufficient permissions to access this page.');
	    }
	    }

		 // Verify Nonce Form
	   function validate_form() {
		if(isset($_POST['btn_blue'])){
		if (!isset($_POST['barcode_display_products_nonce_1']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_1'], 'barcode_display_products_n2')){
		wp_die('You do not have access to this page.');
		} else{
		$products = sanitize_text_field(trim($_POST['products']));
		$len = sanitize_text_field(trim($_POST['len']));
		}
		if (!isset($_POST['barcode_display_products_nonce_2']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_2'], 'barcode_display_products_n1')){
		wp_die('You do not have access to this page.');
		} else{
		$products = sanitize_text_field(trim($_POST['products']));
		$len = sanitize_text_field(trim($_POST['len']));
		}
		}
	    }


	   // Register the jQuery & CSS scripts and link the files
	   public static function create_barcode_scripts(){
		// jQuery
		wp_enqueue_script('jquery');
		// jQuery scripts for barcode
		wp_register_script('barcode', hp_barcode_lettersize_url_p .'js/barcode.js', array('jquery'));
		wp_register_script('bundle', hp_barcode_lettersize_url_p .'js/bundle.js', array('jquery'));
		wp_register_script('valida.2.1.7', hp_barcode_lettersize_url_p .'js/valida.2.1.7.js', array('jquery'));
		wp_enqueue_script('barcode');
		wp_enqueue_script('bundle');
		wp_enqueue_script('valida.2.1.7');
	    // CSS scripts for barcode
		wp_register_style('barcode', hp_barcode_lettersize_url_p . 'css/barcode.css');;
		wp_enqueue_style('barcode');
	    }
        }

	  // This function will create the Title & Sku barcode.
	  function hp_ts_barcode_products(){

	  echo '<h2>Generate Barcodes: Title & Sku</h2>';

	  if ($_SERVER['REQUEST_METHOD'] == "POST"){

	  $_POST = filter_input(INPUT_POST, FILTER_SANITIZE_STRING);
	  $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	  $products = filter_input(INPUT_POST, 'products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	  if (array_key_exists('products', $products)) {
	  $products = isset($_POST['products']) ? $_POST['products'] : '';
	  }

	  $ids = implode(',', $products);

	  if(!preg_match('/^[,0-9]+$/', $ids)) die('Fatal Error !');

	  $query = new WP_Query(Array('query'=>'id='.$ids, 'post_type'=>array('product'), 'posts_per_page' => -1));

	  if($query->have_posts()){

	  while ($query->have_posts()){

	  $query->the_post();

	  require_once 'gd/hp-title-sku-barcode-gd.php';

	  if($_POST){
	  $len = sanitize_text_field(trim($_POST['len']));

	  $len = intval( $_POST['len']);
	  if(!$len) {$len = '';}

	  if ($len){
	  $string = mb_strimwidth(get_the_title(), 0, intval($len), ' ');
	  }

	  $img = hp_barcode_img_ts($string, get_the_ID());

	  ob_start();
	  imagepng($img);
	  imagedestroy($img);
	  $img_output = ob_get_clean();
	  $img_base64 = 'data:image/png;base64,' .base64_encode($img_output);

	  global $wpdb;
	  $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}woo_bar1 (img_base64) VALUES (%s)", $img_base64));
	  }
	  }
	  }else {
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }
	  wp_redirect(admin_url('admin.php?page=hp_ts_display_barcode'));
	  exit;
	  }

	  $args = array('post_type' => array('product'), 'posts_per_page' => -1);
	  $posts = get_posts( $args );

	  if(is_array($posts) && count($posts) == 0){
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }

	  echo '<h3>All Products</h3>';
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Products:<span style="margin: 0px 0px 0px 6px;">'.esc_html($post = count($posts)).'</span></strong> </p>';
	  echo '<div style="width: 90%; height: 100%; border: 1px dashed #8e24aa; padding: 4px 4px 4px 4px;">';
	  echo "<ol id='ol-tag' type='decimal'>";

	  foreach($posts as $post) {
	  echo '<li>'.sanitize_text_field($post->post_title).'</li>';
	  }

	  echo '</ol>';
	  echo '</div>';
	  echo '<form id="valida" name="valida" class="valida" action="" method="POST">';
	  wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
	  echo '<script type="text/javascript">$(document).ready(function() {$("#valida").valida();});</script><br>';
	  echo '<fieldset>';
	  echo '<div class="codetype">';
	  echo '<label for="codetype">Select CodeType:</label>';
	  echo '<select name="codetype" required id="codetype" data-required="Please Select Codetype." require class="at-required">';
	  echo '<option selected value=""></option>';

	  $c1 = array("code128a","code128b","code128c","code39","code25","codabar");
	  foreach($c1 as $c){
	  echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	  }

	  echo '</select>';
	  echo '</div>';

	  echo '<div class="code128a code128b code128c code39 code25 codabar box"><br>';

	  echo '<div class="len1">';
	  echo '<script type="text/javascript">function updateTextInput1(val) {document.getElementById("length").value=val;}</script>';
	  echo '<input type="range" id="len" name="len" style="width: 400px;" max="31" min="2" step="1" onchange="updateTextInput1(this.value);"><br>';
	  echo '<label for="len1">Length of the Title on the Barcode: <input type="text" required id="length" style="width: 35px;" class="at-required"></label>';
	  echo '<div class="message0" style="color: red;">Enter the product title length, the number must be between 2 and 31.</div>';
	  echo '</div>';

	  echo '</div>';
	  echo '</fieldset><br>';

	  foreach($posts as $post) {
	  echo '<input type="hidden" id="product-'.sanitize_text_field($post->ID).'" name="products[]" class="products" value="'.sanitize_text_field($post->ID).'" checked="checked" />';
	  }

	  wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	  echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">';
	  echo '</form>';
	  }

	  // This function will display the Title & Sku barcode
	  function hp_ts_display_barcode(){             
	  
          echo '<br>';
	  echo '<h2>Display Title & Sku</h2>';
	  global $wpdb;
	  $result1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar1");
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Barcode:<span style="margin: 0px 0px 0px 6px;">'.esc_js(esc_html(count($result1))).'</span></strong> </p>';
	  echo '<br>';
	  echo '<form method="POST" action="">';
          wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  if ($_SERVER['REQUEST_METHOD']=="POST" && $_POST['remove_bar']) {
	  if ($_GET['bar']) $_POST['bar'][] = $_GET['bar'];
	  $count = 0;
	  if (is_array($_POST['bar'])) {
	  foreach ($_POST['bar'] as $id) { 
	  $wpdb->query("DELETE FROM {$wpdb->prefix}woo_bar1 WHERE id='".$id."' LIMIT 1"); 
	  $count++; 
	  }
	  }
	  }
	  echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: white;" id="printBar">
	  <tbody>';
	  global $wpdb;
	  $row_count=0;
	  $col_count=0;
	  $result2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar1");
	  foreach($result2 as $row2){
	  if($row_count%4==0){echo '<tr>';
	  $col_count=1;
	  }
	  echo '
	  <td style="border: 1px solid black;">
	  <input type="checkbox" name="bar[]" value="'.sanitize_text_field($row2->id).'">
	  <div class="center">
	  <img src="'.sanitize_text_field($row2->img_base64).'" alt="'.sanitize_text_field($row2->id).'" style="width: 160px; height: 70px; background-color: white; border: 0px solid #021a40; padding: 5px 5px 5px 5px;">
	  </div></td>';
	  if($col_count==4){
	  echo "</tr>";
	  }
	  $row_count++;
	  $col_count++;
	  }
	  echo '</tbody></table>'; 
	  echo '<br>';
      wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
      echo '<input type="hidden" name="remove_bar" value="1" />';
	  echo '<input type="button" class="btn_blues" name="btn_blues" value="Print Out">'; 
      echo '<script type="text/javascript">function toggle(source) {checkboxes = document.getElementsByName("bar[]"); for(var i=0, n=checkboxes.length;i<n;i++) {checkboxes[i].checked = source.checked; }}</script>';
	  echo '<input type="submit" class="btn_reds" name="btn_reds" value="Remove"><input type="checkbox" onClick="toggle(this)" />Select All Checkboxes<br/>';
	  echo '</form>';
        }

	  // This function will create the Title barcode.
	  function hp_t_barcode_products(){

	  echo '<h2>Generate Barcodes: Title</h2>';

	  if ($_SERVER['REQUEST_METHOD'] == "POST"){

	  $_POST = filter_input(INPUT_POST, FILTER_SANITIZE_STRING);
	  $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	  $products = filter_input(INPUT_POST, 'products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	  if (array_key_exists('products', $products)) {
	  $products = isset($_POST['products']) ? $_POST['products'] : '';
	  }

	  $ids = implode(',', $products);

	  if(!preg_match('/^[,0-9]+$/', $ids)) die('Fatal Error !');

	  $query = new WP_Query(Array('query'=>'id='.$ids, 'post_type'=>array('product'), 'posts_per_page' => -1));

	  if($query->have_posts()){

	  while ($query->have_posts()){

	  $query->the_post();

	  require_once 'gd/hp-title-barcode-gd.php';

	  if($_POST){
	  $len = sanitize_text_field(trim($_POST['len']));

	  $len = intval( $_POST['len']);
	  if(!$len) {$len = '';}

	  if ($len){
	  $string = mb_strimwidth(get_the_title(), 0, intval($len), ' ');
	  }

	  $img = hp_barcode_img_t($string, get_the_ID());

	  ob_start();
	  imagepng($img);
	  imagedestroy($img);
	  $img_output = ob_get_clean();
	  $img_base64 = 'data:image/png;base64,' .base64_encode($img_output);

	  global $wpdb;
	  $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}woo_bar2 (img_base64) VALUES (%s)", $img_base64));
	  }
	  }
	  }else {
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }
	  wp_redirect(admin_url('admin.php?page=hp_t_display_barcode'));
	  exit;
	  }

	  $args = array('post_type' => array('product'), 'posts_per_page' => -1);
	  $posts = get_posts( $args );

	  if(is_array($posts) && count($posts) == 0){
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }

	  echo '<h3>All Products</h3>';
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Products:<span style="margin: 0px 0px 0px 6px;">'.esc_html($post = count($posts)).'</span></strong> </p>';
	  echo '<div style="width: 90%; height: 100%; border: 1px dashed #8e24aa; padding: 4px 4px 4px 4px;">';
	  echo "<ol id='ol-tag' type='decimal'>";

	  foreach($posts as $post) {
	  echo '<li>'.sanitize_text_field($post->post_title).'</li>';
	  }

	  echo '</ol>';
	  echo '</div>';
	  echo '<form id="valida" name="valida" class="valida" action="" method="POST">';
	  wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
	  echo '<script type="text/javascript">$(document).ready(function() {$("#valida").valida();});</script><br>';
	  echo '<fieldset>';
	  echo '<div class="codetype">';
	  echo '<label for="codetype">Select CodeType:</label>';
	  echo '<select name="codetype" required id="codetype" data-required="Please Select Codetype." require class="at-required">';
	  echo '<option selected value=""></option>';

	  $c1 = array("code128a","code128b","code128c","code39","code25","codabar");
	  foreach($c1 as $c){
	  echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	  }

	  echo '</select>';
	  echo '</div>';

	  echo '<div class="code128a code128b code128c code39 code25 codabar box"><br>';

	  echo '<div class="len1">';
	  echo '<script type="text/javascript">function updateTextInput1(val) {document.getElementById("length").value=val;}</script>';
	  echo '<input type="range" id="len" name="len" style="width: 400px;" max="31" min="2" step="1" onchange="updateTextInput1(this.value);"><br>';
	  echo '<label for="len1">Length of the Title on the Barcode: <input type="text" required id="length" style="width: 35px;" class="at-required"></label>';
	  echo '<div class="message0" style="color: red;">Enter the product title length, the number must be between 2 and 31.</div>';
	  echo '</div>';

	  echo '</div>';
	  echo '</fieldset><br>';

	  foreach($posts as $post) {
	  echo '<input type="hidden" id="product-'.sanitize_text_field($post->ID).'" name="products[]" class="products" value='.sanitize_text_field($post->ID).' checked="checked" />';
	  }

      wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	  echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">';
	  echo '</form>';
	  }

	  // This function will display the Title & Sku barcode
	  function hp_t_display_barcode(){
		  
	  echo '<br>';
	  echo '<h2>Display Title</h2>';
	  global $wpdb;
	  $result1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar2");
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Barcode:<span style="margin: 0px 0px 0px 6px;">'.esc_js(esc_html(count($result1))).'</span></strong> </p>';
	  echo '<br>';
	  echo '<form method="POST" action="">';
	  wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  if ($_SERVER['REQUEST_METHOD']=="POST" && $_POST['remove_bar']) {
	  if ($_GET['bar']) $_POST['bar'][] = $_GET['bar'];
	  $count = 0;
	  if (is_array($_POST['bar'])) {
	  foreach ($_POST['bar'] as $id) { 
	  $wpdb->query("DELETE FROM {$wpdb->prefix}woo_bar2 WHERE id='".$id."' LIMIT 1"); 
	  $count++; 
	  }
	  }
	  }
	  echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: white;" id="printBar">
	  <tbody>';
	  global $wpdb;
	  $row_count=0;
	  $col_count=0;
	  $result2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar2");
	  foreach($result2 as $row2){
	  if($row_count%4==0){echo '<tr>';
	  $col_count=1;
	  }
	  echo '
	  <td style="border: 1px solid black;">
	  <input type="checkbox" name="bar[]" value="'.sanitize_text_field($row2->id).'">
	  <div class="center">
	  <img src="'.sanitize_text_field($row2->img_base64).'" alt="'.sanitize_text_field($row2->id).'" style="width: 160px; height: 70px; background-color: white; border: 0px solid #021a40; padding: 5px 5px 5px 5px;">
	  </div></td>';
	  if($col_count==4){
	  echo "</tr>";
	  }
	  $row_count++;
	  $col_count++;
	  }
	  echo '</tbody></table>'; 
	  echo '<br>';
	  wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
      echo '<input type="hidden" name="remove_bar" value="1" />';
	  echo '<input type="button" class="btn_blues" name="btn_blues" value="Print Out">'; 
      echo '<script type="text/javascript">function toggle(source) {checkboxes = document.getElementsByName("bar[]"); for(var i=0, n=checkboxes.length;i<n;i++) {checkboxes[i].checked = source.checked; }}</script>';
	  echo '<input type="submit" class="btn_reds" name="btn_reds" value="Remove"><input type="checkbox" onClick="toggle(this)" />Select All Checkboxes<br/>';
	  echo '</form>';
	  }

	  // This function will create the Sku barcode.
	  function hp_s_barcode_products(){

	  echo '<h2>Generate Barcodes: Sku</h2>';

	  if ($_SERVER['REQUEST_METHOD'] == "POST"){

	  $_POST = filter_input(INPUT_POST, FILTER_SANITIZE_STRING);
	  $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	  $products = filter_input(INPUT_POST, 'products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	  if (array_key_exists('products', $products)) {
	  $products = isset($_POST['products']) ? $_POST['products'] : '';
	  }

	  $ids = implode(',', $products);

	  if(!preg_match('/^[,0-9]+$/', $ids)) die('Fatal Error !');

	  $query = new WP_Query(Array('query'=>'id='.$ids, 'post_type'=>array('product'), 'posts_per_page' => -1));

	  if($query->have_posts()){

	  while ($query->have_posts()){

	  $query->the_post();

	  require_once 'gd/hp-sku-barcode-gd.php';

	  if($_POST){
	  $len = sanitize_text_field(trim($_POST['len']));

	  $len = intval( $_POST['len']);
	  if(!$len) {$len = '';}

	  if ($len){
	  $string = mb_strimwidth(get_the_title(), 0, intval($len), ' ');
	  }

	  $sku = get_post_meta(get_the_ID(), '_sku', true);
	  $img = hp_barcode_img_s($sku, get_the_ID());

	  ob_start();
	  imagepng($img);
	  imagedestroy($img);
	  $img_output = ob_get_clean();
	  $img_base64 = 'data:image/png;base64,' .base64_encode($img_output);

	  global $wpdb;
	  $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}woo_bar3 (img_base64) VALUES (%s)", $img_base64));
	  }
	  }
	  }else {
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }
	  wp_redirect(admin_url('admin.php?page=hp_s_display_barcode'));
	  exit;
	  }

	  $args = array('post_type' => array('product'), 'posts_per_page' => -1);
	  $posts = get_posts( $args );

	  if(is_array($posts) && count($posts) == 0){
	  echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	  }

	  echo '<h3>All Products</h3>';
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Products:<span style="margin: 0px 0px 0px 6px;">'.esc_html($post = count($posts)).'</span></strong> </p>';
	  echo '<div style="width: 90%; height: 100%; border: 1px dashed #8e24aa; padding: 4px 4px 4px 4px;">';
	  echo "<ol id='ol-tag' type='decimal'>";

	  foreach($posts as $post) {
	  echo '<li>'.sanitize_text_field($post->post_title).'</li>';
	  }

	  echo '</ol>';
	  echo '</div>';
	  echo '<form id="valida" name="valida" class="valida" action="" method="POST">';
	  wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
	  echo '<script type="text/javascript">$(document).ready(function() {$("#valida").valida();});</script><br>';
	  echo '<fieldset>';
	  echo '<div class="codetype">';
	  echo '<label for="codetype">Select CodeType:</label>';
	  echo '<select name="codetype" required id="codetype" data-required="Please Select Codetype." require class="at-required">';
	  echo '<option selected value=""></option>';

	  $c1 = array("code128a","code128b","code128c","code39","code25","codabar");
	  foreach($c1 as $c){
	  echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	  }

	  echo '</select>';
	  echo '</div>';

	  echo '<div class="code128a code128b code128c code39 code25 codabar box"><br>';

	  echo '</div>';
	  echo '</fieldset><br>';

	  foreach($posts as $post) {
	  echo '<input type="hidden" id="product-'.sanitize_text_field($post->ID).'" name="products[]" class="products" value='.sanitize_text_field($post->ID).' checked="checked" />';
	  }

	  wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	  echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">';
	  echo '</form>';
	  }

	  // This function will display the Sku barcode
	  function hp_s_display_barcode(){
	  echo '<br>';
	  echo '<h2>Display Sku</h2>';
	  global $wpdb;
	  $result1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar3");
	  echo '<p style="font-size: 14px;"><strong> Total Numbers of Barcode:<span style="margin: 0px 0px 0px 6px;">'.esc_js(esc_html(count($result1))).'</span></strong> </p>';
	  echo '<br>';
	  echo '<form method="POST" action="">';
	  wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	  if ($_SERVER['REQUEST_METHOD']=="POST" && $_POST['remove_bar']) {
	  if ($_GET['bar']) $_POST['bar'][] = $_GET['bar'];
	  $count = 0;
	  if (is_array($_POST['bar'])) {
	  foreach ($_POST['bar'] as $id) { 
	  $wpdb->query("DELETE FROM {$wpdb->prefix}woo_bar3 WHERE id='".$id."' LIMIT 1"); 
	  $count++; 
	  }
	  }
	  }
	  echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: white;" id="printBar">
	  <tbody>';
	  global $wpdb;
	  $row_count=0;
	  $col_count=0;
	  $result2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woo_bar3");
	  foreach($result2 as $row2){
	  if($row_count%4==0){echo '<tr>';
	  $col_count=1;
	  }
	  echo '
	  <td style="border: 1px solid black;">
	  <input type="checkbox" name="bar[]" value="'.sanitize_text_field($row2->id).'">
	  <div class="center">
	  <img src="'.sanitize_text_field($row2->img_base64).'" alt="'.sanitize_text_field($row2->id).'" style="width: 160px; height: 70px; background-color: white; border: 0px solid #021a40; padding: 5px 5px 5px 5px;">
	  </div></td>';
	  if($col_count==4){
	  echo "</tr>";
	  }
	  $row_count++;
	  $col_count++;
	  }
	  echo '</tbody></table>'; 
	  echo '<br>';
	  wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
      echo '<input type="hidden" name="remove_bar" value="1" />';
	  echo '<input type="button" class="btn_blues" name="btn_blues" value="Print Out">'; 
      echo '<script type="text/javascript">function toggle(source) {checkboxes = document.getElementsByName("bar[]"); for(var i=0, n=checkboxes.length;i<n;i++) {checkboxes[i].checked = source.checked; }}</script>';
	  echo '<input type="submit" class="btn_reds" name="btn_reds" value="Remove"><input type="checkbox" onClick="toggle(this)" />Select All Checkboxes<br/>';	  echo '</form>';
	  }
       }
	  // Check if HP_Barcode exists then
	  // the plugin will activate or deactivate
	  if(class_exists('HP_Barcode')){
          register_activation_hook( __FILE__, array('HP_Barcode', 'activate_hp_barcode_lettersize'));
          register_deactivation_hook( __FILE__, array('HP_Barcode', 'deactivate_hp_barcode_lettersize'));
          register_uninstall_hook(__FILE__, array('HP_Barcode', 'uninstall_hp_barcode_lettersize'));
          $HP_Barcode = new HP_Barcode();
	  }
?>
