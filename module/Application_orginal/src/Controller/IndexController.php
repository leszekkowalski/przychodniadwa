<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\LoginForm;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
         $request   = $this->getRequest();
   $form=new \Application\Form\LoginForm();
    $viewModel = new ViewModel(['form' => $form]);


    if (! $request->isPost() ){
       
        return $viewModel;
    }
    
    // $lekarz=new Lekarz('','');
    // $this->lekarzDodajForm->setInputFilter($lekarz->getInputFilter());
    
    
    $form->setData($request->getPost());

    if (! $form->isValid()) {
        return $viewModel;
    }
    $lekarz = $form->getData();
    
    
    exit();
    }
}
