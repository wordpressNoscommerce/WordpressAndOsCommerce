<?php
require_once ("../../../wp-config.php" );
require_once ("osCommerce.php");

// can be used to declare javascript variables with namespaces to use with your script
// wp_localize_script( $handle, $namespace, $variable_array );

// prepare product query
$osc_products = new osc_products();
header('Content-type: application/json');
$osc_products->osc_get_special_offers();
?>
