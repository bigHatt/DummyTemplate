<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Template\ReverseTemplate;

$t2 = 'Hello, my name is {{name}}. Oh my is {name2}}, and he is {name3}';
$string = "Hello, my name is Dio. Oh my is Jotaro, and he is Polnareff";
$reverse = new ReverseTemplate($t2, $string);
$props = $reverse->getProperties();
var_dump($props);