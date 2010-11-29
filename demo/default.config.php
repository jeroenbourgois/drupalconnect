<?php
//Set initial connection parameters
$localDomain = '';               //the domain that this php code is on. NEEDS to be input for the api key in the Services module api key setup
$apiKey = ''; // the api key copied from the drupal Services module
$endPoint = ''; //the drupal site's xmlrpc url
$credentials = array(); // the user credentials for attaching to the drupal site. The first connection is anonymous.
$auth_user = array(0 => 'USER',1 => 'PASSWORD');// add the authorised user name and password for connection 0=username,1=password
$func = 'node.get'; //the default function
$param = array(0=>'10'); // the default node, sent as array