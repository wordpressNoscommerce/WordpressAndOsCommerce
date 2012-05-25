<?php
// include wordpress
require_once ( "../../../wp-config.php" );
require_once (ABSPATH."/wp-content/plugins/oscommerce/osCommerce.php");

header('Content-type: text/html');
// echo '<h4>Here come the Shows</h4>';
$filter['artist'] = $_GET['artistId'];
$filter['limit'] = 8;
// 
echo gigpress_sidebar($filter);
?>
