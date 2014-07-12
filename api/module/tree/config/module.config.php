return array(
    'router' => array(
        'routes' => array(
            'test-hello-world' => array(
                'type'    => 'Literal',
                    'options' => array(
                    'route' => '/hello/world',
                    'defaults' => array(
                        'controller' => 'tree\Controller\Hello',
                        'action'     => 'world',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'tree\Controller\Hello' => 'tree\Controller\HelloController',
        ),
    ),
    // ... other configuration ...
);