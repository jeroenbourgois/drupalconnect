<?php
	// let's turn on error reporting
	ini_set("display_errors", 1);
	
	// include the XMLRPC library
	// I used the one by keithdevens.com/software/xmlrpc
	// You are free to use any library, but then the call will be different
	//
	// Note: this library has one big downside, it throws a lot of warnings in PHP 5, since
	// it has some deprecated calls. I just hide warnings on my production site, so that was not
	// a problem for me, I you feel this is an issue, try contacting Keith, or choose another library.
	require_once("xmlrpc.php");
	
	// settings
	$domain = "drupalconnect.backend";    // the domain you are calling to, without http://
	$endpoint = "/services/xmlrpc";       // the endpoint where the xmlrpc service is running, you shouldn't need to change this

	// perform the XMLRPC request
	$result = XMLRPC_request(
				$domain, // the domain
				$endpoint, // the endpoint
				"drupalconnect.get.pages" // the method we will be calling
				);

	// I don't know if it is always this way, but during all my tests
	// the actual result was in the second element of the returning array
	// the first element was a boolean
	$result = $result[1];
	
	// show what the output is giving us, for testing only
	// var_dump($result);
?>
<!DOCTYPE html>
<head>
	<title>Drupalconnect frontend</title>
	
	<style type="text/css">
		* { padding: 0; margin: 0; }
		html  { font-family: "Lucida grande", arial, sans-serif; color: #222;}
		body  { width: 800px; margin: 0 auto; }
		h1 	  { margin: 80px 0; }
		h3		{ margin-bottom: 10px; }
		.page { margin-bottom: 40px; border-bottom: dotted 1px #999; padding-bottom: 40px; }
	</style>
</head>
<body>
	<h1>Drupalconnect frontend</h1>
	<h2>Pages from the Drupal backend:</h2>
	<!-- Page nodes -->
	<?php foreach($result as $result_item) :
		// the XMLRPC server parses everthing into associative arrays.
		// if you know you stored objects in your result array on the backend (like we did)
		// you can just convert each node back to an object, and access the properties through
		// the -> notation, otherwise, you can use the associative array notation
		$result_item = (object) $result_item;
	?>
	<div class="page">
		<h3><?php echo($result_item->title); // or $result_item["title"] ?></h3>
		<div><?php echo($result_item->body); // or $result_item["body"] ?></div>
	</div>
	<?php endforeach; ?>
</body>