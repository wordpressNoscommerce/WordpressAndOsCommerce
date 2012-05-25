<?php
// include wordpress
require_once( "../../../wp-config.php" );
require_once ("osCommerce.php");
// prepare manufacturer query
$osc_manufacturers = new osc_manufacturers();
$osc_manufacturers->osc_get_manufacturers_page();
?>
