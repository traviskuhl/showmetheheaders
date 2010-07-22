<?php
	
	// memcache me
	$memcache = new Memcache;
	
	// connect
	$memcache->connect('127.0.0.1', 11211);

	define("CID_HOST","smth::hosts");

?>