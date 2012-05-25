<?php
// include wordpress
require_once ( "../../../wp-config.php" );
require_once ("osCommerce.php");
// prepare product query
$osc_products = new osc_products();
$osc_products->osc_get_products_page();
?>
