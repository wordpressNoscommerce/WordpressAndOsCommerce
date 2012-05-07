<?php
//require_once ('debug.php');
require_once( "../../../wp-config.php" );
require_once (ABSPATH."/wp-content/plugins/oscommerce/osCommerce.php");

// can be used to declare javascript variables with namespaces to use with your script
// wp_localize_script( $handle, $namespace, $variable_array );


// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );

/*    fb ("wp_enqueue_script( 'my-ajax-request', ".plugin_dir_url( __FILE__ )."js/ajax.js', array( 'jquery' ) ");
 // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
 wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
 */
// prepare manufacturer query
$osc_manufacturers = new osc_manufacturers();
//fb("osc_get_manufacturers_page:".gettype($osc_manufacturers));
$osc_manufacturers->osc_get_manufacturers_page();
?>
