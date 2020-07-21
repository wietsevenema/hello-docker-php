<?php
require_once(__DIR__ . '/../vendor/autoload.php');

/*
 * This is a script to demonstrate
 * 
 *  - Using Redis for sessions.
 *  - Calling a service.
 *
 * In a real app you might want to use a framework.
 */

// REDIS: Set up session storage in redis.
ini_set("session.save_handler","redis");
$redis_url = getenv('REDIS_URL', true);
ini_set("session.save_path", "$redis_url?timeout=1&persistent=1");

$session = session_start();
// If you don't check this return 
// you will get a (silent) warning.
if (!$session){ 
  throw new Exception("Failed to store sessions in Redis");
}

// Add to cart insecurely (don't use GET
// to change user state)
if( isset($_GET['add']) ){
  $_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
  array_push($_SESSION['cart'], htmlspecialchars($_GET['add'], ENT_NOQUOTES));
  header("Location: /");
  exit();
}

// Clear cart insecurely 
if( isset($_GET['clear']) ){
  $_SESSION['cart'] = [];
  header("Location: /");
  exit();
}

// Initialize template library
$loader = new \Twig\Loader\FilesystemLoader( __DIR__);
$twig = new \Twig\Environment($loader, [
  'cache' => sys_get_temp_dir(),
  'debug' => getenv('DEBUG', true)
]);

// CALLING A SERVICE: Load 10 random products
$client = new GuzzleHttp\Client([
  // Set time-out, default is to wait forever
  'connect_timeout' => .1, 
  'read_timeout' => .5
]); 
$product_url = getenv('PRODUCTS_URL', true);
$response = $client->get("$product_url/random");
$products = json_decode(
  $response->getBody(), 
  false, 
  10, 
  JSON_THROW_ON_ERROR // If you do not set this, it won't error
);

// Render template
echo $twig->render('index.html', [
  'products' => $products,
  'cart' => $_SESSION['cart'],
]);