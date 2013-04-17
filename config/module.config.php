<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\ApplicationLog\Admin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/application-log',
                    'defaults' => array(
                        'controller' => 'Grid\ApplicationLog\Controller\Admin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\ApplicationLog\Admin\Export' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/application-log/export',
                    'defaults' => array(
                        'controller' => 'Grid\ApplicationLog\Controller\Admin',
                        'action'     => 'export',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\ApplicationLog\Controller\Admin' => 'Grid\ApplicationLog\Controller\AdminController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'DashboardApplicationLogPlugin' => 'Grid\ApplicationLog\Controller\Plugin\DashboardPlugin',
        ),
    ),
    'factory' => array(
        'Grid\ApplicationLog\Model\Log\StructureFactory' => array(
            'dependency'    => 'Grid\ApplicationLog\Model\Log\StructureInterface',
            'adapter'       => array(
                ''          => 'Grid\ApplicationLog\Model\Log\Structure\DefaultFallback',
            ),
        ),
    ),
    'log' => array(
        'application'   => array(
            'writers'   => array(
                'default'   => array(
                    'name'  => 'Grid\ApplicationLog\Model\Log\Writer',
                ),
            ),
        ),
    ),
    'modules'   => array(
        'Grid\Core'  => array(
            'dashboardBoxes' => array(
                'applicationLog'    => array(
                    'order'         => 100,
                    'plugin'        => 'DashboardApplicationLogPlugin',
                    'params'        => array( 10 ),
                    'resource'      => 'applicationLog',
                    'privilege'     => 'view',
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'applicationLog' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/applicationLog',
                'pattern'       => '%s.php',
                'text_domain'   => 'applicationLog',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/application-log/admin/list'       => __DIR__ . '/../view/grid/application-log/admin/list.phtml',
            'grid/application-log/admin/dashboard'  => __DIR__ . '/../view/grid/application-log/admin/dashboard.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
