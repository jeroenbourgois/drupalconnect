<?php
/*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
* MA 02110-1301, USA.
*/
/*
*
* name: PHP interface to Drupal via XMLRPC
* by onyx@chalcedony.co.nz
* http://chalcedony.co.nz
*/

/**
* REQUIRES Service module and XMLRPC server set in the drupal site, with API Key authentication
* *********************************************************************************************/
class Drupal_connect {
  function __construct( $domain = '', $apiKey = '', $endPoint = '', $credentials = '', $func ='', $param='', $verbose = TRUE )
  {
    // set local domain or IP address
    // this needs to match the domain set when you created the API key
    $this->domain = $domain;

    // set API key
    $this->kid = $apiKey;

    // set target web service endpoint
    $this->endpoint = $endPoint;

    // extended debugging
    $this->verbose = $verbose;

    // user credentials for login
    $this->credentials = $credentials;

    // function within drupal services to run; REMEMBER PERMISSIONS!
    $this->func = $func;

    // parameters to pass to drupal function
    $this->param = $param;
  }

  /*
  * anonymous connection function
  * to open a PHP/Drupal session
  *
  * Note, this method does not require the API key
  */
  public function dru_connect ($endPoint){
    // needs only $endPoint passed to it.

    // set vars for this connection
    $method_name = 'system.connect';
    $required_args = array();

    // prepare the request
    $request = xmlrpc_encode_request(
    $method_name, $required_args
    );

    // prepare the request context
    $context = stream_context_create(
      array(
      'http' => array(
        'method' => "POST",
        'header' => "Content-Type: text/xml",
        'content' => $request,
        )
      )
    );

    // connect
    var_dump($endPoint);
    $connect = file_get_contents($endPoint, false, $context);

    // retrieve the result
    $response = xmlrpc_decode($connect);
    $output ='';

    // display the result on screen
    if (xmlrpc_is_fault($response)) {
      print 'Error from '.$endPoint.':';
      trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
    } else {
      // let's look at what came back
      $output.= 'Received from '.$endPoint.': ';
      $output.= ''. htmlspecialchars(print_r($response, true)) .'';
      $output.='';
    }

    if ($this->verbose) {
      //	print_r ($response);
      print $output;
    }

    return $response;
  }
  
  /*
  * Now we have a session, we can login to
  * retrieve our article feed
  *
  * This is our first use of the API key
  */
  public function auth_connect( $domain, $endPoint, $sessid, $kid, $user_credentials, $method_name='user.login', $param, $verbose) {


    // set vars for this connection
    $nonce = getUniqueCode("10");
    $timestamp = (string) strtotime("now");
    $required_args = array();

    // now prepare a hash
    $hash_parameters = array(
      $timestamp,
      $domain,
      $nonce,
      $method_name,
    );

    // create a hash using our API key
    $hash = hash_hmac("sha256", implode(';', $hash_parameters), $kid);

    // prepared the arguments for this service
    // you can see the required arguments on the method's test page
    // http://www.mysite.com/admin/build/services
    $required_args = array(
      $hash,
      $domain,
      $timestamp,
      $nonce,
      $sessid,
    );

    // any user-defined arguments for this service
    // here we use the login credentials if it is a user.login method, else pass the method parameters
    if ($method_name =='user.login') {
        $user_args = $user_credentials;
    } else {
      $user_args = $param;
    }
    
    print_r($user_args);
    
    // add the arguments to the request
    foreach ($user_args as $arg) {
      array_push($required_args, $arg);
    }

    // prepare the request
    $request = xmlrpc_encode_request($method_name, $required_args);

    // prepare the request context
    $context = stream_context_create(array(
                                      'http' => array(
                                      'method' => "POST",
                                      'header' => "Content-Type: text/xml",
                                      'content' => $request,
                                      )
                                    )
                                );

    $output ='';
    
    // connect
    $connect = file_get_contents($endPoint, false, $context);
    
    print $connect;
    
    // retrieve the result
    $response = xmlrpc_decode($connect);

    // display the result on screen
    if (xmlrpc_is_fault($response)) {
      print 'Error';
      trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
    } else {
      $output .='Received';
      $output .= ''. htmlspecialchars(print_r($response, true)) .'';
      
      if ($method_name =='user.login'){
        // SAVE OUR USER OBJECT - we'll need it later
        $user = new stdClass();
        $user = (object) $response['user'];
      }

      // ALSO SAVE OUR LOGGED IN SESSID - we'll need it to logout
      // sessid changes after we login
      $loggedinsessid = $sessid;

      if ($this->verbose) {
        //	print_r ($response);
        print $output;
      }

      return $response;
    }
  }
}
?>