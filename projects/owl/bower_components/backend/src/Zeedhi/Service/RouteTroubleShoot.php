<?php
namespace Zeedhi\Service;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 10/07/13
 * Time: 08:42
 * To change this template use File | Settings | File Templates.
 */
class RouteTroubleShoot
{

    public static function errorHandler(array $err) {
        echo "Erro...";
        echo "<pre>";
        var_dump($err);
    }

    public static function exceptionHandler(\Exception $e) {
        echo $e->getMessage();
    }

}
