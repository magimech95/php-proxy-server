<?php
set_time_limit (60);
// error_reporting(0);
// ini_set('display_errors', 0);

// use Proxy\Factory;
// use Proxy\Response\Filter\RemoveEncodingFilter;
// use Symfony\Component\HttpFoundation\Request;

require 'vendor/autoload.php';
// require '/proxy/src/Encrypt.php';

use Guzzle\Http\Client;

// Create a client and provide a base URL
$client = new Client('https://www.google.com');
// Create a request with basic Auth
// $request = $client->get('/MageshS')->setAuth('MageshS', '11206270mag');
$request = $client->get('/');
// Send the request and get the response
$response = $request->send();
echo $response->getBody();
// >>> {"type":"User", ...
echo $response->getHeader('Content-Length');


?>