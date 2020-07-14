<?php
require __DIR__.'/../vendor/autoload.php';

use App\Format\FormatInterface;
use App\Format\JSON;
use App\Format\XML;
use App\Format\YAML;
use App\Format\BaseFormat;
use App\Format\FromStringInterface;
use App\Format\NamedFormatInterface;

use App\Service\Serializer;
use App\Controller\IndexController;
use App\Container;

print_r("Annotations<br /><br />");

$data = [
  "name" => "John",
  "surname" => "Doe"
];

$serializer = new Serializer(new JSON());
$controller = new IndexController($serializer);

$container = new Container();
$container->addService('format.json', function() use ($container){
  return new JSON();
});
$container->addService('format.xml', function() use ($container){
  return new XML();
});
$container->addService('format', function() use ($container){
  return $container->getService('format.json');
}, FormatInterface::class);

$container->loadServices('App\\Service');
$container->loadServices('App\\Controller');

var_dump($container->getServices());

echo "<hr />";
var_dump($container->getService('App\\Controller\\IndexController')->index());
var_dump($container->getService('App\\Controller\\PostController')->index());

