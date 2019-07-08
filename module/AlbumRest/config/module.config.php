<?php
return array(

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'album-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album-rest/:action[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AlbumRest\Controller\AlbumRestController',
                        'action' => 'document',
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Application\Cache\Redis' => 'Application\Service\Factory\RedisFactory',
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'album-rest' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
