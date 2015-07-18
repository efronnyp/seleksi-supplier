<?php

use Phalcon\Mvc\View;

class KriteriaController extends ControllerBase
{

    public function indexAction() {
        $data = array();
        $data["menu"] = "Kriteria";
        $data["menu_desc"] = "pengaturan kriteria penilaian";
        $data["menu_icon_class"] = "ion ion-android-checkbox-outline";
        // Fetch existing kriteria data from kriteria table
        /*
         * $kriteriaRs = $this->modelsManager->createBuilder()
         * ->columns(array("c.id_kriteria", "c.deskripsi AS sub_kriteria", "p.deskripsi AS kriteria"))
         * ->addFrom("Kriteria", "c")
         * ->leftJoin("Kriteria", "c.parent_id = p.id_kriteria", "p")
         * ->where("c.parent_id IS NOT NULL AND c.active = true")
         * ->orderBy("kriteria")
         * ->getQuery()
         * ->execute();
         */
        $kriteriaRs = Kriteria::getKriteriaAndSubKriteria($this->modelsManager->createBuilder());
        
        $this->view->setVars(array(
            "data" => $data,
            "kriteriaRs" => $kriteriaRs
        ));
    }

    public function formAction() {
        // Disable main view layout
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        // Read POST data and process kriteria id if exist
        $data = array();
        
        if ($this->request->isPost()) {
            $id = (int) $this->request->getPost("id", "int");
            if (!empty($id)) {
                $kriteria = Kriteria::getKriteriaAndSubKriteriaById($id, $this->modelsManager->createBuilder());
                
                if (empty($kriteria)) {
                    $this->flashSession->error("Fatal Error! Unknown kriteria id received by server");
                    return;
                }
                
                $data["id"] = $kriteria->id_kriteria;
                $data["kriteria"] = $kriteria->kriteria;
                $data["sub_kriteria"] = $kriteria->sub_kriteria;
                $this->session->set("#pending_id_kriteria", $id);
            } else {
                $data["id"] = $data["kriteria"] = $data["sub_kriteria"] = "";
            }
        }
        
        $this->view->setVar("data", $data);
    }

    public function updateAction() {
        // Disable view
        $this->view->disable();
        // Get and process POST data
        if ($this->request->isPost() &&
            !empty($data = $this->request->getPost("data", "trim")) &&
            in_array($action = $this->request->getPost("action"), array("Add", "Update"))) {
            $kriteria_val = ucfirst(htmlspecialchars($data["kriteria"]));
            if (empty($kriteria_val)) {
                $this->flashSession->error("Kolom kriteria harus diisi. Silahkan ulangi update kembali.");
                return $this->response->redirect("kriteria");
            }
            
            $sub_kriteria_val = ucfirst(htmlspecialchars($data["sub_kriteria"]));
            if ($action == "Add") {
                $parent = $this->findOrCreateParent($kriteria_val);
                if (empty($parent)) {
                    return $this->response->redirect("kriteria");
                }
                
                if (!empty($sub_kriteria_val)) {
                    $kriteria = new Kriteria();
                    $kriteria->setParentId($parent->getIdKriteria());
                    $kriteria->setDeskripsi($sub_kriteria_val);
                }
            } else {
                if (empty($id = (int) $this->session->get("#pending_id_kriteria"))) {
                    $this->flashSession->error("Fatal Error! No pending kriteria id stored in server. Please retry!");
                    return $this->response->redirect("kriteria");
                }
                
                $kriteria = Kriteria::findFirstByIdKriteria($id);
                if (empty($kriteria)) {
                    $this->flashSession->error("Fatal Error! Kriteria not found with the specified id.");
                    return $this->response->redirect("kriteria");
                }
                
                $parent = $this->findOrCreateParent($kriteria_val);
                if (empty($parent)) {
                    return $this->response->redirect("kriteria");
                }
                
                $kriteria->setParentId($parent->getIdKriteria());
                $kriteria->setDeskripsi($sub_kriteria_val);
            }
            
            if (!$kriteria->save()) {
                $this->flashSession->error("Fatal error! Terjadi kegagalan pada saat melakukan update pada tabel kriteria. ".
                        "Kode: UPD01");
                foreach ($kriteria->getMessages() as $err) {
                    $this->flashSession->error($err);
                }
                return $this->response->redirect("kriteria");
            }
            
            $this->flashSession->success("Database updated successfully.");
        }
        
        // Clean pending id_kriteria in session and return
        $this->session->remove("#pending_id_kriteria");
        
        return $this->response->redirect("kriteria");
    }

    public function deltAction() {
        // Disable view
        $this->view->disable();
        // Get and process POST data
        if ($this->request->isPost() && !empty($id = (int) $this->request->getPost("id", "int"))) {
            $kriteria = Kriteria::findFirstByIdKriteria($id);
            if (empty($kriteria)) {
                $this->flashSession->error("Fatal Error! Unable to find the requested kriteria");
                return;
            }
            if (!$kriteria->delete()) {
                $this->flashSession->error("Fatal Error! Unable to delete the requested kriteria record");
                foreach ($kriteria->getMessages() as $err) {
                    $this->flashSession->error($err);
                }
                return;
            }
            
            $this->flashSession->success("Record has been deleted successfully.");
        }
    }

    public function chooseListAction() {
        // Disable main level view
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        if ($this->request->isPost()) {
            $blacklist = $this->request->getPost("exclude");
            if (empty($blacklist)) $blacklist = array("");
            // Fetch all active sub kriteria (parent_id not null) record excluding the blacklist id(s)
            $kriteriaRs = Kriteria::getKriteriaAndSubKriteria($this->modelsManager->createBuilder(), $blacklist);
            
            $this->view->setVar("kriteriaRs", $kriteriaRs);
        }
    }

    /**
     * This function will find kriteria parent's record or create one if doesn't exist yet
     *
     * @param string $kriteria_val            
     * @return Kriteria
     */
    private function findOrCreateParent($kriteria_val) {
        $parent = Kriteria::findFirstByDeskripsi($kriteria_val);
        if (empty($parent)) {
            $parent = new Kriteria();
            $parent->setDeskripsi($kriteria_val);
            if (!$parent->save()) {
                $this->flashSession->error("Fatal error! Terjadi kegagalan pada saat menambahkan parent kriteria. ".
                        "Kode: PRNT01");
                foreach($parent->getMessages() as $err) {
                    $this->flashSession->error($err);
                }
                return null;
            }
        }
        
        return $parent;
    }

}
