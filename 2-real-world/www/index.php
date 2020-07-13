<?php
require_once(__DIR__ . '/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader( __DIR__);
$twig = new \Twig\Environment($loader, [
    'cache' => sys_get_temp_dir(),
]);

$name = getenv('NAME', true);
echo $twig->render('index.html', ['name' => $name]);

?>
