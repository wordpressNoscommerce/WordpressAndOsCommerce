<?php
/** AJAX entry point for various product queries:
 * formats & xsell for the products page
 * prodpair for single product reloads after a shopping cart reload 
 **/
// include wordpress
require_once( "../../../wp-config.php" );
require_once ("osCommerce.php");
// prepare product query
$osc_products = new osc_products();
// get special parms
$pid = $_GET['pid'];
$model = $_GET['mdl'];
$result = new StdClass;

if ($model && $pid) {
	$result->formats = $osc_products->osc_get_product_formats($model);
  $result->xsell = $osc_products->osc_get_xsell_products($pid);
} elseif ($pid) {	// for missing prods in client database with parent
	$result->prodpair = $osc_products->osc_get_product_and_parent($pid);
}

if ($osc_products->json) {
	header('Content-type: application/json');					
  echo json_encode($result);
} else
  return $result;
?>
