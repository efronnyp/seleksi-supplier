<?php

use Phalcon\Mvc\View;

/**
 * ErrorController
 */
class ErrorController extends \Phalcon\Mvc\Controller
{

    public function show404Action()
    {
        $this->response->setStatusCode(404, 'Not Found');
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        $this->view->pick('error/404');
    }

}
