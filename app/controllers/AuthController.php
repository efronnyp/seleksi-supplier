<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class AuthController extends Controller
{

    public function indexAction()
    {
        if (!empty($this->session->get("auth"))) { // Already logged in?
            return $this->response->redirect("/"); // Go home, you're drunk!
        }
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
            $user = Users::findFirst(array(
                "login_name = :login_name: AND active = true",
                "bind" => array("login_name" => $login_name)
            ));
            
            if (empty($user)) {
                echo json_encode(array(
                    "success" => false,
                    "errorType" => "username",
                    "errorMessage" => "Username tidak dikenal"
                ));
                return;
            } else if ($user->isBanned()) {
                echo json_encode(array(
                    "success" => false,
                    "errorType" => "username",
                    "errorMessage" => "Username ini tidak dapat digunakan kembali"
                ));
                return;
            } else if ($user->isSuspended()) {
                echo json_encode(array(
                        "success" => false,
                        "errorType" => "username",
                        "errorMessage" => "Untuk sementara, username ini tidak dapat digunakan"
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
                "user" => $user,
                "role" => Roles::findFirstByIdRole($user->getIdRole())
            ));
            echo json_encode(array("success" => true));
        }
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect("login");
    }

}