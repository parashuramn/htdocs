<?php
        function gb_urldecode($arr) {
            if(is_string($arr)) {
                    if(strpos($arr, "mailto") == false)
                    return urldecode($arr);
                else
                {
                    $arr = str_replace(array("%40", "%3A"),array("@", ":"), $arr);
                    //Exclude emails from urldecode
                    preg_match_all("/mailto([^@]+)@/i", $arr, $matches);
                    $replaced = array();
                    $replace_string = "NANBMUYJKHASLKDKA";
                    if(!empty($matches[0])) {
                            foreach($matches[0] as $k => $matchstring) {
                                    $rs = "[[".$replace_string.$k."]]";
                                    $replaced[$rs] = rawurldecode($matchstring);
                                    $arr = str_replace($matchstring, $rs, $arr);
                            }
                            $arr = urldecode($arr);
                            $arr = str_replace(array_keys($replaced), $replaced, $arr);
                            return $arr;
                    }
                    else
                    return urldecode($arr);
                }

            }
            else if(is_array($arr))
            {
                foreach($arr as $key => $val)
                    $arr[$key] = gb_urldecode($val);
            }
            else if(is_object($arr))
            {
                foreach($arr as $key => $val)
                    $arr->{$key} = gb_urldecode($val);
            }


            return $arr;
	}

	function generate_time() {
		$milliseconds = round(microtime(true) * 1000);
		$timestamp =  floor($milliseconds/1000);
		$milli_part = $milliseconds - $timestamp*1000;
		$d = new DateTime(date("Y-m-d H:i:s", $timestamp));
		$d->setTimezone(new DateTimeZone("UTC"));
		$tz = $d->format(DateTime::ATOM);
		$tz = str_replace("+00:00", ".".$milli_part."Z", $tz);
		return $tz;
	}
	function generate_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}	
	function validate_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}	
	function validate_uri($uri) {
		return true;
	}
    function clean_agent($agent) {
            $string = is_string($agent);
		
	    //Fix trailing and leading quotes in the agent string in state api call.
            if($string)
            $agent = trim($agent, '"');

            $agent = ($string)? json_decode($agent):$agent;
            if(!empty($agent->account->homePage) && trim($agent->account->homePage) == "-" || !empty($agent->account->name) && trim($agent->account->name) == "-" ) {
                    unset($agent->account);
            }
            $agent = ($string)? json_encode($agent):$agent;
            return $agent;
    }
	function verify_agent($agent) {
		$count = !empty($agent->mbox) + !empty($agent->mbox_sha1sum) + !empty($agent->openid) + (!empty($agent->account->homePage) || !empty($agent->account->name));
		if($count > 1)
			return "Multiple Inverse Functional Identifier for agent: ".json_encode($agent);
		if($count == 0)
			return "No Inverse Functional Identifier for agent: ".json_encode($agent);
		
		if(!empty($agent->mbox)) {
			$mbox = is_array($agent->mbox)? $agent->mbox[0]:$agent->mbox;
			if(strpos($mbox, "mailto:") !== 0)
				return 'mbox must start with "mailto:". invalid mbox ['.$mbox.'] for agent: '.json_encode($agent);
			$email = str_replace("mailto:", "", $mbox);
			if(!validate_email($email)) {
				return "Invalid email id [".$email."] for agent: ".json_encode($agent);
			}
		}
		if(!empty($agent->openid)) {
			if(!validate_uri($agent->openid))
				return "Invalid openId [".$agent->openid."] for agent: ".json_encode($agent);
		}
		if(!empty($agent->account->homePage) && !empty($agent->account->name)) {
			if(!validate_uri($agent->account->homePage))
				return "Invalid Account homePage [".$agent->account->homePage."] for agent: ".json_encode($agent);
		}
		if(!empty($agent->account->homePage) + !empty($agent->account->name) == 1) {
			return "Invalid account for agent: ".json_encode($agent);
		}
	}
    function clear_cache() {
        Cache::clear();
        clearCache();

        $files = array();
        $folder = glob(CACHE . '*');
        if(!empty($folder))
        $files = array_merge($files, $folder); // remove cached css
        
        $folder = glob(CACHE . 'css' . DS . '*'); // remove cached css
        if(!empty($folder))
        $files = array_merge($files, $folder); // remove cached css
        
        $folder =  glob(CACHE . 'js' . DS . '*');  // remove cached js           
        if(!empty($folder))
        $files = array_merge($files, $folder); // remove cached css
        
        $folder = glob(CACHE . 'models' . DS . '*');  // remove cached models           
        if(!empty($folder))
        $files = array_merge($files, $folder); // remove cached css
        
        $folder = glob(CACHE . 'persistent' . DS . '*');  // remove cached persistent           
        if(!empty($folder))
        $files = array_merge($files, $folder); // remove cached css
        
        foreach ($files as $f) {
            if (is_file($f)) {
                unlink($f);
            }
        }

        if(function_exists('apc_clear_cache')):      
        apc_clear_cache();
        apc_clear_cache('user');
        endif;
    }
	function seconds_to_time($inputSeconds) {
		$secondsInAMinute = 60;
		$secondsInAnHour  = 60 * $secondsInAMinute;
		$secondsInADay    = 24 * $secondsInAnHour;

		$return = "";
		// extract days
		$days = floor($inputSeconds / $secondsInADay);
		$return .= empty($days)? "":$days."day";
		
		// extract hours
		$hourSeconds = $inputSeconds % $secondsInADay;
		$hours = floor($hourSeconds / $secondsInAnHour);
		$return .= (empty($hours) && empty($days))? "":" ".$hours."hr";
		
		// extract minutes
		$minuteSeconds = $hourSeconds % $secondsInAnHour;
		$minutes = floor($minuteSeconds / $secondsInAMinute);
		$return .= (empty($hours) && empty($days) && empty($minutes))? "":" ".$minutes."min";
		
		// extract the remaining seconds
		$remainingSeconds = $minuteSeconds % $secondsInAMinute;
		$seconds = ceil($remainingSeconds);
		$return .= " ".$seconds."sec";

		return trim($return);
	}
	function to_time($timeval) {
		return seconds_to_time(to_seconds($timeval));
	}
	function maybe_to_time($timeval) {
		if(strpos($timeval, "PT") !== 0)
			return $timeval;
		else if(!strpos($timeval, "S"))
			return $timeval;

		$t = str_replace("PT", "", $timeval);
		$t = str_replace("H", "", $t);
		$t = str_replace("M", "", $t);
		$t = str_replace("S", "", $t);
		
		if(!is_numeric($t))
			return $timeval;
		else
			return to_time($timeval);
	}
	function to_seconds($timeval) {
		if(empty($timeval)) return 0;
		
		$timeval = str_replace("PT", "", $timeval);
		$timeval = str_replace(array("H", "hr"), "h ", $timeval);
		$timeval = str_replace(array("M", "min"), "m ", $timeval);
		$timeval = str_replace(array("S", "sec"), "s ", $timeval);

		$time_sections = explode(" ", $timeval);
		$h = $m = $s = 0;
		foreach($time_sections as $k => $v) {
			$value = trim($v);
			
			if(strpos($value, "h"))
			$h = intVal($value);
			else if(strpos($value, "m"))
			$m = intVal($value);
			else if(strpos($value, "s"))
			$s = floatval($value);
		}
		$time = $h * 60 * 60 + $m * 60 + $s;
		
		if($time == 0)
		$time = floatval($timeval);
		
		return $time;
	}
	function readable_timestamp($time) {
//		return  date("Y-m-d H:i:s", strtotime($time));
		return  str_replace(date(" Y"), "", date("F j, Y H:i:s", strtotime($time)));
	}
	function atomic_timestamp($time) {
		return  date(DATE_ATOM, strtotime($time));
	}
	if (!function_exists('getallheaders')) 
	{ 
	  	function getallheaders() { 
	        foreach ($_SERVER as $name => $value) 
	       { 
	           if (substr($name, 0, 5) == 'HTTP_') 
	           { 
	               $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))); 
	               $headers[$name] = $value; 
	           } else if ($name == "CONTENT_TYPE") { 
	               $headers["Content-Type"] = $value; 
	           } else if ($name == "CONTENT_LENGTH") { 
	               $headers["Content-Length"] = $value; 
	           } 
	       } 
	       return $headers; 
	    } 
   	} 

    function flatten($a, $key = ""){
         $a = (array) $a;
	 $ret = array();
	 	foreach ($a as $el_k => $el_v) {
	        if(!is_array($el_v) && !is_object($el_v))
	        {
                if(empty($key))
                    $ret[$el_k] = $el_v;
                else
                    $ret[$key.".".$el_k] = $el_v;
	        }
	        else
	        {
                $el_v = (array) $el_v;
			 	foreach ($el_v as $el2_k => $el2_v) {
                    if(!is_array($el2_v) && !is_object($el2_v))
                    {
                        if(empty($key))
                            $ret[$el_k.".".$el2_k] = $el2_v;
                        else
                            $ret[$key.".".$el_k.".".$el2_k] = $el2_v;
                    }
                    else
                    {
                        $el2_v = (array) $el2_v;
                        if(empty($key))
                                $obj  =  flatten($el2_v, $el_k.".".$el2_k);
                        else
                                $obj  =  flatten($el2_v, $key.".".$el_k.".".$el2_k);

                        if(count($obj))
                        foreach($obj as $k => $v)
                        {
                                $ret[$k] = $v;
                        }
                    }
                }
	        }

	    }
        $ret2 = array();
        if(count($ret))
		foreach($ret as $k=>$v)
		{
            $k = strtolower($k);
            if($k == "actor.mbox" || $k == "actor.mbox.0")
            $ret2[$k] = str_replace("mailto:","",$v);
            else
            $ret2[$k] = $v;
        }
		return $ret2;
   }
	function keys_humanize($a) {
		$ret = array();
		foreach ($a as $key => $value) {
			if(empty($value))
			continue;

			if(strpos($key, "objecttype"))
			continue;

			$key = str_replace(".mbox.0", " Email", $key);
			$key = str_replace(".name.0", " Name", $key);
			$key = str_replace("mbox", "Email", $key);
			$key = str_replace("context.contextactivities.", "", $key);
			$key = str_replace("definition", "", $key);
			$key = str_replace(".und", "", $key);
			$key = str_replace("display", "Name", $key);
			$key = str_replace("en-us", "", $key);
			$key = str_replace(".0.", " ", $key);
			$key = str_replace(".", " ", $key);
			
			if($key == "id")
				$key = "Statement ID";
			
			if($key == "timestamp" || $key == "stored")
				$value = readable_timestamp($value);
			$ret[$key] = $value;			
		}
		return $ret;
	}
    function file_get_contents_curl($url) {
            $url = str_replace(" ", "%20", $url);
            $url = str_replace("(", "%28", $url);
            $url = str_replace(")", "%29", $url);
            $ch = curl_init();
            $timeout = 5;
        	$userAgent = !empty($_SERVER["HTTP_USER_AGENT"])? $_SERVER["HTTP_USER_AGENT"]:"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)";
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            if(!ini_get('safe_mode') && !ini_get('open_basedir'))
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			//curl_setopt($ch, CURLOPT_VERBOSE, true); $verbose = fopen('php://temp', 'w+'); curl_setopt($ch, CURLOPT_STDERR, $verbose); VERBOSE DEBUG
            $data = curl_exec($ch);
			/*
			if(curl_errno($ch))
			{
				return array('error' => curl_error($ch));
			}
				
			if ($result === FALSE) {
	 			printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
				htmlspecialchars(curl_error($ch)));
			}
			rewind($verbose);
			$verboseLog = stream_get_contents($verbose);
			echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
			*/
            curl_close($ch);
            return $data;
    }
    function gb_file_get_contents($url) {
		if(function_exists('curl_version'))
			return file_get_contents_curl($url);
		else
			return file_get_contents_fopen($url);    
    }
    function file_get_contents_fopen($url) {

		$streamopt = array(
			'ssl' => array(
				'verify-peer' => false,
			),
			'http' => array(
				'method' => 'GET',
				'ignore_errors' => true,
				'max_redirects' => '20'
			),
		);

		$context = stream_context_create($streamopt);
		$stream = fopen($url, 'rb', false, $context);
		$ret = stream_get_contents($stream);
		$meta = stream_get_meta_data($stream);
		return $ret;
    }
    function post_to_url_post($url, $data = array(), $return_info = false) {
    	return post_to_url($url, $data, "POST", $return_info);
    }
    function post_to_url_get($url, $data = array(), $return_info = false) {
    	return post_to_url($url, $data, "GET", $return_info);
    }
	function post_to_url($url, $data = array(), $method = "POST", $return_info = false)
	{
		if(function_exists('curl_version'))
			return post_to_url_curl($url, $data, $method, $return_info);
		else
			return post_to_url_fopen($url, $data, $method, $return_info);
	}
	function post_to_url_fopen($url, $data, $method = "POST", $return_info = false)
	{
		$content = http_build_query($data, '', '&');

		if($method == "GET") {
			$url = (strpos($url, "?") === false)? $url."?".$content:$url."&".$content; 	
		}

		$streamopt = array(
			'ssl' => array(
				'verify-peer' => false,
			),
			'http' => array(
				'method' => $method,
				'ignore_errors' => true,
				'max_redirects' => '3',
				'header' =>  array(
					'Content-Type: application/x-www-form-urlencoded',
					'Accept: application/x-www-form-urlencoded, */*; q=0.01',
				),
				'content' => http_build_query($data),
			),
		);

		$context = stream_context_create($streamopt);
		$stream = fopen($url, 'rb', false, $context);
		$ret = stream_get_contents($stream);
		if(!empty($return_info)) {
			$info = $http_response_header;
			if(preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", implode("\n", $info), $out )) {
				$info["http_code"] = $out[1];
			}
			return array("return" => $ret, "info" => $info);
		}

		//	$meta = stream_get_meta_data($stream);
		return $ret;
	}
	function post_to_url_curl($url, $data, $method = "POST", $return_info = false) {
		  $content = http_build_query($data, '', '&');
		
		  $ch = curl_init ();
		  
		  if($method == "GET") {
		  	$url = (strpos($url, "?") === false)? $url."?".$content:$url."&".$content; 	
		  }
		  else
		  {
			  curl_setopt($ch,CURLOPT_POST,1);
			  curl_setopt($ch,CURLOPT_POSTFIELDS,$content);			  	
		  }
		  
		  curl_setopt($ch,CURLOPT_URL,$url);
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//		  curl_setopt($ch,CURLOPT_USERPWD,"$username:$password");
		  curl_setopt($ch,CURLOPT_USERAGENT, @$_SERVER['HTTP_USER_AGENT']);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 30); //times out after 15s
		  if(!ini_get('safe_mode') && !ini_get('open_basedir'))
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		  //curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//					'Authorization: '.$auth,
					'Content-Type:  application/x-www-form-urlencoded',
					'Accept: application/x-www-form-urlencoded, */*; q=0.01',
//					'X-Experience-API-Version: '.$version
				));		 
		  if (!empty($_SERVER['HTTP_REFERER']))
			curl_setopt($ch,CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
		//curl_setopt($ch, CURLOPT_VERBOSE, true); $verbose = fopen('php://temp', 'w+'); curl_setopt($ch, CURLOPT_STDERR, $verbose); VERBOSE DEBUG
 
		  $result = curl_exec ($ch);

		  if(curl_errno($ch))
		  {
				return array('error' => curl_error($ch), 'info' => curl_getinfo($ch));
		  }
		/*	
		if ($result === FALSE) {
 			printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
			htmlspecialchars(curl_error($ch)));
		}
		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
		*/
		if( !empty($return_info) )
		{
		  	$info = curl_getinfo($ch);
		  	curl_close($ch); 
			return array("return" => $result, "info" => $info);
		}

		  curl_close($ch); 
  		  return $result;
	}
	function grassblade_config_get($key, $default = '') {
		if(defined("GBDB_DATABASE_CONNECT_ERROR"))
			return;
		App::import('Model', 'Config');
		$Config = new Config();
		$config_item = $Config->find("first", array("conditions" => array("key" => trim($key) )));

		if(empty($config_item["Config"]["value"]))
			return $default;
		else
			return maybe_unserialize($config_item["Config"]["value"]);
	}
	function grassblade_config_set($key, $value) {
		if(defined("GBDB_DATABASE_CONNECT_ERROR"))
			return;
		$value = maybe_serialize($value);
		App::import('Model', 'Config');
		$Config = new Config();
		$config_item = $Config->find("first", array("conditions" => array("key" => trim($key) )));
		$id = empty($config_item["Config"]["id"])? 0:$config_item["Config"]["id"];
		$data = array(
					"id"	=> $id,
					"key"	=> $key,
					"value"	=> $value
				);
		return $Config->save($data);
	}
	function grassblade_config_delete($key) {
		if(defined("GBDB_DATABASE_CONNECT_ERROR") || empty($key))
			return;

		App::import('Model', 'Config');
		$Config = new Config();
		$config_item = $Config->find("first", array("conditions" => array("key" => trim($key) )));
		$id = empty($config_item["Config"]["id"])? 0:$config_item["Config"]["id"];
		if(empty($id))
			return;
		$data = array(
					"id"	=> $id,
				);
		return $Config->delete($data);
	}
	function maybe_serialize( $data ) {
	        if ( is_array( $data ) || is_object( $data ) )
	                return serialize( $data );

	        if ( is_serialized( $data, false ) )
	                return serialize( $data );

	        return $data;
	}
	function maybe_unserialize( $original ) {
	        if ( is_serialized( $original ) ) 
	                return @unserialize( $original );
	        return $original;
	}
	function is_serialized( $data, $strict = true ) {
	        if ( ! is_string( $data ) ) {
	                return false;
	        }
	        $data = trim( $data );
	        if ( 'N;' == $data ) {
	                return true;
	        }
	        if ( strlen( $data ) < 4 ) {
	                return false;
	        }
	        if ( ':' !== $data[1] ) {
	                return false;
	        }
	        if ( $strict ) {
	                $lastc = substr( $data, -1 );
	                if ( ';' !== $lastc && '}' !== $lastc ) {
	                        return false;
	                }
	        } else {
	                $semicolon = strpos( $data, ';' );
	                $brace     = strpos( $data, '}' );
	                if ( false === $semicolon && false === $brace )
	                        return false;
	                if ( false !== $semicolon && $semicolon < 3 )
	                        return false;
	                if ( false !== $brace && $brace < 4 )
	                        return false;
	        }
	        $token = $data[0];
	        switch ( $token ) {
	                case 's' :
	                        if ( $strict ) {
	                                if ( '"' !== substr( $data, -2, 1 ) ) {
	                                        return false;
	                                }
	                        } elseif ( false === strpos( $data, '"' ) ) {
	                                return false;
	                        }
	                        // or else fall through
	                case 'a' :
	                case 'O' :
	                        return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
	                case 'b' :
	                case 'i' :
	                case 'd' :
	                        $end = $strict ? '$' : '';
	                        return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	        }
	        return false;
	}


	function grassblade_latest_version() {
		$latest_version = grassblade_config_get("latest_version");
		if(!defined("VERSION_CHECK_AFTER"))
			define("VERSION_CHECK_AFTER", 1*24*60*60);

		if(empty($latest_version["time"]) || time() > $latest_version["time"] + VERSION_CHECK_AFTER || !empty($_REQUEST['force-check']) && $_REQUEST['force-check'] == "version")
		{
			$latest_version = array(
					"time"	=> time(),
					"version"	=> grassblade_license("version")
				);
			if(!empty($latest_version["version"]) && strlen($latest_version["version"]) < 15)
			{
				grassblade_config_set("latest_version", $latest_version);
			}
			else if(!empty($_REQUEST['force-check']) && $_REQUEST['force-check'] == "version")
				 grassblade_config_set("latest_version", "");
		}
		return $latest_version["version"];
	}
	function grassblade_show_version_message() {
		$latest_version = grassblade_latest_version();
		if(!is_string( $latest_version) || $latest_version <= LRS_VERSION)
			return "";

		return '<div id="new_version_available"><a href="https://www.nextsoftwaresolutions.com/my-account/" target="_blank">'.sprintf(__("A new version v%s of GrassBlade LRS is available."), $latest_version)."</a></div>";
	}
	function grassblade_license($action, $email = false, $key = false) {
		if(empty($email) || empty($key))
		{
			$license = grassblade_load_license();
			if(empty($license) || empty($license["email"]) || empty($license["key"]))
				return "";
			$email = $license["email"];
			$key = $license["key"];
		}
		$domain = str_replace(array("http://", "https://"), "", Router::url('/', true));
		$url = "license.nextsoftwaresolutions.com?pluginupdate=grassblade_lrs&action=".$action."&licensekey=".urlencode($key)."&licenseemail=".urlencode($email)."&licensedomain=".urlencode($domain)."&nsspu_wpurl=".urlencode($domain)."&nsspu_admin=&current_version=".LRS_VERSION;
		return post_to_url_post($url, array("action" => $action));
	}
	function grassblade_license_check($email, $key) {
		$ret =  grassblade_license("license", $email, $key);
		
		if(empty($ret) || is_array($ret) || $ret == "false" || $ret == "not_found" || strlen($ret) > 20)
			return false;
		else if($ret == "clear_license")
		{
			grassblade_clear_license();
			return false;
		}
		else
			return grassblade_save_license_file($email, $key);
		
	}

	function grassblade_save_license_file($e, $k) {
		$d = str_replace(array("http://", "https://"), "", Router::url('/', true));
		$e = base64_encode($e);
		$lf = md5($d);
		$t = time();
		$c = '#&$^^@(@';
		$k = base64_encode($k);
		$m	= md5('#&$^^@(@'.$d.$e.$k.$t);
		$l = array(
					"e"	=> $e,
					"k"	=> $k,
					"lf" => $lf,
					"t"	=> $t,
					"v"	=> LRS_VERSION,
					"m"	=> $m
				);
		$l = json_encode($l);
		$l = $m.$c.base64_encode($l);
		$lfn = APP."Config".DS.$lf.".license";
		$ret = file_put_contents($lfn, $l);
		if(empty($ret))
		{
			$lfn_alt = APP."tmp".DS.$lf.".license";
			$ret = file_put_contents($lfn_alt, $l);
			if(empty($ret))
			return "LFC200";
			else
			return "LST200";
		}	
		return "LSC200";
	}
	function grassblade_clear_license() {
		$d = str_replace(array("http://", "https://"), "", Router::url('/', true));
		$lf = md5($d); 
		$lfn = APP."Config".DS.$lf.".license";
		$lfn_alt = APP."tmp".DS.$lf.".license";
		if(file_exists($lfn))
			unlink($lfn);
		if(file_exists($lfn_alt))
			unlink($lfn_alt);			
	}
	function grassblade_load_license($v = false) {
		$d = str_replace(array("http://", "https://"), "", Router::url('/', true));
		$lf = md5($d); 
		$t = time();
		$c = '#&$^^@(@';
		$lfn = APP."Config".DS.$lf.".license";
		$lfn_alt = APP."tmp".DS.$lf.".license";
		if(file_exists($lfn))
		$l = file_get_contents($lfn);
		else if(file_exists($lfn_alt))
		$l = file_get_contents($lfn_alt);

		if(empty($l))
			return false;
		
		$lp = explode($c, $l);

		if(empty($lp[0]) || empty($lp[1]))
			return false;

		$l = $lp[1];
		$l = trim(base64_decode($l)); 
		$l = (array) json_decode($l);
		
		if(empty($l["e"]) || empty($l['k']) || empty($l['lf']) || empty($l['t']) || empty($l['v']) || empty($l['m']))
			return false;

		if($l["t"] + 432000 < $t)
			$v = true;

		if($lf != $l['lf'])
			return false;

		$mt = md5($c.$d.$l["e"].$l["k"].$l["t"]);

		if($mt != $lp[0])
			return false;

		$k = base64_decode($l["k"]);
		$l["k"] = $l["key"] = $k;

		$e = base64_decode($l["e"]);
		$l["email"] = $l["e"]	= $e;

		if($v) {
			$vs = grassblade_license_check($l["e"], $l["k"]);
			if(empty($vs) || $vs == "LFC200")
				return false;
			else
				return grassblade_load_license();
		}

		return $l;
	}
    function grassblade_verify_db($host, $user, $pass, $db)
    {
        $g_link = mysqli_connect($host, $user, $pass);
        if(empty($g_link))
        return 1;//Could not connect to mysql server
        $db_select = mysqli_select_db($g_link, $db);
        if(empty($db_select))
        return 2;//Could not select database
        return 0;//Details verified successfully
    }
    function grassblade_global_permission() {
    	$global_permissions = array();
    	$global_permissions["all"] = array(
    									"label" => __("All"),
    									"allow" => true,
    									"roles" => array("admin" => "Admin", "user" => "User"),
    								);
     	$global_permissions["view_all_data"] = array(
    									"label" => __("View All Data"),
    									"feature" => "data",
    									"allow" => true,
    								);   	
    	$global_permissions["view_own_data"] = array(
    									"label" => __("View Own Data"),
    									"feature" => "data",
    									"allow" => true,
    								);
    	$global_permissions["view_group_data"] = array(
    									"label" => __("View Group Data"),
    									"feature" => "data",
    									"allow" => true,
    								);
		$global_permissions["view_all_filters"] = array(
    									"label" => __("View All Filters"),
    									"controller" => "Reports",
    									"allow" => true,
    									"roles" => array("admin" => "Admin", "user" => "User"),
    								);
    	$global_permissions["manage_users"] = array(
    									"label" => __("Manage Users"),
    									"controller" => "Users",
    									"allow" => true,
    									"roles" => array("admin" => "Admin"),
    								);
    	$global_permissions["manage_groups"] = array(
    									"label" => __("Manage Groups"),
    									"controller" => "Groups",
    									"allow" => true,
    									"roles" => array("admin" => "Admin"),
    								);
    	$global_permissions["manage_triggers"] = array(
    									"label" => __("Manage Triggers"),
    									"controller" => "Triggers",
    									"allow" => true,
    								);
		$global_permissions["manage_configure"] = array(
    									"label" => __("Manage Configuration"),
    									"controller" => "Configure",
    									"allow" => true,
    								);
    	$global_permissions["view_error_logs"] = array(
    									"label" => __("View Error Logs"),
    									"controller" => "ErrorLogs",
    									"allow" => true,
    								);
			
/*    	$global_permissions["disallow_sso"] = array(
    									"label" => __("Disallow SSO"),
    									"feature" => "sso",
    									"allow" => false
    								);

    	$global_permissions["add_user"] = array(
    									"label" => __("Add User"),
    									"controller" => "User",
    									"action" => "add",
    									"allow" => true
    								);
    	$global_permissions["edit_user"] = array(
    									"label" => __("Edit User"),
    									"controller" => "User",
    									"action" => "edit",
    									"allow" => true
    								);
    	$global_permissions["edit_user_self"] = array(
    									"label" => __("Edit User (Self)"),
    									"controller" => "User",
    									"action" => "edit",
    									"id"	=> "self",
    									"allow" => true
    								);
       	$global_permissions["view_user_list"] = array(
    									"label" => __("View User List"),
    									"controller" => "User",
    									"action" => "index",
    									"allow" => true
    								);
*/
    	return $global_permissions;
    }


    function check_permission($permission, $user_id = null) {
    	global $global_permissions;
    	if(empty($user_id))
    		$user_id = User::get("id");

    	$role = User::get("role");
    	if($role == "admin") {
    		if(empty($global_permissions[$permission]["roles"]["admin"]))
    		return true;
    	}

    	$user_permissions = User::get("permissions");

    	if(empty($user_permissions))
    		return true;

    	$user_permissions = json_decode($user_permissions);

    	if(!in_array($permission, $user_permissions)) {
	    	if(in_array("all", $user_permissions))
	    		$return = true;
	    	else
	    		$return = false;
    	}
    	else
    		$return = true;

    	return modified("check_permission", $return, $permission, $user_id);
    }
    modify("init", "gb_manage_controller_permissions", 10, 2);
    function gb_manage_controller_permissions($r, $context) {
    	$controller = @$context->request->params["controller"];
    	$action = @$context->request->params["action"];

    	if(empty($controller) || in_array($action, array("login", "logout", "dashboard", "cron", "count")))
    		return;

    	if($controller."/".$action."/".@$context->request->params["pass"][0] == "Users/edit/".User::get("id"))
    		return;
    	
    	$permissions = array(
    			"Users" 	=> array("manage_users"),
    			"Triggers"	=> array("manage_triggers"),
    			"Groups"	=> array("manage_groups"),
    			"Configure"	=> array("manage_configure"),
    			"Reports"	=> array("view_group_data", "view_own_data", "view_all_data"),
    		);
    	if(!empty($permissions[$controller]))
    	{
			$remove = true;
			foreach ($permissions[$controller] as $permission) {
				if($p = check_permission($permission)) {
					$remove = false;
				}
			}
			if($remove)
			{
    			$context->redirect("/");
    			exit;				
			}
    	}
    }
	modify("grassblade_lrs_menu", "gb_remove_controller_links_without_permissions", 10, 2);
	function gb_remove_controller_links_without_permissions($menus, $context) {
    	$controller = @$context->request->params["controller"];

    	if(empty($controller))
    		return;

    	$permissions = array(
    			"users" 	=> array("manage_users"),
    			"triggers"	=> array("manage_triggers"),
    			"groups"	=> array("manage_groups"),
    			"configure"	=> array("manage_configure"),
    			"reports"	=> array("view_group_data", "view_own_data", "view_all_data"),
    		);	
    	foreach ($menus as $key => $menu) {
    		if(!empty($permissions[$key]))
    		{
    			$remove = true;
    			foreach ($permissions[$key] as $permission) {
    				if(check_permission($permission))
    					$remove = false;
    			}
    			if($remove)
    				unset($menus[$key]);
    		}
    	}
	    return $menus;
	}

    modify("check_permission", "modify_check_permission", 10, 3);
    function modify_check_permission($return, $permission, $user_id) {
    	if(empty($user_id))
    		return array();

    	if($permission == "view_group_data")
    	{
    		if(!$return)
    			return $return;

    		$email = User::get("email");
    		App::import("model", "Group");
    		$GroupModel = new Group();
    		return $GroupModel->groups_by_leader($email);
    	}
    	return $return;
    }
    function is_iis() {
    	return strpos(strtolower( @$_SERVER["SERVER_SOFTWARE"] ), "microsoft-iis") !== false;
    }
    function get_wordpress_client() {
    	if(!function_exists('curl_version'))
    		return __('cURL not installed/enabled. Please make sure cURL is installed and enabled for PHP.');

		$Integrations = grassblade_config_get("Configure_Integrations");
		$gb_xapi = @$Integrations["gb_xapi"];

		if(!empty($gb_xapi["url"]))
		{
	    	require_once(APP."Vendor".DS."wordpress-rest-api".DS."library".DS."Requests".DS."library".DS."Requests.php");
	    	require_once(APP."Vendor".DS."wordpress-rest-api".DS."library".DS."WPAPI.php");
			Requests::register_autoloader();
			$url = str_replace("//wp-json", "/wp-json", $gb_xapi['url']."/wp-json");
			$wp = new WPAPI($url, $gb_xapi['user'], $gb_xapi['pass']);
			$response = $wp->get("/grassblade/v1/getGroups?id=1");

			if( !empty( $response->success ) ) {
				$return = $wp;
			}
			else
			{
				if(@$response->status_code == 401)
					$return = "Invalid username or password.";
				else
					$return = get_wordpress_client_xmlrpc();
			}
			if(strlen($response->body) > 3000)
				$response->body = substr($response->body, 0, 3000);
			if(strlen($response->raw) > 3000)
				$response->raw = substr($response->raw, 0, 3000);

			if( is_string($return) ) {
				$error_data = array(
						"type"			=> "WordPressAPI",
						"user"			=> $gb_xapi["user"],
						"data"			=> array('_SERVER' => $_SERVER, '_REQUEST' => $_REQUEST, "json_api_response" => $response),
						"url"			=> $gb_xapi["url"],
						"request_method"=> "",
						"error_code" 	=> 0,
						"error_msg" 	=> $return,
						"status"		=> 0						
						);
				store_error_log($error_data);
			}
			return $return;
		}
    }
    function get_wordpress_client_xmlrpc() {
    	if(!function_exists("xmlrpc_encode_request"))
    		return __("Invalid WordPress URL. OR, WordPress does not support JSON REST API (Check WordPress version, is it older than v4.7?, or GrassBlade xAPI Companion version, is it older than 1.5.22?). <br>Testing alternative method: XML RPC Module not installed. Undefined function: xmlrpc_encode_request");
    	require_once(APP."Vendor".DS."xml-rpc".DS."WordpressClient.php");

		$Integrations = grassblade_config_get("Configure_Integrations");
		$gb_xapi = @$Integrations["gb_xapi"];
		if(!empty($gb_xapi["url"]))
		{
			try
			{
				$wp = new \HieuLe\WordpressXmlrpcClient\WordpressClient();
			    $wp->setCredentials($gb_xapi["url"].DS.'xmlrpc.php', $gb_xapi["user"], $gb_xapi["pass"]);
			    $wp->getOptions(array("home_url"));
				return $wp;
			}
			catch (Exception $e)
			{
				$code = $e->getCode();
				$message = $e->getMessage();
				if(strlen($message) > 100)
					return __("404 Not Found. Your wordpress url could be wrong.");
				else
				 return "Error Code: ". $code.", Error: ".$message;
			}
		}
		else
		return false; 	
    }
    function call_wordpress_api($wp, $fn, $params) {
    	if(get_class($wp) == "WPAPI") { //JSON REST API
    		try
    		{
	    		$response = $wp->get("/grassblade/v1/".$fn."?".http_build_query($params));
	    		if(!empty($response->success))
	    			return (array) json_decode( $response->body );

	    		$return = "Error: ".$response->status_code;
    		}
    		catch (Exception $e)
			{
				$code = $e->getCode();
				$message = $e->getMessage();
				 $return = "Error Code: ". $code.", Error: ".$message;
			}
    	}
    	else //XMLRPC
    	{
    		try
    		{
    			return $wp->callCustomMethodSec("grassblade.". $fn, $params);
    		}
    		catch (Exception $e)
			{
				$code = $e->getCode();
				$message = $e->getMessage();
				$return = "Error Code: ". $code.", Error: ".$message;
			}
    	}
   		$Integrations = grassblade_config_get("Configure_Integrations");
		$gb_xapi = @$Integrations["gb_xapi"];

		$error_data = array(
		"type"			=> "WordPressAPI",
		"user"			=> $gb_xapi["user"],
		"data"			=> array('_SERVER' => $_SERVER, '_REQUEST' => $_REQUEST),
		"url"			=> $gb_xapi["url"],
		"request_method"=> "",
		"error_code" 	=> @$code,
		"error_msg" 	=> $return,
		"status"		=> 0						
		);
		store_error_log($error_data);
		return $return;
    }
	function gb_validate($fields, $data) {

        if(!empty($fields))
        foreach ($fields as $field => $conditions) {
                if(!isset($data[$field]))
                if(!empty($conditions["conditions"]) && in_array("notempty", $conditions["conditions"]))
                        return "Error: ".$field." is empty";
                else
                        continue;

                $value = @$data[$field];
                if(!empty($conditions["type"]))
                {
//                      CakeLog::debug($field." : ".$conditions["type"]);
                    switch (strtolower($conditions["type"])) {
                        case 'string':
                            if(!is_string($value))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'int':
			    if(trim($value) != intVal($value))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'object':
                        case 'array':
                            $val = maybe_unserialize($value);
                            if(is_string($val))
                            $val = json_decode($value);
                            if(!is_object($val) && !is_array($val))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'iri':
                            if(!is_string($value) || !strpos($value, ":"))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'bool':
                            if(!is_bool($value) &&  strtolower($value) != "true" && strtolower($value) != "false")
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'timestamp':
                            if(!validate_timestamp($value))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        case 'uuid':
                            if(!validate_uuid($value))
                                    return "Error: ".$field." value [".print_r($value, true)."] is not of type: ".$conditions["type"].".";
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if(!empty($conditions["conditions"]["match"]) && !in_array($value, $conditions["conditions"]["match"]))
                    return "Error: ".$field." value [".print_r($value, true)."] is not a valid value";

//              CakeLog::debug($field." : ".print_r(@$conditions["conditions"], true));
                if(!empty($conditions["conditions"]) && in_array("alone", $conditions["conditions"]))
                {
//					CakeLog::debug($field." check for alone");
                    foreach ($fields as $field2 => $conditions2) {
                        if($field != $field2 && !empty($data[$field2]))
                        {
                            return "Error: ".$field." and ".$field2 ." not allowed together";
                        }
                        else
                        {
//                             	CakeLog::debug($field." != ".$field2. " && !empty: ". print_r(@$data[$field2], true));
                        }
                    }
                }
         		if(!empty($conditions["conditions"]) && !empty($conditions["conditions"]["alone_except"]))
                {
//					CakeLog::debug($field." check for alone");
                    foreach ($fields as $field2 => $conditions2) {
                        if($field != $field2 && !empty($data[$field2]) && !in_array($field2, $conditions["conditions"]["alone_except"]))
                        {
                            return "Error: ".$field." and ".$field2 ." not allowed together";
                        }
                        else
                        {
//                             	CakeLog::debug($field." != ".$field2. " && !empty: ". print_r(@$data[$field2], true));
                        }
                    }
                }       
        }
        return true;
	}
	function validate_uuid($uuid) {
		return preg_match('/^\{?[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}\}?$/', $uuid); 
	}
	function validate_timestamp($date)
	{
	    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{1,3})?Z$/', $date, $parts) == true) {
	        $time = gmmktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);

	        $input_time = strtotime($date);
	        if ($input_time === false) return false;

	        return $input_time == $time;
	    } else {
	        return false;
	    }
	}

function gb_json_decode($json) {
	$return = json_decode($json);

	if(empty($return) && json_last_error() == JSON_ERROR_UTF8)
	$return = json_decode(utf8_encode($json));
	
	return $return;
} 

function gb_mail($to = null, $subject = null, $message = null, $from = null, $attachements = null) {

	App::uses('CakeEmail', 'Network/Email');

	App::import('model','Config');
	$config_method = new config();
	$data = $config_method->find("all", array('conditions' =>  array('key' => 'Configure_Integrations') )); 
	$config_data = unserialize($data[0]['Config']['value']);
        $config_data["email"] = modified("email_configuration", @$config_data['email']);

	if(empty($config_data['email']['server']))
	{
		CakeLog::debug("Email not configured");
		return false;
	}
	$config = array(
	    'port'      => $config_data['email']['port'],
	    'timeout'   => 30,
	    'host'      => $config_data['email']['server'],
	    'transport' => 'Smtp'
	);

	if(!empty( $config_data['email']['user']))
	$config['username'] = $config_data['email']['user'];

	if(!empty( $config_data['email']['pass']))
	$config['password'] = $config_data['email']['pass'];

	$Email = new CakeEmail($config);

	if (empty($from)) {
		$from = $config_data['email']['from'];
	}
	try {    
		$Email->emailFormat('html')
	      ->from($from)
	      ->to($to)
	      ->subject($subject)    
	      ->attachments($attachements);  
		if($Email->send($message)) {
			return true;
		}else {
			return false;
		}
	}
	catch(Exception $e) {
		CakeLog::debug("Email not configured correctly.");
		return false;
	}
}
function gb_agent_array($agent) {
	$agent_array = array("agent_name" => "", "agent_mbox" => "");
	if(!empty($agent->name)) {
		$agent_array['agent_name'] = is_array($agent->name)? $agent->name[0]:$agent->name;
	}
	if(!empty($agent->mbox)){
		$mbox = is_array($agent->mbox)? $agent->mbox[0]:$agent->mbox;
		$email = str_replace("mailto:", "", $mbox);
	
		if(!empty($mbox))
			$agent_array['agent_mbox'] = $email;
		
		$agent_array['agent_id'] = $email;
	}
	if(!empty($agent->mbox_sha1sum)) {
		$agent_array['agent_mbox_sha1sum'] = $agent->mbox_sha1sum;
		$agent_array['agent_id'] = $agent->mbox_sha1sum;
	}
	if(!empty($agent->openid)) {
		$agent_array['agent_openid'] = $agent->openid;
		$agent_array['agent_id'] = $agent->openid;
	}
	if(!empty($agent->account->homePage)) {
		$agent_array['agent_account_homePage'] = $agent->account->homePage;
		$agent_array['agent_id'] = $agent->account->homePage;
	}
	if(!empty($agent->account->name)) {
		$agent_array['agent_account_name'] = $agent->account->name;
		$agent_array['agent_id'] = @$agent_array['agent_id']."/".$agent->account->name;
	}
	return $agent_array;
}
function gb_parent_ids($statement) {
	$parents = array();
	if(!empty($statement->{"context"}->contextActivities->{"parent"})) {
		$parents = array();
		$parent = $statement->{"context"}->contextActivities->{"parent"};
		if(is_array($parent)) {
			foreach($parent as $p) {
				if(!empty($p->id))
				$parents[] = $p->id;
			}
		}
		else
		if(!empty($parent->id))
			$parents[] = $parent->id;
	}
	return $parents;
}
function gb_grouping_ids($statement) {
	$groupings = array();
	if(!empty($statement->{"context"}->contextActivities->{"grouping"})) {
		$groupings = array();
		$grouping = $statement->{"context"}->contextActivities->{"grouping"};
		if(is_array($grouping)) {
			foreach($grouping as $p) {
				if(!empty($p->id))
				$groupings[] = $p->id;
			}
		}
		else
		if(!empty($grouping->id))
			$groupings[] = $grouping->id;
	}
	return $groupings;
}
function gb_secure_tokens($UsersAuth, $Input, $Auth) {
//	echo "<pre>";
	$Auth = explode(":", base64_decode(str_replace("Basic ", "", $Auth)));
	if(count($Auth) != 2)
		return "";
	$api_user = $Auth[0];
	$api_pass = $Auth[1];
//	print_r($Auth);
	$token = gb_secure_tokens_decode($api_pass);
//	print_r($token);
	if(empty($token) || empty($token["sha"]) || strlen($token["sha"]) != 40 || empty($token["secure_tokens"])  || empty($token["password_4"]))
		return "";

	$token["token"] = $api_pass;
	$password_4 = $token["password_4"];
	$auths = $UsersAuth->find("all", array("conditions" => array("api_user" => $api_user,  "api_pass LIKE " => $password_4."%")));
	if(empty($auths))
		return "";

	if($token["secure_tokens"] & 8 && ($token["timestamp"] > time() + 3600 || ($token["timestamp"] + $token["duration"] < time() ))) {
	        $Output = new Output();
		$Output->send(401, "Bad auth token.");
	}
	global $secure_tokens;
	$secure_tokens = grassblade_config_get("token_".$api_pass);
	
	if(empty($secure_tokens))
	$secure_tokens = $token;

	modify("before_save", "gb_secure_tokens_check_before_save", 10, 3);
	modify("after_get", "gb_secure_tokens_check_after_get", 10, 3); 
	modify("before_delete", "gb_secure_tokens_check_after_get", 10, 3); 

	return $auths[0]["UsersAuth"];
}
function gb_secure_tokens_check_before_save($data, $type, $Input) {
	global $secure_tokens;
	$types = array("statements", "activities/state", "agents/profile", "activities/profile");
	if(empty($secure_tokens) || empty($secure_tokens["secure_tokens"]) || !in_array($type, $types))
		return $data;

	$Output = new Output();
	$auth = User::get("auth");
	$token = $secure_tokens["token"];
	$pass = substr($token, 40);
	$pass = $auth["auth"]["api_pass"].$pass;
	$pass_array = array();
	if($secure_tokens["secure_tokens"] & 1) {
		if(!empty($data["agent"])) {
			$agent = $data["agent"];
			$agent = gb_json_decode($agent);
			$agent_array = gb_agent_array($agent);
			$agent_id = @$agent_array["agent_id"];
		}
		else
		if(!empty($data["agent_id"]))
			$agent_id = $data["agent_id"];

		if($type != "activities/profile" && empty($data["agent_id"])) //Agent not required for activities/profile, required for all others
			$Output->send(401, "Bad auth token.");

		$pass_array[] = $pass = $pass.$agent_id;

		if(!empty($secure_tokens["permissions"]["agent_id"])) {
			if($agent_id != $secure_tokens["permissions"]["agent_id"])
				$Output->send(401, "Bad auth token.");
		}
	}
	if($secure_tokens["secure_tokens"] & 2) {
		$activity_id = array();
		if(!empty($data["activityId"]))
			$activity_id[] = $data["activityId"];

		if(!empty($data["objectid"]))
			$activity_id[] = $data["objectid"];
		
		if(!empty($data["parent_ids"]))
		{
			$activity_id = array_merge($activity_id, explode(",", $data["parent_ids"]));
		}
		if(!empty($data["grouping_ids"]))
		{
			$activity_id = array_merge($activity_id, explode(",", $data["grouping_ids"]));
		}
		if($type != "agents/profile" && empty($activity_id)) //Activity not required for Agent Profile. Required for all others
			$Output->send(401, "Bad auth token.");

		$pass_array = array();
		foreach ($activity_id as $k => $id) {
			$pass_array[$k] = $pass.$id;
		}
		if(!empty($secure_tokens["permissions"]["activity_id"])) {
			if(!in_array($secure_tokens["permissions"]["activity_id"], $activity_id))
				$Output->send(401, "Bad auth token.");
		}
	}
	if($secure_tokens["secure_tokens"] & 4) {
		$IP = @$_SERVER["REMOTE_ADDR"];
		
		if(!empty($secure_tokens["permissions"]["IP"])) {
			if($IP != $secure_tokens["permissions"]["IP"])
				$Output->send(401, "Bad auth token.");
		}
		foreach ($pass_array as $k => $v) {
			$pass_array[$k] = $v.$IP;
		}
	}

	if(!empty($secure_tokens["permissions"]))
		return $data;

	foreach ($pass_array as $pass) {
		if($secure_tokens["sha"] == sha1($pass))
		{
			$secure_tokens["permissions"] = array(
											"agent_id" => @$agent_id,
											"activity_id" => @$activity_id[$k],
											"IP"	=> @$IP,
										);
			$secure_tokens["timestamp"] = time();
			grassblade_config_set("token_".$token, $secure_tokens);

			return $data;
		}
	}
	$Output->send(401, "Bad auth token.");
}
function gb_secure_tokens_check_after_get($data, $type, $Input) {
	global $secure_tokens;
	$types = array("statements", "activities/state", "agents/profile", "activities/profile");
	if(empty($secure_tokens) || empty($secure_tokens["secure_tokens"]) || !in_array($type, $types))
		return $data;


	$Output = new Output();
	$auth = User::get("auth");
	$token = $secure_tokens["token"];
	$pass = substr($token, 40);
	$pass = $auth["auth"]["api_pass"].$pass;
	$pass_array = array();

	if($type == "statements" && is_string($data))
	{
		$statement = gb_json_decode($data);
	}

	if($secure_tokens["secure_tokens"] & 1) {
		$agent = @$Input->params["agent"];
		if(!empty($agent)) {
			$agent = gb_json_decode($agent);
			$agent_array = gb_agent_array($agent);
			$agent_id = @$agent_array["agent_id"];
		}
		if(empty($agent_id) && !empty($statement->actor))
		{
			$agent_array = gb_agent_array($statement->actor);
			$agent_id = @$agent_array["agent_id"];
		}
		if(empty($agent_id))
			$Output->send(401, "Bad auth token..");

		$pass_array[] = $pass = $pass.$agent_id;
	
		if(!empty($secure_tokens["permissions"]["agent_id"])) {
			if($agent_id != $secure_tokens["permissions"]["agent_id"])
				$Output->send(401, "Bad auth token...");
		}
	}
	if($secure_tokens["secure_tokens"] & 2) {
		$activity_id = array(); 
		if(!empty($Input->params["activityId"]))
		$activity_id[] = $Input->params["activityId"];

		if(!empty($Input->params["activity"]))
		$activity_id[] = $Input->params["activity"];

		if(empty($activity_id[0]) && !empty($statement))
		{
			$activity_id[] = @$statement->{"object"}->{"id"};

			$parent_ids = gb_parent_ids($statement);

			$activity_id = array_merge($activity_id, $parent_ids);

			$grouping_ids = gb_grouping_ids($statement);
			$activity_id = array_merge($activity_id, $grouping_ids);
			
		}

		if(empty($activity_id[0]))
			$Output->send(401, "Bad auth token.".print_r($statement, true));

		if(!empty($secure_tokens["permissions"]["activity_id"])) {
			if(!in_array($secure_tokens["permissions"]["activity_id"], $activity_id))
				$Output->send(401, "Bad auth token.");
		}

		$pass_array = array();
		foreach ($activity_id as $k => $id) {
			$pass_array[$k] = $pass.$id;
		}
	}
	if($secure_tokens["secure_tokens"] & 4) {
		$IP = @$_SERVER["REMOTE_ADDR"];
		
		if(!empty($secure_tokens["permissions"]["IP"])) {
			if($IP != $secure_tokens["permissions"]["IP"])
				$Output->send(401, "Bad auth token.");
		}
		foreach ($pass_array as $k => $v) {
			$pass_array[$k] = $v.$IP;
		}
	}

	if(!empty($secure_tokens["permissions"]))
		return $data;

	foreach ($pass_array as $pass) {
		if($secure_tokens["sha"] == sha1($pass))
		{
			$secure_tokens["permissions"] = array(
											"agent_id" => @$agent_id,
											"activity_id" => @$activity_id[$k],
											"IP"	=> @$IP,
										);
			$secure_tokens["timestamp"] = time();
			grassblade_config_set("token_".$token, $secure_tokens);

			return $data;
		}
	}
	$Output->send(401, "Bad auth token.");
}
function gb_secure_tokens_decode($pass) {
	$return = array();

	$return["sha"] = substr($pass, 0, 40);
	$pass = substr($pass, 40);
	$return["password_4"] = substr($pass, 0, 4);
	$return["secure_tokens"] = intval(substr($pass, 4, 3));
	$return["timestamp"] = intval(substr($pass, 7, 10));
	$return["duration"] = intval(substr($pass, 17));
	return $return;
}
function gb_get_filters($page = "") {
	if(defined("GBDB_DATABASE_CONNECT_ERROR"))
		return array();

	App::import('Model', 'Config');
	$Config = new Config();
	$config_items = $Config->find("all", array("conditions" => array("key LIKE " => "filter_%")));
//return $config_items;
	if(empty($config_items))
		return array();
	$filters = array();
	foreach ($config_items as $config_item) {
		$value = maybe_unserialize($config_item["Config"]["value"]);
		$key = $config_item["Config"]["key"];
		if(strpos("a".$key, "filter_") != 1)
			continue;
		
		if(empty($page) || strtolower($page) == strtolower(@$value["controller"]."/".@$value["action"]))
		$filters[$config_item["Config"]["key"]] = $value;
	}
	return $filters;
}
function gb_get_filter($filter_id) {
	return grassblade_config_get("filter_".$filter_id);
}

function gb_get_filter_emails($id = null)
{
	if(defined("GBDB_DATABASE_CONNECT_ERROR"))
		return array();

	if(!empty($id))
		return grassblade_config_get("filter_emails_".$id);

	App::import('Model', 'Config');
	$Config = new Config();
	$config_items = $Config->find("all", array("conditions" => array("key LIKE " => "filter_emails_%")));

	if(empty($config_items))
		return array();
	$filter_emails = array();
	foreach ($config_items as $config_item) {
		$value = maybe_unserialize($config_item["Config"]["value"]);
		$key = $config_item["Config"]["key"];
		$key_int = str_replace("filter_emails_", "", $key);
		if(empty($key_int) || intval($key_int) != $key_int)
			continue;

		$filter_emails[$config_item["Config"]["key"]] = $value;
	}
	return $filter_emails;
}
function gb_set_filter_emails($filter_id, $filter_emails) {
	grassblade_config_set("filter_emails_".$filter_id, $filter_emails);
}
function gb_get_filter_url($filter_id) {
	$filter = grassblade_config_get("filter_".$filter_id);
	if(empty($filter))
		return "";
	
	$url = Router::url(array(
								"controller" 	=> $filter["controller"],
								"action"		=> $filter["action"]
							), true);
	return strpos('a'.$url, "?")? $url."&filter_id=".$filter_id:$url."?filter_id=".$filter_id;
}

function gb_get_filter_email_timestamp($filter_email) {
    if(!is_array($filter_email))
        $filter_email = gb_get_filter_emails($filter_email);

    if(empty($filter_email["freq"]) && empty($filter_email["freq_text"]))
        return 0;

    if(empty($filter_email['freq']))
        return strtotime($filter_email['freq_text']);

    $freq = $filter_email['freq'];
    $freq_text = $filter_email['freq_text'];

    switch ($freq) {
        case 'every_hour':
            return strtotime(date("H:00:00"));
            break;
        case 'every_day':
            /*
                10:00AM     // 10:00 AM every day
            */
            if(strtotime($freq_text) <= 0)
                return strtotime('today');

            $time = strtotime($freq_text, strtotime('today'));
            if($time > time())
            $time = strtotime($freq_text, strtotime('yesterday'));
            
            if($time <= 0)
                return strtotime("today");
            else
                return $time;
            break;

        case 'every_week':
            /*
                blank                   //Default Monday 00:00AM
                tuesday 2:00PM          //Tuesday 2:00PM Every Week
            */
            if(empty($freq_text))
                return strtotime("monday");
            
            $timestamp = strtotime($freq_text, strtotime('monday'));
            if($timestamp > time())
            $timestamp = strtotime($freq_text, strtotime('last monday'));

            return $timestamp;
            break;

        case 'every_month':
        /*
            tuesday 2:00PM              //First Tuesday of month at 2:00PM
            third tuesday 2:00PM        //Third Tuesday of month at 2:00PM
            +1days 2:00PM               //2nd day of month at 2:00PM
        */
            $timestamp_start_of_month = strtotime(date("Y-m-1"));

            if(empty($freq_text))
                return $timestamp_start_of_month;
        
            $freq_text = explode(",", $freq_text);
            $time = $timestamp_start_of_month;
            foreach ($freq_text as $fr) {
                if(strtotime($fr) > 0)
                {
                    $time = strtotime($fr, $time);
                }
            }

            if(strtotime("+1 month", $timestamp_start_of_month) - $time < 24*60*60)
            {

                $timestamp_start_of_month = strtotime("-1 month", strtotime(date("Y-m-1")));
                $time = $timestamp_start_of_month;
                foreach ($freq_text as $fr) {
                    if(strtotime($fr) > 0)
                    {
                        $time = strtotime($fr, $time);
                    }
                }

            }           
            return $time;
            break;

        case 'every_year':
             /*
                third tuesday 2:00PM        //Third Tuesday at 2:00PM
                +99days 2:00PM              //100th day of year at 2:00PM
                feb 10, 2:00PM              //Feb 10 at 2:00PM
                february 10, 2:00PM         //Feb 10 at 2:00PM
                10 feb, 2:00PM              //Feb 10 at 2:00PM
                feb 10, saturday, 2:00PM    //first saturday after Feb 10, at 2:00PM

            */
           $timestamp_start_of_year = strtotime(date("Y-1-1"));

            if(empty($freq_text))
                return $timestamp_start_of_year;
            
            $freq_text = explode(",", $freq_text);
            $time = $timestamp_start_of_year;
            foreach ($freq_text as $fr) {
                if(strtotime($fr) > 0)
                {
                    $time = strtotime($fr, $time);
                }
            }

            if(strtotime("+1 year", $timestamp_start_of_year) - $time < 48*60*60)
            {

                $timestamp_start_of_year = strtotime("-1 year", strtotime(date("Y-1-1")));
                $time = $timestamp_start_of_year;
                foreach ($freq_text as $fr) {
                    if(strtotime($fr) > 0)
                    {
                        $time = strtotime($fr, $time);
                    }
                }
            }
            return $time;
            break;

        default:
            # code...
            break;
    }
    return 0;
}
function gb_print($msg, $role = "", $pre = false) {
	$user_role = User::get("role");

	if($pre) echo "\n<br><pre>";
	if($user_role == "admin")
	{
		print_r($msg);
	}
	switch ($role) {
		case 'admin':
			return;
			break;
		case 'user':
			if(!empty($user_role))
				print_r($msg);
			break;
		default:
				print_r($msg);		
			# code...
			break;
	}
	if($pre) echo "</pre>";
}
function gb_print_pre($msg, $role = "") {
	gb_print($msg, $role, true);
}
modify("gb_cron", "gb_cron_process_filter_emails", 100, 3);
function gb_cron_process_filter_emails($n, $cron_id, $context) {
	gb_print_pre("Starting Cron Send Filter Emails ...", "admin");
	gb_cron_schedule_filter_emails($cron_id, $context);
	gb_cron_send_filter_emails($cron_id, $context);		
	gb_print_pre("End of Cron Send Filter Emails...", "admin");
	exit;
}

function gb_cron_schedule_filter_emails($cron_id, $context) {
	$filter_emails = gb_get_filter_emails();
	foreach ($filter_emails as $key => $filter_filter_emails) {
                if(!empty($filter_filter_emails) && is_array($filter_filter_emails))
		foreach ($filter_filter_emails as $filter_emails_id => $filter_email) {
			$filter_id = $filter_email["filter_id"];
			$timestamp = gb_get_filter_email_timestamp($filter_email);
			gb_print_pre($filter_emails_id.":".$filter_id.":".$filter_email["freq"].":".$filter_email["freq_text"].":".date("Y-m-d H:i:s", $timestamp), "admin");
			if(empty($timestamp) || $timestamp > time() || $timestamp <= @$filter_email["last_timestamp"])
				continue;

			//Check if already scheduled
			if(!empty($filter_email["scheduled"]) && (time() - $filter_email["scheduled"] > 3*60*60)) {
			
				$filter_email["last_error_on"] = $filter_email["scheduled"];
				$filter_email["last_error_message"] = "Send Timed Out or Interrupted";
				continue;
			}
			gb_print_pre(":scheduled:".$cron_id.":".date("Y-m-d H:i:s", $cron_id).":".date("Y-m-d H:i:s"), "admin");
			//Schedule 
			$filter_email["scheduled"] = $cron_id;
			$filter_filter_emails[$filter_emails_id] = $filter_email;
			gb_set_filter_emails($filter_id, $filter_filter_emails);

		//	gb_print($filter_email, false, true);
		}
	}
	//gb_print($filter_emails, '', true);
}

function gb_cron_send_filter_emails($cron_id, $context) {
	$filter_emails = gb_get_filter_emails();
	foreach ($filter_emails as $key => $filter_filter_emails) {
		if(!empty($filter_filter_emails) && is_array($filter_filter_emails))
		foreach ($filter_filter_emails as $filter_emails_id => $filter_email) {
			$filter_id = $filter_email["filter_id"];
			//Check if already scheduled
			if(empty($filter_email["scheduled"]) || $cron_id != $filter_email["scheduled"])
				continue;

			//Schedule 
			$sent = gb_cron_send_filter_email($filter_email);
			if($sent)
			{
				$filter_email["last_timestamp"] = $filter_email["scheduled"];
				unset($filter_email["scheduled"]);
				unset($filter_email["last_error_on"]);
				unset($filter_email["last_error_message"]);
				$filter_filter_emails[$filter_emails_id] = $filter_email;
			}
			else
			{
				$filter_email["last_error_on"] = $filter_email["scheduled"];
				$filter_email["last_error_message"] = "Send Failed";
				unset($filter_email["scheduled"]);
				$filter_filter_emails[$filter_emails_id] = $filter_email;				
			}
			gb_set_filter_emails($filter_id, $filter_filter_emails);

		//	gb_print($filter_email, false, true);
		}
	}
	//gb_print($filter_emails, '', true);
}

function gb_cron_send_filter_email($filter_email) {
	if(empty($filter_email["filter_id"]))
	{
		gb_print_pre("Empty Filter ID. Ending Send Email", "admin");
		gb_print_pre($filter_email, "admin");
	}
	$filter = gb_get_filter($filter_email["filter_id"]);

	$subject = $filter["GET"]["filter_name"];
	$email_ids = explode(",", $filter_email["email_ids"]);
	$sent = false;
	$attachements = array();

	$type = $filter_email["type"];
	$attachements = gb_get_filter_email_report($filter_email, $type);
	foreach ($email_ids as $to) {
		$to = trim($to);
		if(validate_email($to)) {
			gb_print_pre("Sending Email To: ".$to." Subject: ".$subject." Attached File: ".$attachements, "admin");
			$sent = gb_mail($to, $subject, "", null, $attachements);
		}
	}
	return $sent;
}
//modify("gb_cron", "gb_get_filter_email_report", 10, 2);
function gb_get_filter_email_report($filter_email, $type) {
	gb_print_pre("gb_get_filter_email_report", "admin");

	$filter_id = @$filter_email["filter_id"];
	$filter = gb_get_filter($filter_id);
	if(empty($filter))
	{
		gb_print_pre("Filter not found. Filter ID: ".$filter_id, "admin");
		return;
	}
	
	$action = $filter["action"];

	App::import('Controller', 'Reports');
	App::import('Model', 'Statement');
	$request = new CakeRequest;
	$request->addParams(array('controller' => 'Reports', 'action' => $action));

	$Reports = new ReportsController($request, new CakeResponse);
	$Reports->modelClass = "Statement";
	$Reports->Statement = new Statement;
	$Reports->Components->setController($Reports);

	$column_values = @json_decode(@$filter_email["column_values"]);
	if(!empty($column_values)) {
		$cvs = array();
		foreach ($column_values as $key => $value) {
			$cvs[] = $key;
		}
		$column_values = implode(",", $cvs);
	}
	else
		$column_values = "1,2,3,4,5,6";

	$sort = !empty($filter_email["sort"])? $filter_email["sort"]:@$filter["GET"]["filter_email_sort_" . $filter_id];
	$sort_direction = !empty($filter_email["sort_direction"])? $filter_email["sort_direction"]:@$filter["GET"]["filter_email_sort_direction_" . $filter_id];


	$request = array(
		"filter_id" => $filter_id,
		strtolower($type) => true,
		"layout" => "ajax",
		"return_type" => "path",
		"column_values" => $column_values,
		"sort"				=> $sort,
		"sort_direction"	=> $sort_direction,

		);

	$days = intVal(@$filter_email["days"]);
	if(!empty($days))
	{
		$request["timestamp_start"] = date("Y-m-d", strtotime("yesterday -".$days." days"));
		$request["timestamp_end"] = date("Y-m-d", strtotime("yesterday"));
	}

//	gb_print($filter_email, false, true);
//	gb_print($request, false, true);
	$file = $Reports->{$action}($request);
	gb_print_pre($file, "admin");
	gb_print_pre("gb_get_filter_email_report_end", "admin");

	return $file;
	exit;

}
modify("init", "gb_cdn", 10, 2);
function gb_cdn($r, $context) {
    if(defined("CDN_URL"))
    modify("assetPath", function($path, $options) { return CDN_URL.$path; }, 10, 2);
}
modify("after_save", "gb_void_statements", 4, 3);
function gb_void_statements($statement_array, $type, $Input) {
	if($type != "statements" || $statement_array["verb_id"] != "http://adlnet.gov/expapi/verbs/voided" || $statement_array["object_objectType"] != "StatementRef")
		return $statement_array;

	$id = $statement_array["objectid"];
	if(empty($id))
		return $statement_array;

	App::import('Model', 'Statement');
	$Statement = new Statement();
	$s = $Statement->find("first", array("conditions" => array("statement_id" => $id)));
	
	if(!empty($s) && empty($s["Statement"]["voided"])) {
		$s["Statement"]["voided"] = 1;
		$update = array(
				"id"		=> $s["Statement"]["id"],
				"voided"	=> 1
			);
		$Statement->save($s);
	}

	return $statement_array;	
}

function gb_get_db_cache($key, $ignore_user = false) {
	$key =  "cache_".$key;
	if(!$ignore_user) {
		$user_id = User::get("id");
		if(empty($user_id))
			return;
		$key = $key."_user_".$user_id;
	}
	$data = grassblade_config_get($key);
	if(!isset($data["value"]) || !empty($data["expiry"]) && $data["expiry"] < time()) {
		//Expired
		grassblade_config_delete($key);
		return;
	}
	return $data;
}

function gb_set_db_cache($key, $value, $expiry = null, $ignore_user = false) {
	$key =  "cache_".$key;
	if(!$ignore_user) {
		$user_id = User::get("id");
		if(empty($user_id))
			return;
		$key = $key."_user_".$user_id;
	}
	$data = array(
			"user_id"	=> @$user_id,
			"timestamp" => time(),
			"value"		=> $value,
			"expiry"	=> @$expiry,
		);
	grassblade_config_set($key, $data);
}

function get_video_played_segments_div($statement) {
	if(!empty($statement->result->extensions) && !empty($statement->result->extensions->{"https://w3id.org/xapi/video/extensions/played-segments"}) && is_string($statement->result->extensions->{"https://w3id.org/xapi/video/extensions/played-segments"})) {
		$played_segments = preg_replace("/[^0-9,.\[\]]/", "", $statement->result->extensions->{"https://w3id.org/xapi/video/extensions/played-segments"});
		$played_segments_div = "<div class='played-segments' data-played-segments='".$played_segments."' ";
		if(!empty($statement->result->extensions->{"https://w3id.org/xapi/video/extensions/progress"})) {
			$progress = preg_replace("/[^0-9.]/", "", $statement->result->extensions->{"https://w3id.org/xapi/video/extensions/progress"});
			$played_segments_div .= " data-video-progress='".$progress."' ";
		}
		if(!empty($statement->result->extensions->{"https://w3id.org/xapi/video/extensions/length"})) {
			$length = preg_replace("/[^0-9.]/", "", $statement->result->extensions->{"https://w3id.org/xapi/video/extensions/length"});
			$played_segments_div .= " data-video-length='".$length."' ";
		}
		$played_segments_div .= "></div>";

		return $played_segments_div;
	}
	return '';
}


// Score AVG Calculation on every passed/failed
//		$statement_array = modified("after_save", $statement_array, "statements", $Input);
modify("after_save", function($statement_array, $type, $Input) {
//	CakeLog::write('debug', print_r($statement_array, true));
//	CakeLog::write('debug', $type);

	if($type == "statements") {
			CakeLog::write('debug', "statement");

		if( in_array($statement_array["verb_id"], array("http://adlnet.gov/expapi/verbs/passed", "http://adlnet.gov/expapi/verbs/failed") )) {
			CakeLog::write('debug', "passed/failed");
			$objectid = $statement_array["objectid"];
			$existing_data = grassblade_config_get("score_avg_".$objectid);
		//	CakeLog::write('debug', print_r($existing_data, true));

			if(empty($existing_data))
				return $statement_array;
		//	CakeLog::write('debug', "calculate");
			$result_score_raw 		= $statement_array["result_score_raw"];
			$result_score_scaled 	= $statement_array["result_score_scaled"];

			//Raw Min
			$existing_data["raw_min"] = ($result_score_raw < $existing_data["raw_min"])? $result_score_raw:$existing_data["raw_min"];

			//Raw Max
			$existing_data["raw_max"] = ($result_score_raw > $existing_data["raw_max"])? $result_score_raw:$existing_data["raw_max"];

			//Raw Avg
			$existing_data["raw_avg"] = ($existing_data["raw_avg"] * $existing_data["count"] + $result_score_raw) / ($existing_data["count"] + 1);  

			//Scaled Min
			$existing_data["scaled_min"] = ($result_score_scaled < $existing_data["scaled_min"])? $result_score_scaled:$existing_data["scaled_min"];

			//Scaled Max
			$existing_data["scaled_max"] = ($result_score_scaled > $existing_data["scaled_max"])? $result_score_scaled:$existing_data["scaled_max"];

			//Scaled Avg
			$existing_data["scaled_avg"] = ($existing_data["scaled_avg"] * $existing_data["count"] + $result_score_scaled) / ($existing_data["count"] + 1);  

			//Count
			$existing_data["count"]++;

			//Updated On
			$existing_data["updated_on"] = time();

			//Max ID
			$existing_data["max_id"] = $statement_array["id"];
		//	CakeLog::write('debug', print_r($existing_data, true));

			grassblade_config_set("score_avg_".$objectid, $existing_data);
		}
	}
	return $statement_array;	
}, 10, 3);

function load_contents_data($force_load = false) {
	global $all_content_loaded, $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index, $all_contents_objectid_index;
	if(!$force_load && $all_content_loaded)
		return;
	$all_content_loaded = true;

	App::import('Model', 'ContentType');

	$ContentType = new ContentType();
	$all_content_types = $ContentType->find("all");
	
	App::import('Model', 'Content');
	$Content = new Content();
	$all_contents = $Content->find("all");
	$all_contents_temp = array();

	if(!empty($all_contents))
	foreach ($all_contents as $key => $value) {
		$id = $value["Content"]["id"];
		$all_contents_temp[$id] = $value;
	}

	$all_contents = $all_contents_temp;

	foreach ($all_contents as $key => $value) {
		$parent = $value["Content"]["parent"];
		$remote = $value["Content"]["remote"];
		$objectid = $value["Content"]["objectid"];
		
		if(empty($all_contents_parent_index[$parent]))
			$all_contents_parent_index[$parent] = array();

		if(empty($all_contents_remote_index[$remote]))
			$all_contents_remote_index[$parent] = array();

		if(empty($all_contents_objectid_index[$objectid]))
			$all_contents_objectid_index[$objectid] = array();

		$all_contents_parent_index[$parent][$key] = $key;
		$all_contents_remote_index[$remote][$key] = $key;
		$all_contents_objectid_index[$objectid][$key] = $key;
	}
}

modify("fetch_contents_data", function($r, $context) {
	$modified_time_key = "fetch_contents_data.modified_time";
	$modified_time =  grassblade_config_get($modified_time_key, 0);

	if(!empty($_REQUEST["re_fetch_contents_data"]))
		$modified_time = 0;

	if($modified_time > time() - 3600 && empty($_REQUEST["force_fetch_contents_data"])) //Do not check if checked in last 1 hours.
		return;

	if($modified_time)
		$modified_time -= 12*60*60;

	$current_time = time();

	global $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	load_contents_data();
	App::import('Model', 'Content');
	$Content = new Content();

//	error_reporting(E_ALL);
//	ini_set('display_errors', 1);

	$wp = get_wordpress_client();

	if(empty($wp) || is_string($wp)) {
		if(is_string($wp))
		$context->error = $wp;
		return false;
	}

	$contents_data = call_wordpress_api($wp, "courses", array("modified_time" => $modified_time));
	//echo "<pre>";print_r($contents_data);echo "</pre>";
	if(empty($contents_data))
		return;

	if(is_string($contents_data)) {
		$Integrations = grassblade_config_get("Configure_Integrations");
		$gb_xapi = @$Integrations["gb_xapi"];

		$error_msg = $contents_data;
		$error_data = array(
				"type"			=> "WordPressAPI",
				"user"			=> $gb_xapi["user"],
				"data"			=> array('_SERVER' => $_SERVER, '_REQUEST' => $_REQUEST, "json_api_response" => $contents_data),
				"url"			=> $gb_xapi["url"],
				"request_method"=> "",
				"error_code" 	=> 0,
				"error_msg" 	=> $error_msg,
				"status"		=> 0						
				);
		if(strpos($error_msg, "grassblade.courses does not exist"))
		{
			$error_msg .= " Please upgrade your GrassBlade xAPI Companion on WordPress.";
		}
		else
		store_error_log($error_data);

		$context->Session->setFlash($error_msg, 'default', array('class' => 'note note-danger'));
		return false;
	}

	$updated = date("Y-m-d H:i:s");
	foreach ($contents_data as $course_id => $course_data) {
		//echo "<br>".$course_id.":".(@$course_data->course->post_title);
		//echo "<pre>";print_r($course_data);exit;

		if(is_bool($course_data) && $course_data === true) {
				//Remove contents from list so they are not marked as disabled/deleted.
				if(!empty($all_contents_remote_index[$course_id]))
				foreach ($all_contents_remote_index[$course_id] as $key) {	
					$id = @$all_contents[$key]["Content"]["id"];
					unset($all_contents[$key]);
					foreach(get_all_child_content_indexes($id) as $child_key) {
						unset($all_contents[$child_key]);
					}
				}
			continue;
		}
		if($course_id == "contents" && is_object($course_data)) {
			foreach ($course_data as $xapi_content) {
				update_contents_data(array(), $xapi_content, 0, $Content);
			}
		}	
		else
		if(is_object($course_data))
		update_contents_data($course_data, $course_data->course, 0, $Content);
	}
	//print_r($all_contents);

	foreach ($all_contents as $key => $value) {
		$value["Content"]["status"] = 0;
		$value["Content"]["updated"] = $updated;
		$Content->save($value);
	}
	$modified_time =  grassblade_config_set($modified_time_key, $current_time);

	load_contents_data(true);
	return;
}, 10, 2);
function get_all_child_content_indexes($content) {
	load_contents_data();
	$all_ids = array();
	global $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	if(is_numeric($content))
		$id = $content;
	else if(is_array($content) && !empty($content["Content"]["id"]))
		$id = $content["Content"]["id"];
	else if(is_array($content) && !empty($content["id"]))
		$id = $content["id"];
	
	if(empty($id))
		return $all_ids;

	if(!empty($all_contents_parent_index[$id]))
	foreach ($all_contents_parent_index[$id] as $child_key) {
		$all_ids[$child_key] = $child_key;

		foreach (get_all_child_content_indexes($child_key) as $child_key2) {
			$all_ids[$child_key2] = $child_key2;
		}
	}
	return $all_ids;
}
function get_all_parent_content_indexes($content) {
	load_contents_data();
//	echo ":A".$content;
	$all_ids = array();
	global $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	if(is_numeric($content))
		$id = $content;
	else if(is_array($content) && !empty($content["Content"]["id"]))
		$id = $content["Content"]["id"];
	else if(is_array($content) && !empty($content["id"]))
		$id = $content["id"];

	if(empty($id))
		return $all_ids;
//echo ":B".$id;

	$content = $all_contents[$id];

	$parent_id = $content["Content"]["parent"];

	if(empty($parent_id))
		return $all_ids;
	else {
		$all_ids[$parent_id] = $parent_id;
		//echo "<pre><Br>C".$id.":".$parent_id."</pre>";
		//$temp = get_all_parent_content_indexes($parent_id);
		//print_r($temp);
		$all_ids += get_all_parent_content_indexes($parent_id);
	}
	return $all_ids;
}
function update_contents_data($contents_data, $post, $parent, $Content ) {
	//print_r($contents_data);
	//Add Post and Post Activity ID
	//$post = $contents_data->{$type_name};
	global $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	$types = array(
		"sfwd-courses" 	=> 1,
		"sfwd-lessons"	=> 2,
		"sfwd-topic"	=> 3,
		"sfwd-quiz"		=> 4,
		"gb_xapi_content" => 5
	);
	$updated = date("Y-m-d H:i:s");

	$data = array(
			"name" => $post->post_title,
			"type" => $types[$post->post_type],
			"remote" => $post->ID,
			"parent"	=> $parent,
			"updated"	=> $updated,
			"objectid"	=> $post->activity_id,
			"status"	=> 1
		);

	$data = update_contents_and_activity($data, $Content);	
	//Add Post and Post Activity ID
	$existing_post_id = @$data["id"];
	//echo "<BR>".$existing_post_id;
	unset($all_contents[$existing_post_id]);

	//Add lessons if "lessons"
	if(!empty($contents_data->lessons)) {
		foreach ($contents_data->lessons as $lesson_id => $lesson_data) {
			update_contents_data($lesson_data, $lesson_data->lesson, $existing_post_id, $Content);
		}
	}

	//Add topics if "topics"
	if(!empty($contents_data->topics)) {
		foreach ($contents_data->topics as $topic_id => $topic_data) {
			update_contents_data($topic_data, $topic_data->topic, $existing_post_id, $Content);
		}
	}
	
	//Add quizzes if "quizzes"
	if(!empty($contents_data->quizzes)) {
		foreach ($contents_data->quizzes as $quiz_id => $quiz_data) {
			update_contents_data($quiz_data, $quiz_data->quiz, $existing_post_id, $Content);
		}
	}
	
	//Add xapi content if "xapi_content"
	if(!empty($contents_data->xapi_content)) {
		update_contents_data(array(), $contents_data->xapi_content, $existing_post_id, $Content);
	}
}
function update_contents_and_activity($data, $Content) {
	if(!empty($data)) {
		$conditions = array("remote" => $data["remote"], "parent" => $data["parent"]);
		$existing_content = content_find($conditions);
		if(!empty($existing_content))
			$existing_content = array_shift($existing_content);
		
		if(!empty($existing_content["Content"]))
		{
			$update = false;
			foreach ($existing_content["Content"] as $key => $value) {
				if(in_array($key, array("name", "type", "remote", "parent", "status", "objectid")))
				{
					if(!isset($data[$key]) || $data[$key] != $value)
						$update = true;
				}
			}
			if(empty($update))
				return $existing_content["Content"];
		}

		$existing_content_id = @$existing_content["Content"]["id"];
		$data["id"]	= $existing_content_id;

		$data = $Content->save(array("Content" => $data));
		$existing_content_id = @$data["Content"]["id"];
		return $data["Content"];
	}
}

function content_find_indexes($conditions) {
	load_contents_data();

	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index, $all_contents_objectid_index;

	$r = array();
	if(!empty($conditions["remote"])) {
		if(empty( $all_contents_remote_index[$conditions["remote"]] ))
			return array();
		else
		$r = $all_contents_remote_index[$conditions["remote"]];
	}
	if(!empty($conditions["parent"]) && !empty($all_contents_parent_index[$conditions["parent"]])) {
		if(empty($all_contents_parent_index[$conditions["parent"]]))
			return array();

		if(!empty($r))
			$r = array_intersect($r, $all_contents_parent_index[$conditions["parent"]]);
		else
			$r = $all_contents_parent_index[$conditions["parent"]];
	}
	if(!empty($conditions["objectid"]) && !empty($all_contents_objectid_index[$conditions["objectid"]])) {
		if(empty($all_contents_objectid_index[$conditions["objectid"]]))
			return array();

		if(!empty($r))
			$r = array_intersect($r, $all_contents_objectid_index[$conditions["objectid"]]);
		else
			$r = $all_contents_objectid_index[$conditions["objectid"]];
	}
	return $r;
}
function content_find($conditions) {
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index, $all_contents_objectid_index;
	$contents_index = content_find_indexes($conditions);
	$contents = array();

	if(!empty($contents_index))
	foreach ($contents_index as $key => $value) {
		if(isset($all_contents[$key]))
		$contents[$key] = $all_contents[$key];
	}
	return $contents;
}
function get_all_related_content($statement) {
	load_contents_data();
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	$contents = array();
	$objectids = array();

	if(!empty($statement["objectid"])) {
		$contents = content_find(array("objectid" => $statement["objectid"]));
		$objectids[$statement["objectid"]] = true;

//		echo "<br>".$statement["objectid"];
	}
	if(!empty($statement["parent_ids"])) {
		$parent_ids = array_map("trim", explode(",", $statement["parent_ids"]));
		if(!empty($parent_ids))
		foreach ($parent_ids as $parent_id) {
			if(empty($objectids[$parent_id])) {
				$contents += content_find(array("objectid" => $parent_id));
				$objectids[$parent_id] = true;
			}
		 }
//		 echo "<br>".$parent_id;
	}
	if(!empty($statement["grouping_ids"])) {
		$grouping_ids = array_map("trim", explode(",", $statement["grouping_ids"]));
		if(!empty($grouping_ids))
		foreach ($grouping_ids as $grouping_id) {
			if(empty($objectids[$grouping_id])) {
				$contents += content_find(array("objectid" => $grouping_id));
				$objectids[$grouping_id] = true;
			}
		}
//		echo "<br>".$grouping_id;
	}

	foreach ($contents as $content_id => $content) {
		$parents_indexes = get_all_parent_content_indexes($content_id);
		foreach ($parents_indexes as $parent_index) {
			$contents[$parent_index] = $all_contents[$parent_index];
		}
	}

	return $contents;
}
function de_duplicate_content_by_remote($contents) {
	$temp = array();
	foreach ($contents as $content_id => $content) {
		$temp[$content["Content"]["remote"]] = $content;
	}
	$contents = array();
	foreach ($temp as $content) {
		$contents[$content["Content"]["id"]] = $content;
	}
	return $contents;
}
function categorize_content_by_type( $contents ) {
	load_contents_data();
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index;
//print_r($contents);
	$return = array();
	foreach ($all_content_types as $content_type) {
		$type = $content_type["ContentType"]["id"];
		$nametag = "content_".strtolower( sanitize_string( $content_type["ContentType"]["name"] ) );
//echo $type.":".$nametag;

		if(!isset($return[$nametag]))
			$return[$nametag] = array();

		foreach ($contents as $content_id => $content) {
			if($content["Content"]["type"] == $type) {
				$content["ContentType"] = $content_type["ContentType"];
				$return[$nametag][$content_id] = $content;
			}
		}
	}
	return $return;
}
function get_categorized_content_list() {
	load_contents_data();
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index;
	return categorize_content_by_type($all_contents);		
}	
function sanitize_string($txt) {
	return preg_replace('/[^A-Za-z0-9\-\_]/', '', str_replace(" ", "_", $txt) );
}
function get_content_types() {
	load_contents_data();
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index;
	$content_types = array();
	foreach ($all_content_types as $content_type) {
		$content_type_tag = "content_".strtolower(sanitize_string($content_type["ContentType"]["name"]));
		$content_types[$content_type_tag] = $content_type["ContentType"];
	}
	return $content_types;
}
function display_contents($contents) {
	if(!empty($contents)) {
		$links = array();
		foreach ($contents as $content) {
		//	echo "<pre>";print_r($content);
		//	exit;
			$content_type_tag = "content_".strtolower(sanitize_string($content["ContentType"]["name"]));
			$parameter = urlencode("content[".$content_type_tag."][]");
			$links[] = "<a href='?".$parameter."=".$content["Content"]["id"]."'>".$content["Content"]["name"]."</a>";
		}
		if(count($links) > 1)
		echo "<ol><li>".implode("</li><li>", $links)."</li></ol>";
		else
		echo implode(" ", $links);
	}	
}
function get_content_filter_conditions($contents, $conditions) {
	if(empty($contents))
		return $conditions;
//echo "<pre>";print_r($contents);
	$all_ids = array();
	$final_ids = array();
	foreach ($contents as $key => $value) {
		if(is_array($value))
		foreach ($value as $key2 => $value2) {
			//echo "<br>". $value2;
			if(!empty($value2))
			$all_ids[$value2] = get_all_child_content_indexes($value2) + array($value2 => $value2);

			if(empty($final_ids) || count($final_ids) < 1)
				$final_ids = $all_ids[$value2];
			else
			if(!empty($all_ids[$value2]) && count($all_ids[$value2]) > 0)
			$final_ids = array_intersect($final_ids, $all_ids[$value2]);
		}
	}

	if(empty($final_ids) || count($final_ids) < 1) {
		$conditions[] = "1 = 2";
		return $conditions;
	}

//	print_r($final_ids);
	global $all_contents;
	$object_ids = array();

	foreach ($final_ids as $key => $value) {
		if(!empty($all_contents[$key]["Content"]["objectid"]))
		$object_ids[] = $all_contents[$key]["Content"]["objectid"];
	}
//	print_r($object_ids);

	if(empty($object_ids) || count($object_ids) < 1) {
		$conditions[] = "1 = 2";
		return $conditions;
	}

	$conditions[] = array("OR" => array(
						"objectid" => $object_ids,
						"parent_ids" => $object_ids,
						"grouping_ids" => $object_ids,
						));
	return $conditions;

	//exit;
}
modify("report_format", function($statement, $request, $Context) {
	global $all_content_types, $all_contents, $all_contents_parent_index, $all_contents_remote_index;

	$contents = get_all_related_content($statement);
	$contents = de_duplicate_content_by_remote($contents);
	$contents = categorize_content_by_type($contents);

	if( (!empty($request['csv']) || !empty($request['pdf'])) && !empty($contents)) {

		foreach ($contents as $content_type => $contents_by_type) {
			$content_items = array();
			if(!empty($contents_by_type))
			foreach ($contents_by_type as $key => $value) {
				if(!empty($value["Content"]["name"]))
					$content_items[] = $value["Content"]["name"];
			}
			$contents[$content_type] = implode(", ", $content_items);
		}
	}
	$statement += $contents;

	return $statement;
}, 10, 3);
function h_array($array, $strip_tags = false) {
	$return = '';
	foreach ($array as $key => $value) {
		if(is_array($value) || is_object($value)) {
			$return .= "<br><b>".$key.": </b></b>";
			$return .= "<p style='margin-left: 10px'>".h_array($value)."</p>";
		}
		else {
			$return .= "<b>".$key.": </b></b>";

			if($strip_tags)
			$return .= h(strip_tags($value))."<br>";
			else
			$return .= h($value)."<br>";
		}
	}
	return $return;
}

modify("gb_cron", "gb_cron_triggers_rerun", 50, 3);
function gb_cron_triggers_rerun($n, $cron_id, $context) {
	App::import("Model", "ErrorLog");
	if(defined("RETRIGGER_LOOKUP_TIME"))
		$retrigger_time = RETRIGGER_LOOKUP_TIME;
	else
		$retrigger_time = 86400;

	if(defined("RETRIGGER_AFTER"))
		$RETRIGGER_AFTER = RETRIGGER_AFTER;
	else
		$RETRIGGER_AFTER = 900;

	if(defined("RETRIGGER_MAX_RERUNS"))
		$RETRIGGER_MAX_RERUNS = RETRIGGER_MAX_RERUNS;
	else
		$RETRIGGER_MAX_RERUNS = 3;


	$ErrorLog = new ErrorLog();
	/*
		Fetch all Failed Trigger Logs and Waiting Triggers logs older than 10 minutes
	*/
	$error_logs = $ErrorLog->find("all", array("conditions" => array(
				"type" 	=> "Trigger",
				"OR"	=> array(
						"status" => 0,
						array(
							"status" => 2,
							"created < " => date('Y-m-d H:i:s', time() - $RETRIGGER_AFTER)
							),
					),
				"created > " => date('Y-m-d H:i:s', time() - $retrigger_time)
			),
			"order" => "id ASC"
		));
	$trigger_logs = array();
	foreach ($error_logs as $key => $value) {
		$data = json_decode($value["ErrorLog"]["data"]);
		$key = $value["ErrorLog"]["statement_id"].$value["ErrorLog"]["url"].$data->trigger->id;
		if(empty($trigger_logs[$key]))
		$trigger_logs[$key] = $value["ErrorLog"];
	}

	/*
		Revalidate for - 
			1. Successful Triggers after the Failed/Long Waiting Trigger
			2. Another Trigger running waiting.
			3. Number of Reruns
	*/
	foreach ($trigger_logs as $key => $trigger_log) {
		$has_completed = $ErrorLog->find("count", array("conditions" => array(
				"type" 	=> "Trigger",
				"status" => 1,
				"statement_id" => $trigger_log["statement_id"],
				"url"	=> $trigger_log["url"],
				"id > "	=> $trigger_log["id"]
			)));
		if($has_completed) {
			unset($trigger_logs[$key]);
			continue;
		}

		$is_running = $ErrorLog->find("count", array("conditions" => array(
				"type" 	=> "Trigger",
				"status" => 2,
				"statement_id" => $trigger_log["statement_id"],
				"url"	=> $trigger_log["url"],
				"id > "	=> $trigger_log["id"],
				"created >= " => date('Y-m-d H:i:s', time() - $RETRIGGER_AFTER)
			)));
		if($is_running) {
			unset($trigger_logs[$key]);
			continue;
		}

		$count_reruns = $ErrorLog->find("count", array("conditions" => array(
				"type" 	=> "Trigger",
				"statement_id" => $trigger_log["statement_id"],
				"url"	=> $trigger_log["url"],
				"id >="	=> $trigger_log["id"]
			)));
		
		if($count_reruns >= $RETRIGGER_MAX_RERUNS)
			unset($trigger_logs[$key]);
	}
	App::import("Model", "Statement");
	$Statement = new Statement();

	foreach ($trigger_logs as $key => $trigger_log) {
		$data = json_decode($value["ErrorLog"]["data"]);
		$triggers = array( (array) $data->trigger );
		$statement = $Statement->find("first", array("conditions" => array("statement_id" => $trigger_log["statement_id"])));
		$statement_array = $statement["Statement"];
		echo "<br>ReRun Trigger: ".$trigger_log["statement_id"]." : ".(@$trigger_log["agent_id"]). " : ".$trigger_log["url"] . " : ".$triggers[0]["type"];
		include_once(APP."Vendor".DS."statement_hooks.php");
		statement_hooks($statement_array, null, $triggers);
	}
}
function add_error_log($error_code, $error_message) {
	global $Input;
	if(empty($Input)) {
		$Input = new Input();
	}
	$request_uri = @$_SERVER["REQUEST_URI"];
	$request_uri = explode("?", $request_uri);
	$request_uri = $request_uri[0];

	if(!empty($Input->headers["actor"])) {
		$actor = $Input->headers["actor"];
		$actor = json_decode($actor);
	}
	else
	if(!empty($Input->params["agent"])) {
		$actor = $Input->params["agent"];
		$actor = json_decode($actor);
	}	
	else
	if(!empty($Input->content))
	{
		$statement = json_decode($Input->content);
		if(!empty($statement) && is_object($statement) && !empty($statement->actor))
			$actor = $statement->actor;
	}
	if(!empty($actor)) {
		$agent_array = gb_agent_array($actor);
		$user = $agent_array["agent_id"];
	}
	else
		$user = "";


	if(!empty($Input->headers["activityId"])) {
		$objectid = $Input->headers["activityId"];
	}
	else
	if(!empty($Input->params["activityId"])) {
		$objectid = $Input->params["activityId"];
	}
	else
	if(!empty($Input->content))
	{
		$statement = json_decode($Input->content);
		if(!empty($statement) && is_object($statement) && !empty($statement->object->id))
			$objectid = $statement->object->id;
	}
	else
		$objectid = "";


	if(strpos($request_uri, "/xAPI/") < 0)
	{
		return;
	}
	$type = trim(str_replace(array("xAPI/", "grassblade-lrs/"), array("",""), $request_uri), "/");
	$data = array(
			"type" 			=> $type,
			"user"			=> $user,
			"objectid"		=> $objectid,
			"request_method" => $Input->method,
			"data"			=> json_encode($Input),
			"error_code" 	=> $error_code,
			"error_msg"		=> $error_message,
			"status"		=> 0,
			"url"			=> $_SERVER["REQUEST_URI"],
			"IP"			=> @$_SERVER["REMOTE_ADDR"],
			"created"		=> date("Y-m-d H:i:s"),
			"modified"		=> date("Y-m-d H:i:s"),
  		);
	store_error_log($data);
}
function store_error_log($data) {
	App::import("Model", "ErrorLog");
	$ErrorLog = new ErrorLog();
	if(empty($data["data"]))
		$data["data"] = json_encode(array("_SERVER" => $_SERVER));
	else
	if( is_array($data["data"]) || is_object($data["data"]) ) {
		$data["data"] = json_encode($data["data"]);
	}
	$data["url"] = empty($data["url"])? @$_SERVER["REQUEST_URI"]:$data["url"];
	$data["IP"] = empty($data["IP"])? @$_SERVER["REMOTE_ADDR"]:$data["IP"];
	$data["created"] = empty($data["created"])? date("Y-m-d H:i:s"):$data["created"];
	$data["modified"] = empty($data["modified"])? date("Y-m-d H:i:s"):$data["modified"];
	$data["response"] = empty($data["response"])? "":$data["response"];

	$ErrorLog->save($data);
}
