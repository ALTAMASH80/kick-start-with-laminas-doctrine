<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $zfcDatagrid = null;

    public function __construct(){
    }

    public function indexAction()
    {
        return new ViewModel();
    }
}
