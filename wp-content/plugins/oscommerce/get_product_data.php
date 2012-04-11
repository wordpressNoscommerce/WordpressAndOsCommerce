<?php
require_once ('debug.php');
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
// prepare product query
$osc_products = new osc_products();
$osc_products->osc_db = new osc_db();
$osc_products->shop_id = $_GET['shopID'];
if (is_null($osc_products->shop_id)) $osc_products->shop_id = 1;     	// fix shop id
$osc_products->label_id = $_GET['labelID'];
$osc_products->artist_id = $_GET['artistID'];
$osc_products->format = $_GET['format'];
$osc_products->paged = $_GET['paged'];

$model = $_GET['mdl'];
if (empty($model)) throw new Exception("Required Parameter mdl is missing!");

$pid = $_GET['pid'];
fb("osc_get_product_data (".$model.",".$pid. ")".gettype($osc_products));
$osc_products->json = 0;
$result = array(
    'formats' => $osc_products->osc_get_product_formats($model),
    'xsell' => $osc_products->osc_get_xsell_products($pid),
);

if ( $_GET['json'])
    echo json_encode($result);
else
    return $result;
?>
