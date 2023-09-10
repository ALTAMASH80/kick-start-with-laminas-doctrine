<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;
use DoctrineORMModule\Service\DoctrineObjectHydratorFactory;
use Application\Factory\UserRegisterFactory;
use Application\Repository\UserRepository;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'alias' => [
            'lmcuser_base_hydrator' => DoctrineObjectHydratorFactory::class,
        ],
        'factories' => [
            'lmcuser_user_hydrator' => DoctrineObjectHydratorFactory::class,
            \Application\Listeners\UserManagementListener::class => ReflectionBasedAbstractFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'navigation' => [
        'default' => [
            'home' => [
                'label' => 'Home',
                'route' => 'home',
//                 'resource' => 'home',
            ],
            'category' => [
                'label' => 'Category',
                'uri' => '#',
                'pages' => [
                    'php' => [
                        'label' => 'PHP',
                        'uri' => 'https://www.php.net',
                    ],
                    'laminas' => [
                        'label' => 'Laminas',
                        'uri' => 'https://getlaminas.org/',
//                         'resource' => 'lmcuser',
                    ],
                    'devider' => [
                        'label' => '--devider--', // most important
                        'uri' => '#',
                    ],
                    'magento' => [
                        'label' => 'Magento',
                        'uri' => 'https://business.adobe.com/products/magento/magento-commerce.html',
                    ],
                ],
            ]
        ]
    ],
    'listeners' => [
         \Application\Listeners\UserManagementListener::class,
    ],
];
