<?php
    // =============================
    //     Shorteners Script
    // =============================
    //
    // Shorteners Script currently supports following shortener services
    //      - TinyURL (use as: tinyurl)
    //      - BitLy (use as: bitly)
    //      - SuPr (use as: supr)
    //      - IsGd (use as: isgd)
    //      - L4uIn (use as: l4uin)
    //      - ToLy (use as: toly)
    //      - Adfly (use as: adfly)
    //      - KwnMe (use as: kwnme)
    //      - BrifUs (use as: brifus)
    //
    // Usage:
    // http://www.example.com/short.php?shortener=<SHORTENER SERVICE PROVIDER>&amp;longurl=<URL TO SHORTEN>
    // ==================================================================================================

    // Get the passed arguments
    if (isset($_GET['shortener']))
        $shortener = $_GET['shortener'];
    else
        $shortener ='tinyurl';
    $passedurl = $_GET['longurl'];

    // Determine if the passed long URL has the 'http://' at the start
    if (strpos($passedurl, 'http://') !== false) {
        $passedurl = 'http://' . $passedurl;
    }

    // Determine which function to call
    if ($shortener == 'tinyurl') {
        $shorturl = shortTinyURL($passedurl);
    } elseif ($shortener == 'bitly') {
        $shorturl = shortBitly($passedurl);
    } elseif ($shortener == 'supr') {
        $shorturl = shortSupr($passedurl);
    } elseif ($shortener == 'isgd') {
        $shorturl = shortIsgd($passedurl);
    } elseif ($shortener == 'l4uin') {
        $shorturl = shortL4uin($passedurl);
    } elseif ($shortener == 'toly') {
        $shorturl = shortToly($passedurl);
    } elseif ($shortener == 'adfly') {
        $shorturl = shortAdfly($passedurl);
    } elseif ($shortener == 'kwnme') {
        $shorturl = shortKwnme($passedurl);
    } elseif ($shortener == 'brifus') {
        $shorturl = shortBrifus($passedurl);
    } else {
        $shorturl = 'You need to use a valid shortener service.';
    }

    // Print the result
    // or you can do what ever you like with the result (shortened URL)
    if (true)
        echo json_encode($shorturl);
    else
        echo $shorturl;

    // TinyURL shortener
    function shortTinyURL($ToConvert) {
    	return $ToConvert;
    	try {
        $short_url = file_get_contents('http://tinyurl.com/api-create.php?url=' . $ToConvert);
    	} 
    	catch (Exception $e) {
    		return "/notfound.html";
    	}
    	
        return $short_url;
    }

    // Bit.ly shortener
    function shortBitly($ToConvert) {
        $bitlylogin = 'YOUR_USER_NAME';
        $bitlyapikey = 'YOUR_API_KEY';
        $bitlyurl = file_get_contents('http://api.bit.ly/shorten?version=2.0.1&amp;longUrl=' . $ToConvert . '&amp;login=' . $bitlylogin . '&amp;apiKey=' . $bitlyapikey);
        $bitlycontent = json_decode($bitlyurl,true);
        $bitlyerror = $bitlycontent['errorCode'];
        $short_url = $bitlycontent['results'][$ToConvert]['shortUrl'];
        return $short_url;
    }

    // Su.pr shortener
    function shortSupr($ToConvert) {
        $short_url = file_get_contents('http://su.pr/api?url=' . $ToConvert);
        return $short_url;
    }

    // Is.gd shortener
    function shortIsgd($ToConvert) {
        $short_url = file_get_contents('http://www.is.gd/api.php?longurl=' . $ToConvert);
        return $short_url;
    }

    // L4u.in shortener
    function shortL4uin($ToConvert) {
        $short_url = file_get_contents('http://www.l4u.in/?module=ShortURL&amp;file=Add&amp;mode=API&amp;url=' . $ToConvert);
        return $short_url;
    }

    // To.ly shortener
    function shortToly($ToConvert) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://to.ly/api.php?longurl=".urlencode($ToConvert));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $shorturl = curl_exec ($ch);
        curl_close ($ch);
        return $short_url;
    }

    // Adf.ly shortener
    function shortAdfly($ToConvert) {
        $APIKey = 'YOUR_API_KEY';
        $UserID = 'YOUR_USER_ID';
        $ShortType = 'int'; // or 'banner'
        $short_url = file_get_contents('http://adf.ly/api.php?key=' . $APIKey . '&amp;uid=' . $UserID . '&amp;advert_type=' . $ShortType . '&amp;url=' . $ToConvert);
        return $short_url;
    }

    // Kwn.me shortener
    function shortKwnme($ToConvert) {
        $short_url = file_get_contents('http://kwn.me/t.php?process=1&amp;url=' . $ToConvert);
        return $short_url;
    }

    // Brif.us shortener
    function shortBrifus($ToConvert) {
        $short_url = file_get_contents('http://brif.us/api.php?action=shorturl&amp;format=simple&amp;url=' . $ToConvert);
        return $short_url;
    }
?>