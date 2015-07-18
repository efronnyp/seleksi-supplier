<?php

use Phalcon\Mvc\View;

class KuesionerController extends ControllerBase
{

    public function indexAction() {
        $data = array(
            "menu" => "Kuesioner",
            "menu_desc" => "kumpulan kriteria penilaian yang akan diisi oleh responden",
            "menu_icon_class" => "ion ion-ios-paper-outline"
        );
        //Fetch all active kuesioner(s) with kriteria count provided in each of them
        $kuesionerRs = $this->modelsManager->createBuilder()
            ->columns(array("kh.id_kuesioner", "kh.name", "COUNT(kc.id_kriteria) AS kriteria_count"))
            ->addFrom("KuesionerHead", "kh")
            ->leftJoin("KuesionerChain", "kh.id_kuesioner = kc.id_kuesioner", "kc")
            ->where("kh.active = true AND kc.active = true")
            ->groupBy("kh.name")
            ->getQuery()
            ->execute();
        
        $this->flashSession->warning("Warning");
        $this->flashSession->notice("info");
        $this->flashSession->error("error");
        $this->flashSession->success("success");
        
        $this->view->setVars(array(
            "data"        => $data,
            "kuesionerRs" => $kuesionerRs
        ));
    }
    
    public function listBoxAction() {
        //Disable level main layout view
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        //Fetch all active kuesioner(s) with kriteria count provided in each of them
        $kuesionerRs = $this->modelsManager->createBuilder()
            ->columns(array("kh.id_kuesioner", "kh.name", "COUNT(kc.id_kriteria) AS kriteria_count"))
            ->addFrom("KuesionerHead", "kh")
            ->leftJoin("KuesionerChain", "kh.id_kuesioner = kc.id_kuesioner", "kc")
            ->where("kh.active = true AND kc.active = true")
            ->groupBy("kh.name")
            ->getQuery()
            ->execute();
        
        //Pass required variables to view
        $this->view->setVar("kuesionerRs", $kuesionerRs);
    }
    
    public function detailAction() {
        //Disable view
        $this->view->disable();
        //Get posted kuesioner id
        if ($this->request->isPost() &&
            !empty($kue_id = (int) $this->request->getPost("kue_id", "int"))) {
            $result = array("success" => true);
            
            $kuesioner_head = KuesionerHead::findFirst("id_kuesioner = ".$kue_id." AND active = true");
            if (empty($kuesioner_head)) {
                $result = array(
                    "success" => false,
                    "messages" => array("Fatal Error! Kuesioner records not found with the specified id.")
                );
                echo json_encode($result);
                return;
            }
            
            $result["kue_name"] = $kuesioner_head->getName();
            
            //Fetch and pair all kriteria items in this kuesioner
            $kue_krit_items = $this->modelsManager->createBuilder()
                ->columns(array("k.id_kriteria", "p.deskripsi AS kriteria", "c.deskripsi AS sub_kriteria"))
                ->addFrom("KuesionerChain", "k")
                ->leftJoin("Kriteria", "k.id_kriteria = c.id_kriteria", "c")
                ->leftJoin("Kriteria", "c.parent_id = p.id_kriteria", "p")
                ->where("k.id_kuesioner = :kue_id: AND k.active = true", array("kue_id" => $kue_id))
                ->orderBy("k.sequence_no")
                ->getQuery()
                ->execute();
            
            if ($kue_krit_items->count() < 1) {
                $result = array(
                    "success" => false,
                    "messages" => array("Fatal Error! Too bad, we are unable to retrieve kriteria items record for this kuesioner")
                );
                echo json_encode($result);
                return;
            }
            
            $result["kriteria_items"] = array();
            for ($i = 0; $i < $kue_krit_items->count(); $i++) {
                $k = $kue_krit_items[$i];
                $result["kriteria_items"][$i] = array(
                    "id_kriteria" => $k->id_kriteria,
                    "kriteria" => $k->kriteria,
                    "sub_kriteria" => $k->sub_kriteria
                );
            }
            
            echo json_encode($result);
            $this->session->set("#pending_id_kuesioner", $kue_id);
        }
    }
    
    public function updateAction() {
        // Disable view
        $this->view->disable();
        // Get and process POST data
        if ($this->request->isPost() &&
            !empty($data = $this->request->getPost("data")) &&
            in_array($action = $this->request->getPost("action"), array("Add", "Update"))) {
            if (!isset($data["kue_name"]) || empty($kue_name = ucfirst(htmlspecialchars($data["kue_name"])))) {
                $this->flash->error("Nama kuesioner belum diisi. Mohon isi nama kuesioner dan ulangi update kembali!");
                return;
            }
            
            if (!isset($data["krit_ids"]) || count($krit_ids = array_map("trim", $data["krit_ids"])) < 1) {
                $this->flash->error("Belum ada kriteria yang dipilih untuk kuesioner ini. 
                        Mohon masukkan minimal 1 kriteria dan ulangi update kembali!");
                return;
            }
            
            if ($action == "Add") {
                //Kuesioner dupe check
                if (!empty(KuesionerHead::findFirst("name = '".$kue_name."' AND active = true"))) {
                    $this->flash->error("Kuesioner dengan nama <strong>".$kue_name."</strong> sudah ada di database! 
                            Mohon gunakan nama lain dan ulangi proses update kembali.");
                    return;
                }
                
                $kuesioner_head = new KuesionerHead();
                $kuesioner_head->setName($kue_name);
                
                if (!$kuesioner_head->save()) {
                    $this->flash->error("Fatal Error! Could not create kuesioner head record.");
                    foreach ($kuesioner_head->getMessages() as $err) {
                        $this->flash->error($err);
                    }
                    return;
                }
                
                $kuesioner_chain = new KuesionerChain();
                $kuesioner_chain->setIdKuesioner($kuesioner_head->getIdKuesioner());
                
                for ($i = 0; $i < count($krit_ids);) {
                    $kuesioner_chain->setIdKueKrit(null)->setIdKriteria($krit_ids[$i++])->setSequenceNo($i);
                    if (!$kuesioner_chain->save()) {
                        $this->flash->error("Fatal Error! Could not create kuesioner chain records.");
                        foreach ($kuesioner_chain->getMessages() as $err) {
                            $this->flash->error($err);
                        }
                        return;
                    }
                }
                //It's possible to do direct update after add kuesioner, so store kuesioner id at session
                $this->session->set("#pending_id_kuesioner", $kuesioner_head->getIdKuesioner());
            } else {
                if (empty($kue_id = (int) $this->session->get("#pending_id_kuesioner"))) {
                    $this->flash->error("No pending kuesioner id stored for this session!");
                    return;
                }
                
                $kuesioner_head = KuesionerHead::findFirst("id_kuesioner = ".$kue_id." AND active = true");
                if (empty($kuesioner_head)) {
                    $this->flash->error("Unable to retrieve kuesioner head record using the specified id!");
                    return;
                }
                
                //Save all kuesioner changes. Or even none -_-
                $kuesioner_head->setName($kue_name);
                
                //First, mark all kuesioner kriteria items to inactive
                $result = $this->modelsManager->executeQuery("UPDATE KuesionerChain SET active = false".
                        " WHERE id_kuesioner = :id_kue:", array("id_kue" => $kue_id));
                if (!$result->success()) {
                    $this->flash->error("Unable to perform update for this kuesioner.");
                    foreach ($result->getMessages() as $message) {
                        $this->flash->error($message->getMessage());
                    }
                    return;
                }
                
                for ($i = 0; $i < count($krit_ids); $i++) {
                    $kuesioner_chain = KuesionerChain::findFirst(array(
                        "id_kuesioner = :kue_id: AND id_kriteria = :krit_id: AND sequence_no = :seq_no:",
                        "bind" => array(
                            "kue_id" => $kue_id,
                            "krit_id" => $krit_ids[$i],
                            "seq_no" => $i+1
                        )
                    ));
                    
                    if (empty($kuesioner_chain)) {
                        $kuesioner_chain = new KuesionerChain();
                        $kuesioner_chain->setIdKuesioner($kue_id)->setIdKriteria($krit_ids[$i])->setSequenceNo($i+1);
                    } else {
                        $kuesioner_chain->setActive(true);
                    }
                    
                    if (!$kuesioner_chain->save()) {
                        $this->flash->error("Fatal Error! Could not save changes to kuesioner chain records.");
                        foreach ($kuesioner_chain->getMessages() as $err) {
                            $this->flash->error($err);
                        }
                        return;
                    }
                }
                
                //Finally, save kuesioner head record
                if (!$kuesioner_head->save()) {
                    $this->flash->error("Fatal Error! Error occured while saving kuesioner head record.");
                    foreach ($kuesioner_head->getMessages() as $err) {
                        $this->flash->error($err);
                    }
                    return;
                }
            }
            
            $this->flash->success("Data kuesioner telah berhasil disimpan ke dalam database.");
        }
    }
    
    public function deltAction() {
        //Disable view
        $this->view->disable();
        //Get posted kuesioner id
        if ($this->request->isPost() && !empty($kue_id = (int) $this->request->getPost("kue_id", "int"))) {
            $kuesioner_head = KuesionerHead::findFirstByIdKuesioner($kue_id);
            if (empty($kuesioner_head)) {
                $this->flashSession->error("Data kuesioner tidak ada untuk id tersebut!");
                return $this->response->redirect("kuesioner");
            }
            
            if (!$kuesioner_head->setActive(0)->save()) {
                $this->flashSession->error("Fatal Error! Could not update kuesioner head record.");
                foreach ($kuesioner_head->getMessages() as $err) {
                    $this->flashSession->error($err);
                }
                return $this->response->redirect("kuesioner");
            }
            
            $this->flashSession->success("Kuesioner berhasil dihapus dari database.");
        }
        
        return $this->response->redirect("kuesioner");
    }

}