<?php
namespace Zeedhi\Callback;

use Zeedhi\Application;

interface MethodInterface {

    public function __construct(Application $application);
    public function apply($entity);

}