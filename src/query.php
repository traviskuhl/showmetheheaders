<?php

	// help
	include_once("showme.inc");

	// boring
	$boring = array(
		'accept-ranges',
		'age',
		'allow',
		'connection',
		'cache-control',
		'content-encoding',
		'content-language',
		'content-length',
		'content-location',
		'content-disposition',
		'content-md5',
		'content-range',
		'content-type',
		'date',
		'etag',
		'expires',
		'last-modified',
		'location',
		'pragma',
		'proxy-authenticate',
		'refresh',
		'retry-after',
		'server',
		'set-cookie',
		'trailer',
		'transfer-encoding',
		'vary',
		'via',
		'warning',
		'www-authenticate',
	);

	// don't show
	$ignore = array(
		'set-cookie',
	);
	
	// server
	$server = "#(apache|ngix|yts)#i"; 

	// check for xhr
	$xhr = filter_input(INPUT_GET,"xhr",FILTER_VALIDATE_BOOLEAN);
	
	// q
	$q = filter_input(INPUT_GET,"q",FILTER_SANITIZE_URL);	
	
		// no q just stop
		if ( !$q ) { return; }
	
	// if q
	if ( $q ) {
		
		// host
		$host = $q;
		
		// if q does not contain http:
		if ( stripos($q,"http://") === false OR stripos($q,"https://") == false ) {
			$host = "http://".$q;
		}
		
		// parseed
		$parsed = parse_url($host);
		
		// create a new cURL resource
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);																								
											
		// grab URL and pass it to the browser
		$raw = curl_exec($ch);
		
		// how long
		$time = curl_getinfo($ch,CURLINFO_TOTAL_TIME);
		
		// stat
		$stat = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
			
		// close cURL resource, and free up system resources
		curl_close($ch);
		
		// explode on \n
		$lines = explode("\n",trim($raw));	
		
		// headers
		$headers = array( 'cool' => array(), 'boring' => array() );
		
		// redirects
		$redi = 0;
		$status = array();
		
		// loop through each line
		foreach( $lines as $line ) {
			
			// no line lets skip
			if ( trim($line) == "" ) { continue; }
			
			// status?
			if ( strtoupper(substr($line,0,4)) == 'HTTP' ) {
				
				// get the code
				$status[] = $s = array_shift(array_slice(explode(" ",$line),1,1));

				// redi
				if ( $s >= 300 AND $s <= 399 ) { $redi++; }

				// keep going
				continue;
					
			}
			
			// parts
			$parts = explode(":",$line);
		
			// key and name
			$key = trim( array_shift($parts) );
			$val = trim(implode(":",$parts));
			
			// ignore?
			if ( in_array(strtolower($key),$ignore) ) { continue; }			
	
			if ( strtolower($key) == 'server' AND preg_match($server,$val) == 0 ) { $headers['cool'][$key] = $val; }
	
			// do it 
			if ( in_array(strtolower($key),$boring) ) {
				$headers['boring'][$key] = $val;
			}
			else {
				$headers['cool'][$key] = $val;
			}
	
		}
				
		// just a shell
		$html = "";
	
		// no headers and a bad status
		if ( count($headers['cool']) == 0 AND count($headers['boring']) == 0 ) {
			$html .= "
				<div class='nothing'>
					<h2>No Headers Available</h2>
					<p>We couldn't get any headers for {$q}. It returned a {$stat} status.</p>
				</div>
			";	
		}
		else {
		
			// make our lists
			if ( count($headers['cool']) ) {
				
				// start
				$html = "<ul class='cool'>";
				
					// add them
					foreach ( $headers['cool'] as $k => $v ) {
						$html .= "<li><em>$k</em> <span>$v</span></li>";
					}
			
				// end
				$html .= "</ul>";
		
			}
			else {
				$html .= "<div class='nothing'>nothing special</div>";
			}	
				
			$html .= "<ul class='boring'>";
			
				// add them
				foreach ( $headers['boring'] as $k => $v ) {
					$html .= "<li><em>$k</em> <span>$v</span></li>";
				}	
		
			// done
			$html .= "</ul>";
			
			// html
			$html .= "
				<div class='facts'>
					 Request took {$time} seconds with 
					 status ".plural('code',$status)." of ".implode(', ',$status)." and 
					 {$redi} ".plural('redirect',$redi).". 
					 <a class='raw' href='?q={$q}&raw=true'>Raw Headers</a>
				</div>
			";
			
			// add raw
			$html .= "<pre class='raw ".(isset($_GET['raw'])?'':'hide')."'>".trim($raw)."</pre>";
			
			// host id
			$hid = md5($parsed['host']);
			
			// inc hits
			if ( !$memcache->get($hid) ) {
				$memcache->set($hid,0);
			}
			
			// up it	
			$memcache->increment($hid);
			
			// add to list
			$hosts = $memcache->get(CID_HOST);
			
			// add it 
			$hosts[$hid] = array( $parsed, $headers );
			
			// save
			$memcache->set(CID_HOST, $hosts, MEMCACHE_COMPRESSED);
			
		}
	
		// what to do
		if ( $xhr ) {
		
			// nice header
			header("Content-Type: text/javascript");
		
			// exit with json
			exit( json_encode( array('stat'=>true,'html'=>$html) ) );
			
		}
	
		// print our html
		echo $html;

	}

	function plural($word,$i) {
		if ( is_array($i) ) { $i = count($i); }
		if ( $i <= 1 ) {
			return $word;
		}
		else {
			return "{$word}s";
		}
	}

?>