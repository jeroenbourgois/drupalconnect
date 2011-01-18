<?php
/**
* DrupalConnect
* version: 0.1
*/
class DrupalConnect
{
  public $verbose = FALSE;
	public $domain = "";
	public $api_key = "";
	
	private $endpoint = "";
	private $user;
	private $session_id;
	private $logged_in_session_id;
	
	function __construct($endpoint, $sender_domain, $api_key)
	{
		$this->endpoint = $endpoint;
		$this->domain = $sender_domain;
		$this->api_key = $api_key;
	}
	
	/**
	 * Creates a valid drupal xlmrpc session
	 * 
	 * @return $session_id
	 **/	
	public function init_session()
	{
    $request = xmlrpc_encode_request('system.connect', array());
		$context = stream_context_create(
			array('http' => array('method' => "POST", 'header' => "Content-Type: text/xml", 'content' => $request,)));

    // connect
    $connect = file_get_contents($this->endpoint, false, $context);

    // retrieve the result
    $response = xmlrpc_decode($connect);

    // process result
    if (xmlrpc_is_fault($response))
			trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
		
		
    if ($this->verbose)
      print_r($response);

		// fetch the session id
		$response = $response['sessid'];
		$session_id = $response;

    return $response;
  }

	/**
	 * Authenticate a user to the remote drupal site
	 *
	 * The user should have all required permissions to CRUD nodes, views, ... or whatever was set on the remote
	 * xmlrpc server and services module (along with the permissions)
	 *
	 * @param string $session_id a valid session id for the current xml/rpc session
	 * @param string $username
	 * @param string $password
	 */
	public function login($session_id, $username, $password) {
    // set vars for this connection
		$method_name = 'user.login';
    $nonce = $this->getUniqueCode();
    $timestamp = (string) strtotime("now");


    // now prepare a hash
    $hash_parameters = array($timestamp, $this->domain, $nonce, $method_name,);

    // create a hash using our API key
    $hash = hash_hmac("sha256", implode(';', $hash_parameters), $this->api_key);

    $required_args = array($hash, $this->domain, $timestamp, $nonce, $session_id, $username, $password);

    // prepare the request
    $request = xmlrpc_encode_request($method_name, $required_args);
    $context = stream_context_create(
			array('http' => array('method' => "POST", 'header' => "Content-Type: text/xml", 'content' => $request,)));

    $output ='';
    
    // connect
    $connect = file_get_contents($this->endpoint, false, $context);

    if($this->verbose)
    	print $connect;
    
    // retrieve the result
    $response = xmlrpc_decode($connect);

    // display the result on screen
    if (xmlrpc_is_fault($response)) {
      trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
    } else {
			// save the user object - we'll need it later
			$this->user = new stdClass();
			$this->user = (object) $response['user'];
     
      // save the current session_id - we'll need it to logout
      // sessid changes after we login
      $logged_in_session_id = $response['sessid'];

      if ($this->verbose)
        print_r ($response);

      return $this->user;
    }
	}
	
	public function get_node($session_id, $nid) {
    // set vars for this connection
		$method_name = 'node.get';
    $nonce = $this->getUniqueCode();
    $timestamp = (string) strtotime("now");


    // now prepare a hash
    $hash_parameters = array($timestamp, $this->domain, $nonce, $method_name,);

    // create a hash using our API key
    $hash = hash_hmac("sha256", implode(';', $hash_parameters), $this->api_key);

    $required_args = array($hash, $this->domain, $timestamp, $nonce, $nid);
print_r($required_args);
    // prepare the request
    $request = xmlrpc_encode_request($method_name, $required_args);
    $context = stream_context_create(
			array('http' => array('method' => "POST", 'header' => "Content-Type: text/xml", 'content' => $request,)));

    // connect
    $connect = file_get_contents($this->endpoint, false, $context);

    if($this->verbose)
    	print $connect;
    
    // retrieve the result
    $response = xmlrpc_decode($connect);

    // display the result on screen
    if (xmlrpc_is_fault($response)) {
      trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
            print_r ($response);
      return NULL;
    }

    if ($this->verbose)
      print_r ($response);

    return $response;
	}
	
	public function get_view($view) {
	    // set vars for this connection
  		$method_name = 'views.get';
      $nonce = $this->getUniqueCode();
      $timestamp = (string) strtotime("now");

      // now prepare a hash
      $hash_parameters = array($timestamp, $this->domain, $nonce, $method_name,);

      // create a hash using our API key
      $hash = hash_hmac("sha256", implode(';', $hash_parameters), $this->api_key);

      $required_args = array($hash, $this->domain, $timestamp, $nonce, $view, $view);
  print_r($required_args);
      // prepare the request
      $request = xmlrpc_encode_request($method_name, $required_args);
      $context = stream_context_create(
  			array('http' => array('method' => "POST", 'header' => "Content-Type: text/xml", 'content' => $request,)));

      // connect
      $connect = file_get_contents($this->endpoint, false, $context);

      if($this->verbose)
      	print $connect;

      // retrieve the result
      $response = xmlrpc_decode($connect);

      // display the result on screen
      if (xmlrpc_is_fault($response)) {
        trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
              print_r ($response);
        return NULL;
      }

      if ($this->verbose)
        print_r ($response);

      return $response;
	}

	/**
	* Function for generating a random string, used for
	* generating a token for the XML-RPC session
	*/
	private function getUniqueCode($length = "10"){
	  $code = md5(uniqid(rand(), true));
	  if ($length != "") return substr($code, 0, $length);
	  else return $code;
	}
}



