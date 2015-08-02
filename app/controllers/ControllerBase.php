<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller
{

    /**
     * Return true if current logged in user's role name is "Respondent"
     */
    public function isResponden()
    {
        return $this->session->get("auth")["role"]->getName() == "Respondent";
    }

    /**
     * Check for user authorization before execute any action
     * @param unknown $dispatcher
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        if (empty($auth = $this->session->get("auth"))) {
            $this->response->redirect("login");
            return false; // Signal dispatcher to halt active operation 
        } else {
            $controller_name = $dispatcher->getControllerName();
            $action_name = $dispatcher->getActionName();
            
            $conditions = "resource = :resource: AND action = :action: AND :my_role: & granted_role";
            $permissions = Permissions::findFirst(array(
                $conditions,
                "bind" => array("resource" => $controller_name, "action" => $action_name, "my_role" => $auth["role"]->getIdRole())
            ));
            
            if (empty($permissions)) {
                $permissions = Permissions::findFirst(array(
                    $conditions,
                    "bind" => array("resource" => $controller_name, "action" => "*", "my_role" => $auth["role"]->getIdRole())
                ));
                
                if (empty($permissions)) {
                    $this->response->setStatusCode("404", "Not Found");
                    $this->dispatcher->forward(array(
                        "controller" => "error",
                        "action"     => "show404"
                    ));
                }
            }
            
            $this->view->setVar("responden", $auth["role"]->getName() == "Respondent");
        }
    }

}
