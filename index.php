<?php
set_time_limit (60);
// error_reporting(0);
// ini_set('display_errors', 0);

use Proxy\Factory;
use Proxy\Response\Filter\RemoveEncodingFilter;
use Symfony\Component\HttpFoundation\Request;

require 'vendor/autoload.php';
require 'src/Encrypt.php';

// $uri = $_SERVER['REQUEST_SCHEME']. "://" .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// $uri = $_SERVER['REQUEST_URI'];
// $c = new App_Sandbox_Cipher('Magesh');
// $encrypted = $c->encrypt($uri);
// die($c->decrypt("5b132560a9cd7841d82681fd8b9dfa7992cf3f9755c8778b"));
// $path = "http://phpserver.2fh.co/".$encrypted;
// $path = "https://college-great.appspot.com/main.php?url=".$uri;
// die("$uri<br>$path");
$path = "http://www.google.com";

// Create the proxy factory.
$proxy = Factory::create();

// Add a response filter that removes the encoding headers.
// $proxy->addResponseFilter(new RemoveEncodingFilter());

// Create a Symfony request based on the current browser request.
$request = Request::createFromGlobals();

// Forward the request and get the response.
$response = $proxy->forward($request)->to($path);
// die($response);
$content = $response-> getContent();
try {
	$gzip = @gzdecode($content);
    } catch (Exception $e) {
        die("I could not create a car");
    }
if($gzip) $response-> setContent($gzip);

$response->headers->set('Access-Control-Allow-Origin', 'http://www.hotstar.com');
$response->headers->set('Access-Control-Allow-Credentials', 'true');

// $response->prepare($request);
// Output response to the browser.
$response->send();

?>