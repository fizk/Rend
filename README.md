### How to get
use composer
```
   {
        "name": "what-ever",
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/fizk/Rend"
            }
        ],
        "require": {
            "php": ">=5.5",
            "fizk/rend": "dev-master"
        },
    }
```

    module.config.php

    return [
        'view_manager' => [
            'strategies' => [
                'MessageStrategy',
            ]
        ]
    ]


    service.config.php

    return [
        'factories' => [
            'MessageStrategy' => 'Rend\View\Strategy\MessageFactory',
        ]
    ]


    Module.php


    namespace WhatEverNameSpace;

    use Laminas\Mvc\MvcEvent;

    class Module
    {
        public function onBootstrap(MvcEvent $e)
        {
            register_shutdown_function(new \Rent\Event\ShutdownErrorHandler());

            $eventManager = $e->getApplication()->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, new Rend\Event\ApplicationErrorHandler());
        }

        public function getConfig()
        {
            return include __DIR__ . '/config/module.config.php';
        }

        public function getServiceConfig()
        {
            return include __DIR__ . '/config/service.config.php';
        }

        public function getAutoloaderConfig()
        {
            return [
                'Laminas\Loader\StandardAutoloader' => [
                    'namespaces' => array(
                        __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];
        }
    }


read more here
https://github.com/fizk/Loggjafarthing/wiki/Controllers
