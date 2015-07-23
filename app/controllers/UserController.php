<?php

class UserController extends ControllerBase
{

    public function indexAction()
    {
        $data = array();
        $data["menu"] = "Responden";
        $data["menu_desc"] = "pengaturan pengisi kuesioner (responden)";
        $data["menu_icon_class"] = "ion ion-ios-people";
        
        $this->view->setVars(array(
            "data" => $data
        ));
    }

}