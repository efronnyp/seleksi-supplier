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

    public function submitAction()
    {
        // Disable view
        $this->view->disable();
        // Check and get POSTED data
        if ($this->request->isPost() &&
            !empty($login_name = $this->request->getPost("username")) &&
            !empty($password = $this->request->getPost("password")))
        {
            $user = Users::findFirstByLoginName($login_name);
            if (empty($user)) {
                echo json_encode(array(
                    "success" => false,
                    "errorType" => "username",
                    "errorMessage" => "Username tidak dikenal"
                ));
                return;
            }
            
            if (!$this->security->checkHash($password, $user->getPassword())) {
                echo json_encode(array(
                    "success" => false,
                    "errorType" => "password",
                    "errorMessage" => "Password yang anda masukkan salah"
                ));
                return;
            }
            
            $this->session->set("auth", array(
                "user" => $user
            ));
            echo json_encode(array("success" => true));
        }
    }

    public function logoutAction()
    {
        $this->session->remove("auth");
        $this->flashSession->success("You've been logged out successfully.");
        return $this->response->redirect("login");
    }

}