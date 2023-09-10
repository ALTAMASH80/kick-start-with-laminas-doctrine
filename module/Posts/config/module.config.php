<?php
declare(strict_types=1);

namespace Posts;

return [
    'router' => [
        'routes' => [
            
        ],
    ],
    'controller' => [
        'factories' => [
            
        ],
    ],
    'service_manager' => [
        'factories' => [
            
        ],
    ],
    'doctrine' => [
        'driver' => [
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
            __NAMESPACE__.'_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    realpath(__DIR__ . '/../src/Entity'),
                ],
            ],
            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => [
                'drivers' => [
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    __NAMESPACE__ . '\\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
];