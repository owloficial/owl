<?php
namespace Zeedhi\Security;

interface SecurityProxyInterceptor{
	public function afterInvoke($request, $response);
	public function beforeInvoke($route);
}
