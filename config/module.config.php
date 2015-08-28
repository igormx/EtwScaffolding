<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'etwscaffolding-controller-tableGateway' => 'EtwScaffolding\Controller\TableGatewayController',
            'etwscaffolding-controller-model' => 'EtwScaffolding\Controller\ModelController'
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'etwscaffolding-service-dbintrospection' => 'EtwScaffolding\Service\DBIntrospection',
            'etwscaffolding-service-codegenerator' => 'EtwScaffolding\Service\CodeGenerator'
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'generate-tableGateway' => array(
                    'options' => array(
                        'route' => 'etw generate tableGateway [--dbAdapter=] [--module=] [--table=] [--allTables]',
                        'defaults' => array(
                            'controller' => 'etwscaffolding-controller-tableGateway',
                            'action' => 'generate'
                        )
                    )
                ),
                'generate-model' => array(
                    'options' => array(
                        'route' => 'etw generate model [--dbAdapter=] [--module=] [--table=] [--allTables]',
                        'defaults' => array(
                            'controller' => 'etwscaffolding-controller-model',
                            'action' => 'generate'
                        )
                    )
                )
            )
        )
    )
);