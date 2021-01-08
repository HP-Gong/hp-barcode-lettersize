<?php
/**
 * Barcode Letter-Size
 *
 * Plugin Name: Barcode Letter-Size
 * Plugin URI: https://wordpress.org/plugins/barcode-lettersize
 * Description: Creating and Printing Barcodes on Letter-Size Papers.
 * Version: 1.3.0
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
define('hp_barcode_qr_lettersize_url_p', plugin_dir_url( __FILE__ ));

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

	   // Activation Function & installed woo_bar tables
	   public function activate_hp_barcode_qr_lettersize(){
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name1 = $wpdb->prefix . 'woo_bar1';
		$table_name2 = $wpdb->prefix . 'woo_bar2';

		$sql1 = "CREATE TABLE $table_name1 (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(50) NOT NULL,
		`category` VARCHAR(50) NOT NULL,
		`symbology` VARCHAR(50) NOT NULL,
		`data` VARCHAR(50) NOT NULL,
		`size` VARCHAR(50) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		$sql2 = "CREATE TABLE $table_name2 (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(50) NOT NULL,
	    `category` VARCHAR(50) NOT NULL,
		`symbology` VARCHAR(50) NOT NULL,
		`data` VARCHAR(50) NOT NULL,
		`size` VARCHAR(50) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql1);
        dbDelta($sql2);
		}

		// Deactivation Function
	   public function deactivate_hp_barcode_qr_lettersize(){
		global $wpdb;
		}

	   // Uninstall Function & Remove woo_bar tables from the databases
	   public function uninstall_hp_barcode_qr_lettersize(){
		global $wpdb;
		$woo_bar = $wpdb->prefix."woo_bar";
		$sql = "DROP TABLE IF EXISTS $woo_bar;";
		$wpdb->query($sql);
		}

	   // Check if WooCommerce plugin is install and activated
	   // in order for Barcode Letter-Size plugin to run
	   public function check_if_woo_install(){
	    if (! class_exists('WooCommerce')){
	    $url = admin_url('/plugins.php');
	    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    deactivate_plugins( plugin_basename( __FILE__ ));
	    wp_die( __('Barcode Letter-Size requires WooCommerce to run. <br>Please install WooCommerce and activate before attempting to activate again.<br><a href="'.$url.'">Return to the Plugins section</a>'));
	    }
        }

       // Check if WooCommerce plugin has the current version and
	   // activated in order for Barcode Letter-Size plugin to run
	   public function check_versions(){
	    global $woocommerce;
	    if (version_compare($woocommerce->version, '4.8.0', '<')){
	    $url = admin_url('/plugins.php');
	    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    deactivate_plugins( plugin_basename( __FILE__ ));
	    wp_die( __('Barcode Letter-Size is disabled.<br>Barcode Letter-Size requires a minimum of WooCommerce v4.8.0.<br><a href="'.$url.'">Return to the Plugins section</a>'));
	    }
	    }

	   // Add Menu Button/Menu Page & Submenu Buttons/Submenu Pages
	   public function add_admin_menu(){
		add_menu_page('Create Barcodes', 'Create Barcodes', 'administrator', 'hp_s_barcode_qr_products', array($this, 'plugin_settings'), hp_barcode_qr_lettersize_url_p . 'img/icon.png', 59);
		add_submenu_page('hp_s_barcode_qr_products', 'Select Barcode', 'Select Barcode', 'manage_options', 'hp_s_barcode_qr_products', 'hp_s_barcode_qr_products', 'hp_s_barcode_qr_products1');
		add_submenu_page('hp_s_barcode_qr_products', 'Display Barcode', 'Display Barcode', 'manage_options', 'hp_s_display_barcode_qr', 'hp_s_display_barcode_qr', 'hp_s_barcode_qr_products2');
		add_submenu_page('hp_s_barcode_qr_products', 'Shortcode', 'Shortcode', 'manage_options', 'short_barcode', 'short_barcode', 'hp_s_barcode_qr_products3');
		}

		 // Only Administrator have permissions to access this page
	   public function plugin_settings() {
	    if (!current_user_can('administrator')){
	    wp_die('You do not have sufficient permissions to access this page.');
	    }
	    }

		 // Verify Nonce Form
	   public function validate_form() {
		if(isset($_POST['btn_blue'])){
		if (!isset($_POST['barcode_display_products_nonce_1']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_1'], 'barcode_display_products_n2')){
		wp_die('You do not have access to this page.');
		} else{
		$products = sanitize_text_field(trim($_POST['products']));
	    $category = sanitize_text_field(trim($_POST['category']));
		$symbology = sanitize_text_field(trim($_POST['symbology']));
		$size = sanitize_text_field(trim($_POST['size']));
		}
		}
		if(isset($_POST['btn_red'])){
		if (!isset($_POST['barcode_display_products_nonce_2']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_2'], 'barcode_display_products_n1')){
		wp_die('You do not have access to this page.');
		} else{
		$products = sanitize_text_field(trim($_POST['products']));
	    $category = sanitize_text_field(trim($_POST['category']));
		$symbology = sanitize_text_field(trim($_POST['symbology']));
		$size = sanitize_text_field(trim($_POST['size']));
		}
		}
		if(isset($_POST['btn_blues'])){
		if (!isset($_POST['barcode_display_products_nonce_3']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_3'], 'barcode_display_products_n4')){
		wp_die('You do not have access to this page.');
		} 
		}
		if(isset($_POST['btn_reds'])){
		if (!isset($_POST['barcode_display_products_nonce_4']) || !wp_verify_nonce($_POST['barcode_display_products_nonce_3'], 'barcode_display_products_n4')){
		wp_die('You do not have access to this page.');
		}
		}
		}

	   // Register the jQuery & CSS scripts and link the files
	   public function create_barcode_scripts(){
		// jQuery
		wp_enqueue_script('jquery');
		// jQuery scripts for barcode
		wp_register_script('barcode', hp_barcode_qr_lettersize_url_p .'js/barcode.js', array('jquery'));
		wp_register_script('bundle', hp_barcode_qr_lettersize_url_p .'js/bundle.js', array('jquery'));
		wp_register_script('valida.2.1.7', hp_barcode_qr_lettersize_url_p .'js/valida.2.1.7.js', array('jquery'));
	    wp_register_script('printPreview', hp_barcode_qr_lettersize_url_p .'js/printPreview.js', array('jquery'));
		wp_enqueue_script('barcode');
		wp_enqueue_script('bundle');
		wp_enqueue_script('valida.2.1.7');
		wp_enqueue_script('printPreview');

	    // CSS scripts for barcode
		wp_register_style('barcode', hp_barcode_qr_lettersize_url_p . 'css/barcode.css');
		wp_enqueue_style('barcode');
	    }
        }

	  // This function will create the selected barcode.
	  function hp_s_barcode_qr_products(){

	   echo '<h2>Select 1 Barcode For All Products</h2>';
		  
	   if ($_SERVER['REQUEST_METHOD'] == "POST"){

	   $_POST = filter_input(INPUT_POST, FILTER_SANITIZE_STRING);
	   $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	   $products = filter_input(INPUT_POST, 'products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	   if (array_key_exists('products', $products)) {
	   $products = isset($_POST['products']) ? $_POST['products'] : '';
	   }

	   $ids = implode(',', $products);

	   if(!preg_match('/^[,0-9]+$/', $ids)) die('Fatal Error !');
		   
	   $category = isset($_POST['category']) ? $_POST['category'] : '';

       $query = new WP_Query(array('query' => 'id='.$ids, 'post_type' => array('product'), 'post_status' => 'publish', 'product_cat' => ''.$category.'', 'posts_per_page' => -1));
		   
	   if($query->have_posts()){

	   while ($query->have_posts()){

	   $query->the_post();

	   if($_POST){
		   
	   $title = get_the_title($query->post_title);
		   
	   $category = sanitize_text_field(trim($_POST['category']));
		  
	   $symbology = sanitize_text_field(trim($_POST['symbology']));

	   $data = get_post_meta(get_the_ID(), '_sku', true);
		   
	   $size = sanitize_text_field(trim($_POST['size']));

	   global $wpdb;
	   $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}woo_bar1 (title, category, symbology, data, size) VALUES (%s, %s, %s, %s, %s)", $title, $category, $symbology, $data, $size));
	   $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}woo_bar2 (title, category, symbology, data, size) VALUES (%s, %s, %s, %s, %s)", $title, $category, $symbology, $data, $size));
	   }
		   
	   }
	   }else {
	   echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	   }
	   wp_redirect(admin_url('admin.php?page=hp_s_display_barcode_qr'));
	   exit;
	   }

	   $args = array('post_type' => array('product'), 'posts_per_page' => -1);
	   $posts = get_posts( $args );

	   if(is_array($posts) && count($posts) == 0){
	   echo "<h3>No products found !</h3><p>Go to <a href=".admin_url('edit.php?post_type=product').">Products</a> to create Your first product !.</p>";
	   }

	   echo '<p style="font-size: 14px;"><strong> Total Numbers of Products:<span style="margin: 0px 0px 0px 6px;">'.esc_html($post = count($posts)).'</span></strong> </p>';
	   echo '<form id="valida" name="valida" class="valida" action="" method="POST">';
	   wp_nonce_field('barcode_display_products_n2', 'barcode_display_products_nonce_1');
	   echo '<script type="text/javascript">$(document).ready(function() {$("#valida").valida();});</script><br>';
	   echo '<fieldset>';
		  
	   echo '<div class="category">';
	   echo '<label for="category" style="font-size:14px;">Select a category: </label>';
	   echo '<select name="category" required id="category" data-required="Please select a category." require class="at-required">';
	   echo '<option selected value=""></option>'; 
	   echo '<optgroup label=" -- Category -- ">'; 
       $parentid = get_queried_object_id();
         
       $args = array('parent' => $parentid);
 
       $terms = get_terms( 'product_cat', $args );
 
       foreach ( $terms as $term ) {   
	   echo "<option value='".esc_attr($term->name)."'>".esc_html($term->name). esc_html("&nbsp;(".$term->count.")")."</option>";
       }       
       echo '</optgroup>'; 
	   echo '</select>'; 
	   echo '</div><br>';
		  
	   echo '<div class="symbology">';
	   echo '<label for="symbology" style="font-size:14px;">Select a codetype: </label>';
	   echo '<select name="symbology" required id="symbology" data-required="Please select a codetype." require class="at-required">';
       echo '<option selected value=""></option>'; 
	   echo '<optgroup label=" -- Barcode -- ">'; 
	   $c2 = array("upc-a","ean-13","ean-8","code-39","code-93","ean-128","code-128","itf");
	   foreach($c2 as $c){
	   echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	   }
       echo '</optgroup>'; 
	   echo '<optgroup label=" -- QR Code -- ">'; 
	   $c3 = array("qr","dmtx");
	   foreach($c3 as $c){
	   echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	   }
       echo '</optgroup>'; 
	   echo '</select>';
	   echo '</div><br>';
		  
	   echo '<div class="size">';
	   echo '<label for="size" style="font-size:14px;">Select a number size for the barcode image: </label>';
	   echo '<select name="size" required id="size" data-required="Please select a number size for the barcode image." require class="at-required">';
       echo '<option selected value=""></option>'; 
       echo '<optgroup label=" - Size - ">'; 
	   $c4 = array("1","2","3","4","5");
	   foreach($c4 as $c){
	   echo "<option value='".esc_attr($c)."'>".esc_attr($c)."</option>";
	   }
       echo '</optgroup>'; 
	   echo '</select>';
	   echo '</div>';
	   echo '</fieldset><br>';

	   foreach($posts as $post) {
	   echo '<input type="hidden" id="product-'.sanitize_text_field($post->ID).'" name="products[]" class="products" value="'.sanitize_text_field($post->ID).'" checked="checked" />';
	   }
       wp_nonce_field('barcode_display_products_n1', 'barcode_display_products_nonce_2');
	   echo '<input type="submit" class="btn_blue" name="btn_blue" value="Generate">';
	   echo '<input type="reset" class="btn_red" name="btn_red" value="Reset">';
	   echo '</form>';
	   }

	  // This function will display the the selected Barcode
	  function hp_s_display_barcode_qr(){

       echo '<br>';
	   echo '<h2>Display Barcode:</h2>';
	   global $wpdb;
	   $try0 = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woo_bar1 LIMIT 1");
       $result0 = $wpdb->get_results($try0); 
	   foreach($result0 as $row0){
	   echo '<p style="font-size: 14px;"><strong> Category:<span style="margin: 0px 0px 0px 6px;">'.sanitize_text_field($row0->category).'</span></strong> </p>';
	   }
	   $try1 = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woo_bar1");
       $result1 = $wpdb->get_results($try1);
	   echo '<p style="font-size: 14px;"><strong> Total Numbers of Barcode:<span style="margin: 0px 0px 0px 6px;">'.esc_js(esc_html(count($result1))).'</span></strong> </p>';
	   echo '<br>';
	   echo '<form method="POST" action="">';
       wp_nonce_field('barcode_display_products_n3', 'barcode_display_products_nonce_4');
	   $barcodes = isset($_POST['barcodes']) ? $_POST['barcodes'] : '';
	   if ($_SERVER['REQUEST_METHOD']=="POST" && $_POST['remove_barcode']) {
	   if ($_GET['barcodes']) $_POST['barcodes'][] = $_GET['barcodes'];
	   $count = 0;
	   if (is_array($_POST['barcodes'])){
	   foreach ($_POST['barcodes'] as $id){
	   $try2 = $wpdb->prepare("DELETE FROM {$wpdb->prefix}woo_bar1 WHERE id='".$id."' LIMIT 1");
       $result2 = $wpdb->get_results($try2);
	   $count++;
	   }
	   }
	   }
	   echo '        
       <table style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: white;" cellspacing="0" cellpadding="0" id="div_to_print">
	   <tbody>';
	   global $wpdb;
	   $row_count=0;
	   $col_count=0;
	   $try3 = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woo_bar1");
       $result3 = $wpdb->get_results($try3);
	   foreach($result3 as $row3){
	   if($row_count%4==0){echo '<tr>';
	   $col_count=1;
	   }
	   echo '
	   <td style="border: 1px solid black;">
	   <input type="checkbox" name="barcodes[]" value="'.sanitize_text_field($row3->id).'">	   	   
	   <div id="bart"><p style="font-size:12px; font-family:fontello; margin: 0px 0px -2px 0px;">'.sanitize_text_field($row3->title).'</p>
	   <img src="'.plugins_url("image.php?f=png&s=".sanitize_text_field($row3->symbology)."&d=".sanitize_text_field($row3->data)."&sf=".sanitize_text_field($row3->size)."&ts=2&th=12&h=80", __FILE__ ).'" alt="'.sanitize_text_field($row3->id).'"></div>
	   </td>';
	   if($col_count==4){
	   echo "</tr>";
	   }
	   $row_count++;
	   $col_count++;
	   }
	   echo '</tbody></table>';
	   echo '<br>';
       wp_nonce_field('barcode_display_products_n4', 'barcode_display_products_nonce_3');
       echo '<input type="hidden" name="remove_barcode" value="1" />';
	   echo '<script type="text/javascript">$(function(){$("#btn_blues").printPreview({obj2print:"#div_to_print",width:"810"});}); function toggle(source) {checkboxes = document.getElementsByName("barcodes[]"); for(var i=0, n=checkboxes.length;i<n;i++) {checkboxes[i].checked = source.checked; }}</script>';
	   echo '<input type="button" id="btn_blues" class="btn_blues" name="btn_blues" value="Print Preview">';
	   echo '<input type="submit" class="btn_reds" name="btn_reds" value="Remove"><input type="checkbox" onClick="toggle(this)" />Select All Checkboxes<br/>';
	   echo '</form>';
       }
	
	// This function will display the the shortcodes
	function short_barcode(){
	   echo '<br>';
	   echo '<h2>Shortcode Barcodes</h2>';
	   global $wpdb;
	   $final1 = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woo_bar2");
       $time1 = $wpdb->get_results($final1);
	   echo '<p style="font-size: 14px;"><strong> Total Numbers of Sku:<span style="margin: 0px 0px 0px 6px;">'.esc_js(esc_html(count($time1))).'</span></strong> </p>';
	   echo '<br>';
	   echo '<form method="POST" action="">';
	   wp_nonce_field('barcode_display_products_n3', 'barcode_display_products_nonce_4');
	   if ($_SERVER['REQUEST_METHOD']=="POST" && $_POST['remove_gbar']) {
	   if ($_GET['shortcodes']) $_POST['shortcodes'][] = $_GET['shortcodes'];
	   if (is_array($_POST['shortcodes'])){
	   foreach ($_POST['shortcodes'] as $id){
	   $final2 = $wpdb->prepare("DELETE FROM {$wpdb->prefix}woo_bar2 WHERE id='".$id."' LIMIT 1");
       $time2  = $wpdb->get_results($final2);
	   }
	   }
	   }
       echo '<script type="text/javascript">function toggle(source) {
       checkboxes = document.getElementsByName("shortcodes[]"); 
       for(var i=0, n=checkboxes.length;i<n;i++) {checkboxes[i].checked = source.checked; }}</script>';
       echo '<div id="dvData">';
	   echo '<table style="border-collapse: collapse; width: 40%; border: 1px solid black; background-color: white;">
	   <thead>
	   <tr>
	   <th class="check-column" scope="col" style="border: 1px solid black;"><input type="checkbox" onClick="toggle(this)" value="'.sanitize_text_field($row3->id).'"></th>
	   <th style="border: 1px solid black;" class="col">ID</th>
	   <th style="border: 1px solid black;" scope="col">Name</th>
	   <th style="border: 1px solid black;" scope="col">Category</th>
	   <th style="border: 1px solid black;" class="col">Symbology</th>
	   <th style="border: 1px solid black;" class="col">Shortcode</th>
	   </tr>
	   </thead>
	   <tbody>';
	   global $wpdb;
	   $final3 = "SELECT * FROM {$wpdb->prefix}woo_bar2";
       $time3 = $wpdb->get_results($final3);
	   if(count($time3)){
	   foreach($time3 as $row3){
	   echo '<tr>
	   <td style="border: 1px solid black;" align="center"><input type="checkbox" name="shortcodes[]" value="'.sanitize_text_field($row3->id).'"></td>
	   <td style="border: 1px solid black;" align="center">'.sanitize_text_field($row3->id).'</td>
	   <td style="border: 1px solid black;" align="center">'.sanitize_text_field($row3->title).'</td>
	   <td style="border: 1px solid black;" align="center">'.sanitize_text_field($row3->category).'</td>
	   <td style="border: 1px solid black;" align="center">'.sanitize_text_field($row3->symbology).'</td>
	   <td style="border: 1px solid black;" align="center">[gbarcode id="'.sanitize_text_field($row3->id).'" title="'.sanitize_text_field($row3->title).'"]</td>
	   </tr>';
	   }
	   }
	   echo'</tbody>
	   </table>
	   </div>
	   <br>';
       wp_nonce_field('barcode_display_products_n4', 'barcode_display_products_nonce_3');
       echo '<input type="hidden" name="remove_gbar" value="1" />';
	   echo '<input type="submit" class="btn_reds" name="btn_reds" value="Remove">';
	   echo '</form>';
       }

      // This function create the shortcodes
      function short_barcode2($atts){

      global $wpdb;

      $atts = shortcode_atts(array('id' => ''), $atts);

      $product_id = $atts['id'];

      $products = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woo_bar2 WHERE id = %s", $product_id);

      $results = $wpdb->get_results($products);

      foreach ($results as $row5){ 
      $output = '<center><p style="font-size:12px; font-family:fontello; margin: 0px 0px -2px 0px;">'.sanitize_text_field($row5->title).'</p>
      <img src="'.plugins_url("image.php?f=png&s=".sanitize_text_field($row5->symbology)."&d=".sanitize_text_field($row5->data)."&sf=".sanitize_text_field($row5->size)."&ts=2&th=12&h=80", __FILE__ ).'" alt="'.sanitize_text_field($row5->id).'"></center>';
      }
      return $output;
      }

      add_shortcode('gbarcode', 'short_barcode2');
	
  }
	  // Check if HP_Barcode exists then
	  // the plugin will activate or deactivate
	  if(class_exists('HP_Barcode')){
         register_activation_hook( __FILE__, array('HP_Barcode', 'activate_hp_barcode_qr_lettersize'));
         register_deactivation_hook( __FILE__, array('HP_Barcode', 'deactivate_hp_barcode_qr_lettersize'));
         register_uninstall_hook(__FILE__, array('HP_Barcode', 'uninstall_hp_barcode_qr_lettersize'));
         $HP_Barcode = new HP_Barcode();
	  }
?>