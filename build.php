<?php
$stylesheet = $_GET['xcss'];

if (!$stylesheet) {
    die('Please specify a style sheet.');
}

$stylesheet = __DIR__ . '/' . $stylesheet;

if (!file_exists($stylesheet)) {
    die('Unable to find style sheet.');
}

spl_autoload_register(function($class) {
    $path = __DIR__ . '/src/' . preg_replace('/\\\\/', '/', $class) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

header('Content-type: text/css');

try {
    $xcss = new Xazure\Css\Generator(__DIR__ . '/config.yml');
    echo $xcss->buildStyleSheet($stylesheet);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . ' - Line ' . $e->getLine() . ', File: ' . $e->getFile();
}