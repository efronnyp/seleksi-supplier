<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    /**
     * Check for user authorization before execute any action
     * @param unknown $dispatcher
     */
    public function beforeExecuteRoute($dispatcher)
    {
        if (!empty($auth = $this->session->get("auth"))) {
            $this->response->redirect("login");
            return false; // Signal dispatcher to halt active operation 
        }
    }

}
