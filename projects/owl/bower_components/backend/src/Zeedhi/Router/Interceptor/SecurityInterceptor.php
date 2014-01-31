<?php
namespace Zeedhi\Router\Interceptor;

/**
 *
 * @author tuliopinto
 */
interface SecurityInterceptor {

    public function beforeInvoke($route);
    public function afterInvoke($request, $respose);

}