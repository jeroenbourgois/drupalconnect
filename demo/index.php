<?php
require('assets/libs/druxml.class.php');

include('config.php');

$talkative = TRUE; // spit out debugging information to screen?

// create connection
$drupalSession = new Drupal_connect( $localDomain, $apiKey, $endPoint, $credentials, $func, $param, $talkative );
$retVal = $drupalSession->dru_connect($endPoint);
$drupalSession->anon_session_id = $retVal['sessid'];
if ($drupalSession->anon_session_id) {
  //$drupalSession comes back as an array. It looks like this:
  //Array ( [sessid] => 3eaec0962d42c7a7bb42b642551e8e0a [user] => Array ( [uid] => 0 [hostname] => 123.123.2.3 [roles] => Array ( [1] => anonymous user ) [session] => [cache] => 0 ) )

  if ($talkative) print '<pre>Anon session id: '.$drupalSession->anon_session_id.'</pr>';

  $drupalSession->credentials = $auth_user;
  // connect for the first time as an authenticated user BEFORE we go getting node data
  $data = $drupalSession->auth_connect($localDomain, $endPoint, $drupalSession->anon_session_id, $apiKey, $drupalSession->credentials, 'user.login', $param, $talkative);
  if ($talkative) print_r($data);

  // now we go get node data
  $data = $drupalSession->auth_connect($localDomain, $endPoint, $drupalSession->anon_session_id, $apiKey, $drupalSession->credentials, $func, $param, $talkative);
  print_r($data);
}

/**
* Function for generating a random string, used for
* generating a token for the XML-RPC session
*/
function getUniqueCode($length = "") {
  $code = md5(uniqid(rand(), true));
  if ($length != "") return substr($code, 0, $length);
  else return $code;
}
?>