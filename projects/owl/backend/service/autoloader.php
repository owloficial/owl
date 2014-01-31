<?php
$ds = DIRECTORY_SEPARATOR;
require __DIR__."/../../bower_components/backend/libs/SplClassLoader.php";
$backend = realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'bower_components'.$ds.'backend');
$libsPath = realpath($backend . $ds . 'libs' . $ds);
$zeedhiPath = realpath($backend . $ds . "src");
$appPath = realpath(__DIR__ . $ds . "..".$ds."src");
$builderPath = realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'..'.$ds.'..'.$ds.'builder'.$ds.'src');
$generatedPath = realpath(__DIR__ . $ds . "..".$ds."gen");
$zdPath = realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'bower_components'.$ds.'backend'.$ds.'src');

function registerNamespace($namespace, $path) {
    $loader = new SplClassLoader($namespace, $path);
    $loader->register();
}

registerNamespace('Respect', $libsPath);
registerNamespace('Zeedhi', $zeedhiPath);
registerNamespace('Callback', $appPath);
registerNamespace('Controller', $appPath);
registerNamespace('ParameterizedQuestionary', $appPath);
registerNamespace('UtilModel', $appPath);
registerNamespace('Service', $appPath);
registerNamespace('Model', $appPath);
registerNamespace('Util', $zdPath);
registerNamespace('ZeedhiDev', $zdPath);
registerNamespace('Temp', $appPath);
registerNamespace('Doctrine', $libsPath);
registerNamespace('Symfony', $libsPath);
registerNamespace('Generated', $generatedPath);

require_once __DIR__."/../../bower_components/backend/libs/Twig/Autoloader.php";




