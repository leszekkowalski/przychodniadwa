<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Application\Polaczenie\LekarzPolaczenie;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;


class IndexController extends AbstractController
{
    
    protected $lekarzDb;
    
    
    public function __construct(LekarzPolaczenie $lekarzDb) {
        $this->lekarzDb=$lekarzDb;
    }
    
    public function indexAction()
    {

        $paginator=$this->lekarzDb->paginatorLekarz(true);
        
        
     // Set the current page to what has been passed in query string,
    // or to 1 if none is set, or the page is invalid:
    $page = (int) $this->params()->fromQuery('page', 1);
    $page = ($page < 1) ? 1 : $page;
    
    $paginator->setCurrentPageNumber($page);
    // Set the number of items per page to 2:
    $paginator->setItemCountPerPage(2);   
        
        return new ViewModel([
            'baseUrl'=>$this->baseUrl,
            'paginator'=>$paginator,
                ]);
    }
    public function pokazAction() {
       
    }
}
