<?php

use Phalcon\Mvc\View;

class UserController extends ControllerBase
{
    const DEFAULT_PASSWORD = "myDefaultPassword256";

    public function indexAction()
    {
        $data = array();
        $data["menu"] = "Responden";
        $data["menu_desc"] = "pengaturan pengisi kuesioner (responden)";
        $data["menu_icon_class"] = "ion ion-ios-people";
        
        //Fetch all active respondent records
        $respondenRs = Users::find(array(
            "id_role = :responden_role_id: AND active = true",
            "bind" => array("responden_role_id" => Roles::findFirstByName("Respondent")->getIdRole()))
        );
        
        $this->view->setVars(array(
            "data"        => $data,
            "respondenRs" => $respondenRs
        ));
    }
    
    public function formAction()
    {
        //Disable level main layout
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    }
    
    public function updateAction()
    {
        //Disable view
        $this->view->disable();
        //As usual, catch and process posted data
        if ($this->request->isPost() &&
            in_array($action = $this->request->getPost("action"), array("Add", "Update"))) {
            if (empty($company = ucfirst(htmlspecialchars($this->request->getPost("company_name", "trim"))))) {
                $this->flashSession->error("Nama perusahaan belum diisi. Mohon isi nama perusahaan dan ulangi proses kembali!");
                return $this->response->redirect("responden");
            }
            
            $email = htmlspecialchars($this->request->getPost("email", "trim"));
            
            if ($action == "Add") {
                $login_name = htmlspecialchars($this->request->getPost("username", "trim"));
                if (empty($login_name)) {
                    $this->flashSession->error("Username belum diisi. Mohon isi kolom username dan ulangi proses kembali!");
                    return $this->response->redirect("responden");
                }
                
                $name = ucfirst(htmlspecialchars($this->request->getPost("first_name", "trim")));
                if (empty($name)) {
                    $this->flashSession->error("Nama Depan belum diisi. Mohon isi kolom Nama Depan dan ulangi proses kembali!");
                    return $this->response->redirect("responden");
                }
                
                if (!empty($this->request->getPost("last_name", "trim"))) {
                    $name = $name." ".ucfirst(htmlspecialchars($this->request->getPost("last_name", "trim")));
                }
                
                $user = new Users();
                $user->setLoginName($login_name)->setName($name)->setCompanyName($company)->setEmail($email);
                //Set the default password
                $user->setPassword($this->security->hash(self::DEFAULT_PASSWORD));
                //Set role_id to Respondent's role id
                $user->setIdRole(Roles::findFirstByName("Respondent")->getIdRole());
                
                if (!$user->save()) {
                    $this->flashSession->error("Fatal Error! Error occured while adding new user record");
                    foreach ($user->getMessages() as $err) {
                        $this->flashSession->error($err);
                    }
                    return $this->response->redirect("responden");
                }
            }
            
            $this->flashSession->success("Data telah berhasil disimpan ke database.");
        }
        
        return $this->response->redirect("responden");
    }

}