<?php

namespace Application\Controller\Plugin;


use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Moj_rbac\Service\RbacManager;

class AccessPlugin extends AbstractPlugin
{
    private $rbacManager;
    
    public function __construct(RbacManager $rbac) 
    {
        $this->rbacManager=$rbac;
    }
    /**
     * 
     * @param type $uprawnienie
     * @param type $parametry - Optional params (used only if an assertion is associated with permission).
     * @return type boolrn
     */
    public function __invoke($uprawnienie,$parametry=[]) 
    {
        return $this->rbacManager->isGranted(null, $uprawnienie, $parametry);
    }
}

