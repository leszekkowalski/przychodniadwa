<?php

namespace Moj_rbac\Controller;

use Application\Controller\AbstractController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Moj_rbac\Model\Rola;

class RolaController extends AbstractController
{
    private $polaczenieRbac;
    
    public function __construct(RbacPolaczenie $polaczenie)
    {
      $this->polaczenieRbac=$polaczenie;
    }
    
    
    public function indexAction()
    {
        return [];
    }
}
