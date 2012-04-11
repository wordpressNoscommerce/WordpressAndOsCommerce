<?php
require_once ('debug.php');
require_once( "../../../wp-config.php" );
require_once (ABSPATH."/wp-content/plugins/oscommerce/osCommerce.php");

// prepare product query
$osc_products = new osc_products();
$osc_products->osc_db = new osc_db();
$osc_products->shop_id = $_GET['shopID'];
if (is_null($osc_products->shop_id)) $osc_products->shop_id = 1;     	// fix shop id
$osc_products->label_id = $_GET['labelID'];
$osc_products->artist_id = $_GET['artistID'];
$osc_products->format = $_GET['format'];
$osc_products->paged = $_GET['paged'];
$osc_products->json = $_GET['json'];

$products_id = $_GET['pid'];
fb("osc_get_xsell_products:".gettype($osc_products).$products_id);
$osc_products->osc_get_xsell_products($products_id);
?>
