<?php
declare(strict_types=1);

namespace Posts\Controller;


use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PostController extends AbstractActionController{
    
    protected $entityManager;
    
    public function indexAction(){
        
        return new ViewModel();
    }
}