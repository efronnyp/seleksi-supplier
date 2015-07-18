<?php

class HomeController extends ControllerBase
{

    public function indexAction() {
        $data = array();
        $data["menu"] = "Home";
        $data["menu_icon_class"] = "fa fa-home";
        
        $this->view->setVars(array(
            "data" => $data
        ));
    }

}

