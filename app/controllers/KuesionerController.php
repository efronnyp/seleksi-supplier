<?php

use Phalcon\Mvc\View;

class KuesionerController extends ControllerBase
{

    private function mergeKuesionerAccess($kue_id, $chosen_responden) {
        foreach (KuesionerAccess::findByIdKuesioner($kue_id) as $k) {
            if (($i = array_search($k->getIdResponden(), $chosen_responden)) === false) {
                if (!$k->delete()) {
                    $this->flash->error("Fatal Error! Unable to delete kuesioner data.");
                    foreach ($k->getMessages() as $err) {
                        $this->flash->error($err);
                    }
                    return false;
                }
            } else {
                array_splice($chosen_responden, $i, 1);
            }
        }
        
        foreach ($chosen_responden as $id_responden) {
            $kue_access = new KuesionerAccess();
            $kue_access->setIdKuesioner($kue_id)->setIdResponden($id_responden);
            
            if (!$kue_access->save()) {
                $this->flash->error("Unable to add new kuesioner access records");
                foreach ($kue_access->getMessages() as $err) {
                    $this->flash->error($err);
                }
                return false;
            }
        }
        
        return true;
    }
    
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
            ->where("kh.active = true AND kc.active = true");
        
        if ($this->isResponden()) {
            $kuesioner_access = KuesionerAccess::find(array(
                "id_responden = :id_responden:",
                "bind" => array("id_responden" => $this->session->get("auth")["user"]->getIdUser())
            ));
            
            $values = array(""); $i = 0;
            foreach ($kuesioner_access as $k) $values[$i++] = $k->getIdKuesioner();
            
            $kuesionerRs = $kuesionerRs->inWhere("kh.id_kuesioner", $values);
        }
        
        $kuesionerRs = $kuesionerRs->groupBy("kh.name")
            ->getQuery()->execute();
        
        $this->view->setVars(array(
            "data"        => $data,
            "kuesionerRs" => $kuesionerRs
        ));
    }
    
    public function listBoxAction() {
        //Disable level main layout view
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        //Fetch all active kuesioner(s) with kriteria count provided in each of them
        return $this->indexAction();
    }
    
    public function detailAction() {
        //Disable view
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        //Get posted kuesioner id
        if ($this->request->isPost() &&
            !empty($kue_id = (int) $this->request->getPost("kue_id", "int"))) {
            $result = array("success" => true);
            
            $kuesioner_head = KuesionerHead::findFirst(array(
                "id_kuesioner = :kue_id: AND active = true",
                "bind" => array("kue_id" => $kue_id)
            ));
            if (empty($kuesioner_head)) {
                return $this->flashSession->error("Fatal Error! Kuesioner records not found with the specified id.");
            }
            
            if ($this->isResponden()) {
                $kue_access = KuesionerAccess::findFirst(array(
                    "id_kuesioner = :id_kuesioner: AND id_responden = :id_responden:",
                    "bind" => array(
                        "id_kuesioner" => $kue_id,
                        "id_responden" => $this->session->get("auth")["user"]->getIdUser()
                    )
                ));
                
                if (empty($kue_access)) {
                    return $this->flashSession->error("Damn! You dant permited to access this kuesioner");
                }
            }
            
            $result["kue_name"] = $kuesioner_head->getName();
            
            //Fetch and pair all kriteria items in this kuesioner
            $kue_krit_items = $this->modelsManager->createBuilder()
                ->columns(array("k.id_kue_krit", "k.id_kriteria", "p.deskripsi AS kriteria", "c.deskripsi AS sub_kriteria"))
                ->addFrom("KuesionerChain", "k")
                ->leftJoin("Kriteria", "k.id_kriteria = c.id_kriteria", "c")
                ->leftJoin("Kriteria", "c.parent_id = p.id_kriteria", "p")
                ->where("k.id_kuesioner = :kue_id: AND k.active = true", array("kue_id" => $kue_id))
                ->orderBy("k.sequence_no")
                ->getQuery()
                ->execute();
            
            if ($kue_krit_items->count() < 1) {
                return $this->flashSession->error("Fatal Error! Too bad, we are unable to retrieve ".
                        "kriteria items record for this kuesioner");
            }
            
            $result["kriteria_items"] = array();
            for ($i = 0; $i < $kue_krit_items->count(); $i++) {
                $k = $kue_krit_items[$i];
                $result["kriteria_items"][$i] = array(
                    "kriteria" => $k->kriteria,
                    "sub_kriteria" => $k->sub_kriteria
                );
                
                if ($this->isResponden()) {
                    $result["kriteria_items"][$i]["id_kue_krit"] = $k->id_kue_krit;
                    $kue_value = KuesionerValue::findFirst(array(
                        "id_kue_krit = :id_kue_krit: AND id_responden = :id_responden:",
                        "bind" => array(
                            "id_kue_krit"  => $k->id_kue_krit,
                            "id_responden" => $this->session->get("auth")["user"]->getIdUser()
                        )
                    ));
                    
                    if (!empty($kue_value)) {
                        $result["kriteria_items"][$i]["weight"] = $kue_value->getWeight();
                        $result["readonly"] = true;
                    }
                } else {
                    $result["kriteria_items"][$i]["id_kriteria"] = $k->id_kriteria;
                }
            }
            
            $this->view->setVar("result", $result);
        }
    }
    
    public function respondenListAction() {
        //Disable main level view
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        //Process only if http post request
        if ($this->request->isPost()) {
            $responden = array();
            $exclude_user_id = array("");
            $i = 0;
            
            if (!empty($kue_id = (int) $this->request->getPost("kue_id", "int"))) {
                //Fetch already selected respondents data
                $kue_access = $this->modelsManager->createBuilder()
                    ->columns(array("k.id_responden", "k.status" ,"u.name", "u.company_name"))
                    ->addFrom("KuesionerAccess", "k")
                    ->leftJoin("Users", "k.id_responden = u.id_user", "u")
                    ->where("k.id_kuesioner = :kue_id:", array("kue_id" => $kue_id))
                    ->getQuery()
                    ->execute();
                
                for (; $i < $kue_access->count(); $i++) {
                    $k = $kue_access[$i];
                    $responden[$i] = array(
                        "selected" => true,
                        "id_responden" => $k->id_responden,
                        "status" => $k->status,
                        "name" => $k->name,
                        "company" => $k->company_name
                    );
                    $exclude_user_id[$i] = $k->id_responden;
                }
            }
            
            //Fetch all not selected yet respondents data from Users table
            $users = Users::query()
                ->notInWhere("id_user", $exclude_user_id)
                ->andWhere("id_role = :role_id:", array("role_id" => Roles::findFirstByName("Respondent")->getIdRole()))
                ->andWhere("banned = false")
                ->andWhere("suspended = false")
                ->andWhere("active = true")
                ->execute();
            
            foreach ($users as $u) {
                $responden[$i++] = array(
                    "selected" => false,
                    "id_responden" => $u->getIdUser(),
                    "status" => null,
                    "name" => $u->getName(),
                    "company" => $u->getCompanyName()
                );
            }
            
            $this->view->setVar("responden", $responden);
        }
    }
    
    public function updateAction() {
        // Disable view
        $this->view->disable();
        // Get and process POST data
        if ($this->request->isPost() &&
            !empty($data = $this->request->getPost("data")) &&
            in_array($action = $this->request->getPost("action"), array("Add", "Update"))) {
            if (!isset($data["kue_name"]) || empty($kue_name = ucfirst(htmlspecialchars(trim($data["kue_name"]))))) {
                $this->flash->error("Nama kuesioner belum diisi. Mohon isi nama kuesioner dan ulangi update kembali!");
                return;
            }
            
            if (!isset($data["krit_ids"]) || count($krit_ids = array_map("trim", $data["krit_ids"])) < 1) {
                $this->flash->error("Belum ada kriteria yang dipilih untuk kuesioner ini. ".
                        "Mohon masukkan minimal 1 kriteria dan ulangi update kembali!");
                return;
            }
            
            //Get selected respondents id
            $chosen_responden = isset($data["chosen_responden"]) ? $data["chosen_responden"] : array();
            
            if ($action == "Add") {
                //Kuesioner dupe check
                if (!empty(KuesionerHead::findFirst("name = '".$kue_name."' AND active = true"))) {
                    $this->flash->error("Kuesioner dengan nama <strong>".$kue_name."</strong> sudah ada di database! ".
                            "Mohon gunakan nama lain dan ulangi proses update kembali.");
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
                
                if (!$this->mergeKuesionerAccess($kuesioner_head->getIdKuesioner(), $chosen_responden)) return;
                //It's possible to do direct update after add kuesioner, so store kuesioner id at session
                $this->session->set("#pending_id_kuesioner", $kuesioner_head->getIdKuesioner());
            } else {
                if (empty($kue_id = (int) $this->request->getPost("kue_id", "int"))) {
                    $this->flash->error("Damn! No kuesioner id received!");
                    return;
                } else if ($kue_id == -1 && empty($kue_id = $this->session->get("#pending_id_kuesioner"))) {
                    $this->flash->error("Damn! No kuesioner id stored!");
                    return;
                }
                
                $kuesioner_head = KuesionerHead::findFirst("id_kuesioner = ".$kue_id." AND active = true");
                if (empty($kuesioner_head)) {
                    $this->flash->error("Unable to retrieve kuesioner head record using the specified id!");
                    return;
                }
                
                if ($kuesioner_head->getStatus() == 'L') { //Kuesioner status already locked
                    if ($this->mergeKuesionerAccess($kue_id, $chosen_responden)) {
                        $this->flash->warning("Kuesioner ini telah diisi oleh responden! ".
                                "Hanya hak akses responden saja yang berhasil diupdate ke database.");
                    }
                    return;
                }
                
                //Additional check if kuesioner name changed
                if ($kue_name != $kuesioner_head->getName()) {
                    if (!empty(KuesionerHead::findFirst("id_kuesioner != ".$kue_id." AND name = '".$kue_name."' AND active = true"))) {
                        $this->flash->error("Kuesioner dengan nama <strong>".$kue_name."</strong> sudah ada di database. ".
                                "Mohon gunakan nama yang lain!");
                        return;
                    }
                }
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
                
                if (!$this->mergeKuesionerAccess($kue_id, $chosen_responden)) return;
            }
            
            $this->flash->success("Data kuesioner telah berhasil disimpan ke dalam database.");
        }
    }
    
    public function respondenSubmitAction() {
        //Disable view
        $this->view->disable();
        //Get and process posted data
        if ($this->request->isPost() &&
            !empty($kue_id = (int) $this->request->getPost("detail_kue_id", "int")) &&
            is_array($kue_krit_id = $this->request->getPost("kue_krit_id")) &&
            is_array($krit_weight = $this->request->getPost("krit_weight"))) {
            $id_responden = $this->session->get("auth")["user"]->getIdUser();
            
            if (count($kue_krit_id) < 1 || count($krit_weight) < 1 ||
                count($kue_krit_id) != count($krit_weight)) {
                $this->flashSession->error("Invalid input parameters!");
                return $this->response->redirect("kuesioner");
            }
            
            $kuesioner_head = KuesionerHead::findFirst(array(
                "id_kuesioner = :kue_id: AND active = true",
                "bind" => array("kue_id" => $kue_id)
            ));
            
            if (empty($kuesioner_head)) {
                $this->flashSession->error("Damn! Invalid kuesioner id! Where do you got this fake id from?");
                return $this->response->redirect("kuesioner");
            }
            
            $kue_access = KuesionerAccess::findFirst(array(
                "id_kuesioner = :kue_id: AND id_responden = :id_responden:",
                "bind" => array(
                    "kue_id" => $kue_id,
                    "id_responden" => $id_responden
                )
            ));
            
            if (empty($kue_access) || $kue_access->getStatus() != 'N') {
                $this->flashSession->error("Damn! You either don't have permission to access or ".
                        "you've already submit this kuesioner before");
                return $this->response->redirect("kuesioner");
            }
            
            for ($i = 0; $i < count($kue_krit_id); $i++) {
                $kue_value =  new KuesionerValue();
                $kue_value->setIdKueKrit($kue_krit_id[$i])->setIdResponden($id_responden)->setWeight($krit_weight[$i]);
                
                if (!$kue_value->save()) {
                    $this->flashSession->error("Fatal Error! Could not record kuesioner value!");
                    foreach ($kue_value->getMessages() as $err) {
                        $this->flashSession->error($err);
                    }
                    return $this->response->redirect("kuesioner");
                }
            }
            
            //Prepare to update kuesioner_normalisasi table
            $kue_value = KuesionerValue::query()
                ->columns(array(
                    "id_kue_krit",
                    "id_responden",
                    "weight - ".KuesionerValue::maximum(array(
                        "id_kue_krit IN (".implode(",", $kue_krit_id).")",
                        "column" => "weight"
                    ))." AS normalisasi_value"
                ))
                ->inWhere("id_kue_krit", $kue_krit_id)
                ->execute();
            
            foreach ($kue_value as $k) {
                //Reuse kuesioner_normalisasi record if already exist
                $kue_normalisasi = KuesionerNormalisasi::findFirst(array(
                    "id_kue_krit = :id_kue_krit: AND id_responden = :id_responden:",
                    "bind" => array("id_kue_krit" => $k->id_kue_krit, "id_responden" => $k->id_responden)
                ));
                
                if (empty($kue_normalisasi)) {
                    $kue_normalisasi = new KuesionerNormalisasi();
                }
                
                $kue_normalisasi->setIdKueKrit($k->id_kue_krit)->setIdResponden($k->id_responden);
                //Normalisasi value = kriteria weight - max kuesioner value
                $kue_normalisasi->setValue($k->normalisasi_value);
                
                if (!$kue_normalisasi->save()) {
                    $this->flashSession->error("Unable to update normalisasi table! This is dangerous!");
                    foreach ($kue_normalisasi->getMessages() as $err) {
                        $this->flashSession->error($err);
                    }
                    return $this->response->redirect("kuesioner");
                }
            }
            
            //Prepare to update hasil_matriks_a table
            $kue_normalisasi = KuesionerNormalisasi::query()
                ->columns(array(
                    "id_kue_krit",
                    "id_responden",
                    "value / ".KuesionerNormalisasi::sum(array(
                        "id_kue_krit IN (".implode(",", $kue_krit_id).")",
                        "column" => "value"
                    ))." AS matriks_a_value"
                ))
                ->inWhere("id_kue_krit", $kue_krit_id)
                ->execute();
            
            foreach ($kue_normalisasi as $k) {
                //Reuse hasil_matriks_a record if already exist
                $matriks_a = HasilMatriksA::findFirst(array(
                    "id_kue_krit = :id_kue_krit: AND id_responden = :id_responden:",
                    "bind" => array("id_kue_krit" => $k->id_kue_krit, "id_responden" => $k->id_responden)
                ));
                
                if (empty($matriks_a)) {
                    $matriks_a = new HasilMatriksA();
                }
                
                $matriks_a->setIdKueKrit($k->id_kue_krit)->setIdResponden($k->id_responden);
                //Matriks a value = normalisasi value / total normalisasi value
                $matriks_a->setValue($k->matriks_a_value);
                
                if (!$matriks_a->save()) {
                    $this->flashSession->error("Unable to update matriks a table! This is terrible!");
                    foreach ($matriks_a->getMessages() as $err) {
                        $this->flashSession->error($err);
                    }
                    return $this->response->redirect("kuesioner");
                }
            }
            
            //Prepare to update entropy table
            $entropy_values  = array();
            $dispersi_values = array();
            $dispersi_sum = 0;
            $resp_total = KuesionerValue::count(array(
                "id_kue_krit IN (".implode(",", $kue_krit_id).")",
                "column"  => "DISTINCT id_responden"
            ));
            $ln_responden_total = log($resp_total);
            
            for ($i = 0; $i < count($kue_krit_id); $i++) {
                $entropy_values[$i] = (-1 / $ln_responden_total) *
                    HasilMatriksA::sum(array(
                        "id_kue_krit = :kue_krit_id:",
                        "column" => "value + LN(value)",
                        "bind"   => array("kue_krit_id" => $kue_krit_id[$i])
                    ));
                $dispersi_values[$i] = 1 - $entropy_values[$i];
                $dispersi_sum += $dispersi_values[$i];
            }
            
            for ($i = 0; $i < count($kue_krit_id); $i++) {
                //Check for old entropy records presence, reuse if there's one
                $entropy = Entropy::findFirstByIdKueKrit($kue_krit_id[$i]);
                if (empty($entropy)) {
                    $entropy = new Entropy();
                }
                
                $entropy->setIdKueKrit($kue_krit_id[$i])
                        ->setEntropyValue($entropy_values[$i])
                        ->setDispersiValue($dispersi_values[$i])
                        ->setWeightValue($dispersi_values[$i] / $dispersi_sum);
                
                if (!$entropy->save()) {
                    $this->flashSession->error("Unable to update entropy table! Please undo process manually!");
                    foreach ($entropy->getMessages() as $err) {
                        $this->flashSession->error($err);
                    }
                    return $this->response->redirect("kuesioner");
                }
            }
            
            if (!$kuesioner_head->setStatus('L')->save() || !$kue_access->setStatus('S')->save()) {
                $this->flashSession->error("Fatal Error! Failed to lock/change kuesioner's status for this request.");
            } else {
                $this->flashSession->success("Data telah sukses disimpan ke database.");
            }
            
            return $this->response->redirect("kuesioner");
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