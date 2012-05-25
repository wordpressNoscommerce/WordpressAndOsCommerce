<?php
// include wordpress
require_once ( "../../../wp-config.php" );
require_once (ABSPATH."/wp-content/plugins/oscommerce/osCommerce.php");

// prepare product query
$osc_products = new osc_products();
header('Content-type: application/json');
$osc_products->osc_get_labels();
?>
