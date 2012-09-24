<?php
require_once('header.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'modules/feeligo/se3_api.php');

/**
 * Api endpoint script
 *
 * this file is provided as a skeleton implementation of a Feeligo API Endpoint.
 */

/**
 * require the SDK's built-in FeeligoController
 * 
 * you may need to modify the path according to your directory structure
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'modules/feeligo/sdk/lib/controllers/controller.php');

/**
 * instantiate the FeeligoController
 *
 * the constructor expects the singleton instance of FeeligoApi as its argument
 */
$controller = new FeeligoController(Feeligo_Se3_Api::_());

/**
 * process the request
 */
$response = $controller->run();

/**
 * set response headers and output body
 */
header("HTTP/1.1 ".$response->code());
foreach($response->headers() as $k => $v) {
  header("$k: $v");
}
echo $response->body();