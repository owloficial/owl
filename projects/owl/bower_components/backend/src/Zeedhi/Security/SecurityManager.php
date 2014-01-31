<?php
namespace Zeedhi\Security;

class SecurityManager {

	private $interceptors = array();

	public function __construct(){

	}

	public function afterInvoke($request){

		foreach ($this->interceptors as $key => $interceptor) {

			$interceptor->afterInvoke($request);
		}
	}
	public function beforeInvoke($request, $respose){
		foreach ($this->interceptors as $key => $interceptor) {
			$interceptor->beforeInvoke($request, $respose);
		}		
	}

	public function addInterfector(SecurityProxyInterceptor $interceptor){
		$this->interceptors[] = $interceptor;
	}

	public function clearInterceptors(){
		$this->interceptors = array();
	}	


}