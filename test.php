<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head></head>
<body>
<h1>Test.php</h1>
<?php
require 'wp-config.php';

$dbh = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD, true );

echo 'DB_HOST:'.DB_HOST;
echo ' DB_USER:'.DB_USER;
echo ' <p>';
echo $dbh;
?>
</body>
</html>
