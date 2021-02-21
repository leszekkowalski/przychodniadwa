<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Moj_rbac\Service\RbacManager;

class AccessHelper extends AbstractHelper
{
    public $rbac;
    public function __construct(RbacManager $rbac) 
    {
      $this->rbac=$rbac;  
    }
    
    public function __invoke($uprawnienie,$parametry=[]) 
    {
        return $this->rbac->isGranted(null, $uprawnienie, $parametry);
    }
}

