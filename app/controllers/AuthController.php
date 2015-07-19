<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class AuthController extends Controller
{

    public function indexAction()
    {
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        $this->view->pick("login/index");
    }

    public function logoutAction()
    {
        $this->session->remove("auth");
        $this->flashSession->success("You've been logged out successfully.");
        return $this->response->redirect("login");
    }

}