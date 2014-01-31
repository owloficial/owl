<?php
$ds = DIRECTORY_SEPARATOR;
require __DIR__."/../../../../backend/libs/SplClassLoader.php";
$backend = realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'..'.$ds.'..'.$ds.'backend');
$libsPath = realpath($backend . $ds . 'libs' . $ds);
$zeedhiPath = realpath($backend . $ds . "src");
$appPath = realpath(__DIR__ .$ds.'..'.$ds.'..'.$ds.'..'.$ds.'..'.$ds."developer".$ds."zeedhiTool".$ds."src");
$generatedPath = realpath(__DIR__ . $ds . "..".$ds . "..".$ds . "..".$ds . "..".$ds."developer".$ds."zeedhiTool".$ds."gen");
$external      = realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'..'.$ds.'..'.$ds."external");

function registerNamespace($namespace, $path) {
    $loader = new SplClassLoader($namespace, $path);
    $loader->register();
}

// C:\workfolder\zeedhi-dyman\developer\zeedhiTool\src\Controller\Controller.php

registerNamespace('Respect', $libsPath);
registerNamespace('Zeedhi', $zeedhiPath);
registerNamespace('Controller', $appPath);
registerNamespace('Model', $external);
registerNamespace('Doctrine', $libsPath);
registerNamespace('Symfony', $libsPath);
registerNamespace('Generated', $generatedPath);
registerNamespace('Callback', $external);