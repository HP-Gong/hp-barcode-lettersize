<?php
/**
 * Barcode Letter-Size
 *
 * Plugin Name: Barcode Letter-Size
 * Plugin URI: https://github.com/hp-gong/hp-barcode-lettersize
 * Description: Creating and Printing Barcodes on Letter-Size Papers.
 * Version: 1.1.0
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
	 	
	  // Activation & Deactivation Function
	  public static function activate_hp_barcode_lettersize(){}
	  public static function deactivate_hp_barcode_lettersize(){}
      
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
	    if (version_compare($woocommerce->version, '3.0.7', '<')){
	    $url = admin_url('/plugins.php');
	    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    deactivate_plugins( plugin_basename( __FILE__ ));
	    wp_die( __('Barcode Letter-Size is disabled.<br>Barcode Letter-Size requires a minimum of WooCommerce v3.0.7.<br><a href="'.$url.'">Return to the Plugins section</a>'));
	    }
	    }
       
	   // Add Menu Button/Menu Page & Submenu Buttons/Submenu Pages
	   public static function add_admin_menu(){
		add_menu_page('Create Barcodes', 'Create Barcodes', 'administrator', 'hp_ts_barcode_display_products', array($this, 'plugin_settings'), hp_barcode_lettersize_url_p . 'img/icon.png', 59);
		add_submenu_page('hp_ts_barcode_display_products', 'Title & Sku', 'Title & Sku', 'manage_options', 'hp_ts_barcode_display_products', 'hp_ts_barcode_display_products', 'hp_ts_barcode_display_products1');
		add_submenu_page('hp_ts_barcode_display_products', 'Title', 'Title', 'manage_options', 'hp_t_barcode_display_products', 'hp_t_barcode_display_products', 'hp_t_barcode_display_products2');
		add_submenu_page('hp_ts_barcode_display_products', 'Sku', 'Sku', 'manage_options', 'hp_s_barcode_display_products', 'hp_s_barcode_display_products', 'hp_ts_barcode_display_products3');
		}
		
		// Only Administrator have permissions to access this page
	    public static function plugin_settings() {
	    if (!current_user_can('administrator')){
	    wp_die('You do not have sufficient permissions to access this page.');
	    }
	    }
		
		// Verify Nonce Form
		public static function validate_form() {	
        if(isset($_POST['btn_blue'])){
        if (!isset($_POST['barcode_display_products_nonce']) || !wp_verify_nonce($_POST['barcode_display_products_nonce'], 'barcode_display_products_n')){
        wp_die('You do not have access to this page.');
        }
		else {
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
	    wp_register_script('jQuery-image-upload', hp_barcode_lettersize_url_p .'js/jQuery-image-upload.js', array('jquery'));
	    wp_register_script('valida.2.1.7', hp_barcode_lettersize_url_p .'js/valida.2.1.7.js', array('jquery'));
		wp_enqueue_script('barcode');
		wp_enqueue_script('bundle'); 
	    wp_enqueue_script('jQuery-image-upload');
	    wp_enqueue_script('valida.2.1.7');
		// CSS scripts for barcode
	    wp_register_style('barcode', hp_barcode_lettersize_url_p . 'css/barcode.css');;
	    wp_enqueue_style('barcode');
	    }
   
  }	
	
   // This function will create the Title & Sku barcode. 
   // The results will display on the same page 
   function hp_ts_barcode_display_products(){

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
		  
	   echo '<input type="submit" class="btn_blue" name="btn_blue" id="back" value="Back">'; 
	   echo '<input type="submit" class="btn_red" name="btn_red" id="printOut" value="Preview">';
	   
	   echo '<h3>Displayed Barcodes</h3>';
	   echo '<div id="print_barcodes" class="print_barcodes" style="width: 100%; height: 100%;">';
	  
	   while ($query->have_posts()){ 
	  
	   $query->the_post();
	  
	   require_once 'gd/hp-title-sku-barcode-gd.php';
	   
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
	   
	   echo '<img src="'.$img_base64.'" class="image" style="background-color: white; display: block; float: left; border: 1px solid #021a40; padding-top: 3px; padding-left: 4px; padding-bottom:3px; padding-right: 4px;">';
	   echo "<script>$('.image').imageUpload({formAction: '/'});</script>";
	   } 
	   echo '</div>';
	   }else {
	   echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	   } 
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
	   
	   wp_nonce_field('barcode_display_products_n', 'barcode_display_products_nonce');
	   echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	   echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">'; 
	   echo '</form>'; 
       } 
    
   // This function will create the Title barcode. 
   // The results will display on the same page 
    function hp_t_barcode_display_products(){

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
		  
	   echo '<input type="submit" class="btn_blue" name="btn_blue" id="back" value="Back">'; 
	   echo '<input type="submit" class="btn_red" name="btn_red" id="printOut" value="Preview">';
	   
	   echo '<h3>Displayed Barcodes</h3>';
	   echo '<div id="print_barcodes" class="print_barcodes" style="width: 100%; height: 100%;">';
	  
	   while ($query->have_posts()){ 
	  
	   $query->the_post();
	  
	   require_once 'gd/hp-title-barcode-gd.php';
	   
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
	   
	   echo '<img src="'.$img_base64.'" class="image" style="background-color: white; display: block; float: left; border: 1px solid #021a40; padding-top: 3px; padding-left: 4px; padding-bottom:3px; padding-right: 4px;">';
	   echo "<script>$('.image').imageUpload({formAction: '/'});</script>";
	   } 
	   echo '</div>';
	   }else {
	   echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	   } 
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
	   
	   wp_nonce_field('barcode_display_products_n', 'barcode_display_products_nonce');
	   echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	   echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">'; 
	   echo '</form>'; 
   }
	
   // This function will create the Sku barcode. 
   // The results will display on the same page    
    function hp_s_barcode_display_products(){

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
		  
	   echo '<input type="submit" class="btn_blue" name="btn_blue" id="back" value="Back">'; 
	   echo '<input type="submit" class="btn_red" name="btn_red" id="printOut" value="Preview">';
	   
	   echo '<h3>Displayed Barcodes</h3>';
	   echo '<div id="print_barcodes" class="print_barcodes" style="width: 100%; height: 100%;">';
	  
	   while ($query->have_posts()){ 
	  
	   $query->the_post();
	  
	   require_once 'gd/hp-sku-barcode-gd.php';
	    
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
	   
	   echo '<img src="'.$img_base64.'" class="image" style="background-color: white; display: block; float: left; border: 1px solid #021a40; padding-top: 3px; padding-left: 4px; padding-bottom:3px; padding-right: 4px;">';
	   echo "<script>$('.image').imageUpload({formAction: '/'});</script>";
	   } 
	   echo '</div>';
	   }else {
	   echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	   } 
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
	   
	   wp_nonce_field('barcode_display_products_n', 'barcode_display_products_nonce');
	   echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	   echo '<input type="reset" class="btn_reds" name="btn_reds" value="Reset">'; 
	   echo '</form>'; 
       }
    }
	  // Check if HP_Barcode exists then 
	  // the plugin will activate or deactivate
	  if(class_exists('HP_Barcode')){
		register_activation_hook( __FILE__, array('HP_Barcode', 'activate_hp_barcode_lettersize'));
		register_deactivation_hook( __FILE__, array('HP_Barcode', 'deactivate_hp_barcode_lettersize'));
		$HP_Barcode = new HP_Barcode();
	  }
	
?>
