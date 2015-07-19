<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    
    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {
    
    $view = new View();
    
    $view->setViewsDir($config->application->viewsDir);
    
    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {
            
            $volt = new VoltEngine($view, $di);
            
            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));
            
            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));
    
    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter($config->database->toArray());
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();
    
    return $session;
});

/**
 * Register default router
 */
$di->set('router', function () {
    $router = new Phalcon\Mvc\Router();
    // Route root to HomeController
    $router->add("/", array(
        'controller' => 'home',
        'action' => 'index'
    ));
    // Route login to AuthController
    $router->add("/login", array(
        'controller' => 'auth',
        'action' => 'index'
    ));
    
    return $router;
});

/**
 * Set dispatcher's events manager to catch either Dispatcher::EXCEPTION_HANDLER_NOT_FOUND or Dispatcher::EXCEPTION_ACTION_NOT_FOUND
 */
$di->set('dispatcher', function () use ($di) {

    // Get an EventsManager
    $evManager = $di->getShared('eventsManager');

    // Attach a listener
    $evManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

        // Handle 404 exceptions
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'error',
                        'action'     => 'show404'
                    ));
                    return false;
            }
        }
    });

    $dispatcher = new MvcDispatcher();

    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($evManager);

    return $dispatcher;

}, true);

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function () {
    $flash = new Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
    return $flash;
});

/**
 * Register the flashSession service with custom CSS classes
 */
$di->set('flashSession', function () {
    $flashSession = new Phalcon\Flash\Session(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
    return $flashSession;
});
