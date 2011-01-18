<?php
  ini_set("display_errors", 1);
  
  include('config.php');
  require('assets/libs/drupalconnect.php');
  require('assets/libs/xmlrpc.php');

  $dc = new DrupalConnect($endpoint, $localDomain, $api_key);

  $session_id = $dc->init_session();
  $login = $dc->login($session_id, "admin", "admin");
  $node = $dc->get_view("proximity_site");
  
  //$result = XMLRPC_request("proxit.local", "/services/xmlrpc", "recipe.all");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>drupalconnect demo</title>
  <link rel="stylesheet" href="assets/css/main.css" type="text/css" />
  
  <script src="assets/js/jquery-1.4.4.min.js"></script>
  <script src="assets/js/drupalconnect.js"></script>
</head>
<body>
  <h1>Demo for drupalconnect (xml/rpc)</h1>
  
  <em>Click the headings to collapse code blocks</em>
  
  <h2>Init drupal connect session</h2>
  <pre>session_id: <?php print $session_id; ?></pre>

  <h2>Login with user</h2>
  <pre><?php print_r($login); ?></pre>
  
  <h2>Fetch a node</h2>
  <pre><?php print_r($node); ?></pre>
  
  <h2>Output</h2>
  <pre>
<?php
/*
  foreach ($result[1] as $node) {
    if(is_array($node)){
          print $node["title"] . "<br />";
    }
  }
  */
  var_dump($result);
?>
  </pre>
</body>
</html>

