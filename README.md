
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
    
    use Zend\Mvc\MvcEvent;
    
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
                'Zend\Loader\StandardAutoloader' => [
                    'namespaces' => array(
                        __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];
        }
    }
