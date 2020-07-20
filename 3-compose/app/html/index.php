<?php
require_once(__DIR__ . '/../vendor/autoload.php');

/*
 * Warning: 
 *
 * This is just to demonstrate
 *  - Redis for sessions
 *  - Calling a service.
 *
 * I don't know what I am doing. 
 * Please write better PHP in production
 */

// Set up session 
ini_set("session.save_handler","redis");

$redis_url = getenv('REDIS_URL', true);
ini_set("session.save_path", "$redis_url?timeout=1&persistent=1");

$session = session_start();
// If you don't check this return you will get a (silent) warning.
if (!$session){ 
  throw new Exception("SESSION FAILED");
}

// Add to cart insecurely 
if( isset($_GET['add']) ){
  $_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
  array_push($_SESSION['cart'], htmlspecialchars($_GET['add'], ENT_NOQUOTES));
  header("Location: /");
  exit();
}

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

// Load products
$client = new GuzzleHttp\Client(); // FIXME Add timeout (default is unlimited)
$product_url = getenv('PRODUCTS_URL', true);
$response = $client->get("$product_url/random");
$products = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);

// Render template
echo $twig->render('index.html', [
  'products' => $products,
  'cart' => $_SESSION['cart'],
]);