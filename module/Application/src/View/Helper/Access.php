<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Moj_rbac\Service\RbacManager;

/**
 * This view helper is used to check user permissions.
 */
class Access extends AbstractHelper 
{
    private $rbacManager = null;
    
    public function __construct(RbacManager $rbacManager) 
    {
        $this->rbacManager = $rbacManager;
    }
    
    public function __invoke($permission, $params = [])
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }
}

