<?php

namespace Moj_rbac\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\HydratorInterface;

class RbacPolaczenie
{
    private $polaczenieRbac;
    
    protected $hydrator;

    public function __construct(AdapterInterface $poleczenie, HydratorInterface $hydrator) 
    {
     $this->polaczenieRbac=$poleczenie;
     $this->hydrator=$hydrator;
    }
    
}
    

    

