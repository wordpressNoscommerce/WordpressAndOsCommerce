<?php
require_once ('FirePHPCore/fb.php');
ob_start();
function fbDebugBacktrace($msg = "") {
    $NL = "\n";
    $dbgTrace = debug_backtrace();
    //	    fb("########################################################################################################################");
    if (strlen($msg) > 0) fb($msg);
    // get rid of backslashes and use dots instead
    $abspathPattern = '/'.preg_replace('/\\\\/', '.', rtrim(ABSPATH,'/')).'/';
    //	    fb('PATTERN:'.$abspathPattern);
    foreach($dbgTrace as $dbgIndex => $dbgInfo) {
        // trim path prefix
        $path = $dbgInfo['file'];
        if (strlen(ABSPATH) < strlen($path) && preg_match($abspathPattern, $path))
        $path = substr($path,strlen(ABSPATH));
        /*
         if ($dbgIndex == 1) {
         $newMsg = "at $dbgIndex  ".$path.
         " (line {$dbgInfo['line']}) -> {$dbgInfo['function']}(".
         $dbgInfo['args']."[".sizeof($dbgInfo['args'])."]".
         ")$NL";
         fb($newMsg);
         continue;
         }
         */
        // also shorten paths in args
        $args = $dbgInfo['args'];
        $json = "";
        foreach ($args as &$arg) {	// use reference to replace array members
            // remove path from string for shorter logging
            if (is_string($arg)) {
                if (strlen($arg) == 0) {
                    $arg = "<nul>";		// empty string
                    continue;
                }
                if (strlen(ABSPATH) < strlen($arg) && preg_match($abspathPattern, $arg))
                $arg = substr($arg,strlen(ABSPATH));
                $arg = json_encode($arg);
                continue;
            } // done with strings
            // encode normal object
            if (is_array($arg)) {
                foreach ($arg as &$elarg) {
                    $elarg = json_encode($elarg); // use json to serialize
                    if (is_string($arg) && strlen($elarg) == 0) fb(json_last_error());
                }
            } else {
                if ($arg != null && !is_object($arg)) {
                    //					    fb('#47 type: '. gettype($arg));
                    $arg = json_encode($arg);
                }
                if (is_string($arg) && strlen($arg) == 0) fb(json_last_error());
            }
        }
        $newMsg = "at $dbgIndex  ".$path." (line {$dbgInfo['line']}) -> {$dbgInfo['function']}(".$args.")$NL";
        fb($newMsg);
    }
}
?>