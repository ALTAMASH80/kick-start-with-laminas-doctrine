<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host'          => 'localhost',
                    'charset'       => 'utf8mb4',
                    'collate'       => 'utf8mb4_unicode_ci',
                ],
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    // pick any listeners you need
                    \Gedmo\Tree\TreeListener::class,
                    \Gedmo\Timestampable\TimestampableListener::class,
                    \Gedmo\Sluggable\SluggableListener::class,
                    \Gedmo\Loggable\LoggableListener::class,
                    \Gedmo\Sortable\SortableListener::class,
                ],
            ],
        ],
        'driver' => [
            // defines an annotation driver with two paths, and names it `lrphpt_annotation_driver`
            'lrphpt_annotation_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    realpath(__DIR__ . '/../../module/Application/src/Entity'),
                    realpath(__DIR__ . '/../../vendor/gedmo/doctrine-extensions/src/Loggable/Entity'),
                ],
            ],
            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => [
                'drivers' => [
                    // register `lrphpt_annotation_driver` for any entity under namespace `My\Namespace`
                    'Application\\Entity' => 'lrphpt_annotation_driver',
                    'Gedmo\\Loggable\\Entity' => 'lrphpt_annotation_driver',
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'migrations_executed',
                    'version_column_name' => 'version',
                    'version_column_length' => 255,
                    'executed_at_column_name' => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
                'migrations_paths' => ['Lrphpt\Migrations' => realpath(__DIR__ . '/../../scripts/orm/migrations')],
                'all_or_nothing' => true,
                'check_database_platform' => true,
            ],
        ],
    ],
    'session_containers' => [
        \Laminas\Session\Container::class,
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'session_config'  => [
        'gc_maxlifetime' => 7200,
    ],
    'service_manager' => [
        'aliases' => [
            'Laminas\Authentication\AuthenticationService' => 'lmcuser_auth_service',
        ],
        'abstract_factroies' => [
            \Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory::class,
        ],
    ],
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RouteGuard' => [
                'home' => ['*'],
                'lmcuser/login'    => ['guest'],
                'lmcuser/register' => ['guest'], // required if registration is enabled
                'lmcuser*'         => ['member'] // includes logout, changepassword and changeemail
            ]
        ],
        'role_provider' => [
            'LmcRbacMvc\Role\ObjectRepositoryRoleProvider' => [
                'object_manager'     => 'doctrine.entitymanager.orm_default', // alias for doctrine ObjectManager
                'class_name'         => \Application\Entity\HierarchicalRole::class, // FQCN for your role entity class
                'role_name_property' => 'name', // Name to show
            ],
        ],
    ],
];
