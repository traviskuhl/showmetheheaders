<?php

	// help
	include("showme.inc");
	
	// get all hosts	
	$hosts = $memcache->get(CID_HOST);	
	
	// holders
	$popular = array();
	$cool = array();
	
	// loop and slip into groups
	foreach ($hosts as $hid => $host) {
	
		// popular
		$popular[$hid] = $memcache->get($hid);
		
		// cool
		$cool[$hid] = count($host[1]['cool']);
		
	}

	// sort
	arsort($popular);
	arsort($cool);
	
	// q
	$q = filter_input(INPUT_GET,"q",FILTER_SANITIZE_URL);	

		// no q
		if ( !$q ) {
		
			// rand
			$rand = array_rand(array_slice($popular,0,50));
			
			// set it 
			$q = $hosts[$rand][0]['host'];
			
		} 

	// our own cool headers
	header("X-Hello: What's more fun than looking at headers");
	header("X-Source-Code: http://github.com/traviskuhl/showmetheheaders");
	header("X-Follow-Me-On-Twitter: @traviskuhl");	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Show Me the Headers</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="http://yui.yahooapis.com/combo?3.1.1/build/yui/yui-min.js"></script>
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.1/build/reset-fonts-grids/reset-fonts-grids.css">
		<link rel="stylesheet" type="text/css" href="/static/showme.css">
	</head>
	<body class="<?php echo (isset($_GET['q'])?'prefill':''); ?>">
		<div id="doc">
			<div id="hd">
				<h1><a href="http://showmetheheaders.com/">Show Me the Headers</a></h1>
				<form method="get" action="/">
					for <input type="text" name="q" class="off" value="<?php echo $q; ?>">
				</form>
			</div>
			<div id="bd">
				
				<div id="results">
					<?php include("./query.php"); ?>
				</div>
			
				<div id="leaders">
				
					<ul>
						<li class="h">Extra Headers</li>
						<?php

							// count
							$i = 1;
							
							// each
							foreach ( array_slice($cool,0,20) as $hid => $n ) {
								echo "<li class='".($i>5?'over':'')."'><em>".($i++)."</em> <a class='load' href='?q={$hosts[$hid][0]['host']}'>{$hosts[$hid][0]['host']}</a> <span>($n headers)</span></li>";
							}
							
						?>	
					</ul>
					
					<ul>
						<li class="h">Popular</li>
						<?php

							// count
							$i = 1;
							
							// each
							foreach ( array_slice($popular,0,20) as $hid => $n ) {
								echo "<li class='".($i>5?'over':'')."'><em>".($i++)."</em> <a class='load' href='?q={$hosts[$hid][0]['host']}'>{$hosts[$hid][0]['host']}</a>  <span>($n searches)</span></li>";
							}
							
						?>	
					</ul>
					
				</div>
			
			</div>
			<div id="ft">
				(c) 2010 - <a href='http://twitter.com/traviskuhl'>by @traviskuhl</a>
			</div>
		</div>			
		<script type="text/javascript" src="/static/showme.js"></script>		
		<script type="text/javascript">
		
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-123654-5']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>		
	</body>
</html>
