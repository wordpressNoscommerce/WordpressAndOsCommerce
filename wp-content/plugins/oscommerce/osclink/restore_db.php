<?php
require_once ('debug.php');

foreach($_POST as $key => $value) { fb('$_POST['.$key.']='.$value); }
foreach($_GET as $key => $value) { fb('$_GET['.$key.']='.$value); }

require('includes/setupFramework.php');

ob_end_flush(); // turn off buffering to keep on seeing progress
////////////////////////////////////////////////////////////////////////////////////////////////
// setup Products_id & action
////////////////////////////////////////////////////////////////////////////////////////////////
// be flexible about where our vars come from
$filename = $HTTP_POST_VARS['filename'];
if (!isset($filename))
$filename = $HTTP_GET_VARS['filename'];
if (!isset($filename)) {
	$msg = 'required filename not provided!';
	die('<h4 style="color: red;">'.$msg.'</h3>');
}

// be flexible about where our vars come from
$action = $HTTP_POST_VARS['action'];
if (!isset($action))
$action = $HTTP_GET_VARS['action'];
if (!isset($action)) {
	$msg = 'required action not provided!';
	die ('<h3>'.$msg.'</h3>');
}

$path = DIR_FS_CATALOG."../../../../database/shopdb/";
if (!is_dir($path)) {
	$msg = "path $path not found!";
	die ('<h3>'.$msg.'</h3>');
}
$path = realpath($path)."/";

//////////////////////////////////////////////////////////////////////////////////////////////
// need to be defined before called
function connectDb() {
	// Connect to MySQL server
	mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('Error connecting to MySQL server: ' . mysql_error());
	// Select database
	mysql_select_db(DB_DATABASE) or die('Error selecting MySQL database: ' . mysql_error());
	echo '<h3 style="color: green;">connected to shopkatapult DB</h3>';
}

//////////////////////////////////////////////////////////////////////////////////////////////
function loadDb($path,$filename) {
	// Temporary variable, used to store current query
	$templine = '';
	// Read in entire file
	$lines = file($path.$filename);

	if (!$lines) {
		$msg = "$path$filename is empty";
		$dircontent = scandir ($path);
		$fileTable = '<h4>Contents</h4>';
		foreach ($dircontent as $entry)  {
			if (strpos($entry,'.') == 0) continue;
			$fileTable.= $entry.'<br>';
		}
		echo '<hr>';
		die ('<h3>'.$msg.'</h3>'.$fileTable.'BYE');
	}
?>
<h4 style="color: green;">Started reading <?php echo count($lines); ?> lines
													at <?php echo date("d/m/y : H:i:s", time()); ?>
</h4>
	<?php

	// Loop through each line
	foreach ($lines as $line)
	{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
		continue;

		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
	}
	echo "<h3>loaded ".count($lines)." lines from <i>$path$filename</i></h3>";

}

////////////////////////////////////////////////////////////////////////////////////////////////
// Shopping cart actions
if (isset($action)) {
	// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
	if ($session_started == false) {
		tep_redirect(tep_href_link(FILENAME_COOKIE_USAGE));
	}

	switch ($action) {
		// customer wants to update the product quantity in their shopping cart
		case 'load' :
			echo "<h3>trying to load shopkatapult DB from: <i>".DIR_FS_CATALOG."$filename</i></h3>";
			connectDb();
			loadDb($path,$filename);
			break;
	}
	?>
<h4 style="color: green;">alles sch&ouml;n <?php echo date("d/m/y : H:i:s", time()); ?></h4>
	<?php
} ?>
