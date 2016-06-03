<?php
    class uipatients extends uimodule {
        protected $moduleid    = 'patients';
        protected $modulename  = 'Patients Management';
        protected $menutxt     = 'Patients';
        protected $url         = 'patients';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",                  $this->getstr("perm1")],
                ["search",              $this->getstr("perm2")],
                ["add",                 $this->getstr("perm3")],
                ["list",                $this->getstr("perm4")],
                ["view",                $this->getstr("perm5")],
                ["manage",              $this->getstr("perm6")],
                //["edit",                $this->getstr("perm7")],
            ];
        }

        public function getpage($home) {
            if (!$this->cando("dv")) { /* no permissions to be here */
                return error404();
            }
            $ctt = "";

            /* check for POST stuff */
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();

            if (isgd("view") && $this->cando("view")) {
                $home["view"] = isgd("view");
                $ctt .= $this->vaed_patient($home, isgd("view"));
            }
            else {
                $ctt .= $this->defaultview($home, isgd("view"));
            }

            return $ctt;
        }




        private function defaultview($home) {
            $res = "";
            $fn["0"] = ispd("fn0");
            $subform = "";
            $info    = "";

            /* search a patient */
            if ($this->cando("search")) {
                if ((issetp("searchpatient") || issetp("addpatient")) && $fn["0"] !== "") {
                    $dbres = db_do(self::getsql("getpatientbyidentifier"), ["id" => $fn["0"]]);
                    if (valdbres($dbres, 1, 1)) {
                        $patient = $dbres[0];
                        $subform = bb_af_a($this->getstr("btn1"), gettourl($home, ["view" => $patient["id"]]), "", "bbafin bfafbtn");
                    } else
                    if ($this->cando("add")) {
                        $info = bbwarn($this->getstr("war1"));
                        $subform = bb_af_form_hidden("fn0", $fn["0"]);
                        $subform .= bb_af_a($this->getstr("btncancel"), gettourl($home), "margin-right: 10px;", "bbafin bfafbtn rmbg");
                        $subform .= bb_af_form_submit("addpatient", $this->getstr("btncreate"), FALSE, "", "bbacgreen");
                    } else {
                        $info = bbwarn($this->getstr("war3"));
                    }
                } else {
                    if ((issetp("searchpatient") || issetp("addpatient")) && $fn["0"] === "") {
                        $info = bbwarn($this->getstr("war4"));
                        formalert("fn0");
                    }
                    $fn["0"] = bb_af_form_intxt("fn0", $fn["0"], "", fia("fn0"));
                    $subform = bb_af_form_submit("searchpatient", $this->getstr("btnsearch"), FALSE, "", "");
                }

                $res .= bb_af_sep($this->getstr("sep1"), "", "", "bbafpsep bbafpsepbig bbafpsepf");
                $res .= $info;
                $res .= bb_af_form(
                    bb_af_sep($this->getstr("lbl1"),      $fn["0"],   "", "bbafstxt" . fia("fn0")) .
                    bb_af_txt($subform, "", "bbaftxtsubmit")
                );
            }

            /* list all patients */
            if ($this->cando("list")) {
                $viewurl = gettourl($home, "view") . "=";
                $res .= bb_af_sep($this->getstr("sep2"), "", "", "bbafpsep bbafpsepbig");
                $trs = bb_atabler_fromquery(self::getsql("getpatients"), "", ["identifier", ["bbdbdatetostr"], "uname"],
                $viewurl);
                /* the table */
                $res .= bb_atable_hfi([
                    $this->getstr("lbl1"), "",
                    $this->getstr("lbl2"), "",
                    $this->getstr("lbl3"), "",
                ], $trs);
            }

            return $res;
        }











        private function vaed_patient($home, $id) {
            $pid   = $id;                    /* patient id */
            $apid  = isgd("appointment");    /* appointment id */
            $aid   = isgd("action");         /* action id */
            $addap = isgd("addappointment"); /* event id */
            $addac = isgd("addaction");      /* activity id */

            /* pid must exists, appointment also should exists, if not, jump to default */
            $dbres = db_do(self::getsql("getpatientbyid"), ["id" => $pid]);
            if (!valdbres($dbres, 1, 1))
                return bberror($this->getstr("errnoexist"), "");
            $patient = $dbres[0];

            /* if this is an action view, then get that action's appointment id */
            if ($aid !== "") {
                $dbres = db_do(self::getsql("getactionbyid"), ["id" => $aid]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"), "");
                $action = $dbres[0];
                $apid = $action["appointmentid"];
            }
            /* appointment must also exist and if not set use the default */
            if ($apid !== "") {
                $dbres = db_do(self::getsql("getappointmentbyid"), ["id" => $apid]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"), "");
                $appointment = $dbres[0];
            } else {
                $dbres = db_do(self::getsql("getappointmentbyid"), ["id" => $patient["appointmentid"]]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"), "");
                $appointment = $dbres[0];
            }
            $apid = $appointment["appointmentid"]; /* anyway, set the appointment id */

            /* get path to the DPE */
            $dbres = db_do(self::getsql("getappointmentpath"), ["id" => $apid]);
            if (!valdbres($dbres))
                return bberror($this->getstr("errnoexist"), "");
            $epath = $dbres;

            /* let's make sure that this appointment belong, in the end, to the $pid */
            $proot = $epath[0]; //$epath[count($epath) - 1];
            if (!$proot["parentid"] === NULL ||
                !($proot["id"] === $patient["appointmentid"])) {
                return bberror($this->getstr("errnoexist"), "");
            }

            /* are we trying to add an action in this appointment? */
            if ($addac !== "" && $this->cando("manage")) {
                $dbres = db_do(self::getsql("getactionaddbyid"),
                    ["roleid" => $this->role, "id" => $addac]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"), "");
                $activity = $dbres[0];
            }
            /* are we trying to add an appointment in this appointment? */
            else if ($addap !== "" && $this->cando("manage")) {
                $dbres = db_do(self::getsql("getappointmentpaddbyid"),
                    ["roleid" => $this->role, "id" => $addap]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"), "");
                $event = $dbres[0];
            }


            $contentview = "";
            $faction = gettourl($home);
            /* ADDACTION VIEW */
            if (isset($activity)) {
                $dbres = db_do(self::getsql("getactivityquestions"), ["id" => $addac]);
                if (!valdbres($dbres, 0))
                    return bberror($this->getstr("errsql", __LINE__), "");
                $questions = $dbres;

                $contentview .= bb_af_form_hidden("fnz", $patient["identifier"]);
                $contentview .= bb_af_form_hidden("fny", $activity["name"]);

                $faction = gettourl($home, ["appointment" => $apid]);
                /* print some activity info */
                $contentview .= bb_af_form_hidden("fna", $activity["tid"]);
                $contentview .= bb_af_form_hidden("fnb", $apid);
                $contentview .= bb_af_sep($this->getstr("sepna1"), "", "", "bbafpsep");
                $contentview .= bb_af_sep($this->getstr("lblna1"), $activity["name"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblna1"), $activity["tname"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblna2", bbit($activity["tname"])), bb_af_form_indate("fn0", date("Y-m-d")), "", "bbafsitem afhover");
                $contentview .= bb_af_sep($this->getstr("lblna3", bbit($activity["tname"])), bb_af_form_intime("fn1", date("H:i")), "", "bbafsitem afhover");

                $contentview .= bb_af_sep($this->getstr("sepna2"), "", "", "bbafpsep");
                $contentview .= $this->buildaction($questions);
                $contentview .= bb_af_txt(
                    bb_af_a($this->getstr("btncancel"), $faction, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("addaction", $this->getstr("btn2"), FALSE, "", "bbacgreen"), "", "bbaftxtsubmit");
            }

            /* ADDAPPOINTMENT VIEW */
            else if (isset($event)) {
                $faction = gettourl($home, ["appointment" => $apid]);
                /* print some event info */
                $contentview .= bb_af_form_hidden("fna", $event["tid"]);
                $contentview .= bb_af_form_hidden("fnb", $apid);
                $contentview .= bb_af_form_hidden("fnz", $patient["identifier"]);
                $contentview .= bb_af_form_hidden("fny", $event["name"]);
                $contentview .= bb_af_sep($this->getstr("sepne1"),  "", "", "bbafpsep");
                $contentview .= bb_af_sep($this->getstr("lblne1"), $event["name"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblne4"), $event["tname"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblne2", bbit($event["tname"])), bb_af_form_indate("fn0", date("Y-m-d")), "", "bbafsitem afhover");
                $contentview .= bb_af_sep($this->getstr("lblne3", bbit($event["tname"])), bb_af_form_intime("fn1", date("H:i")), "", "bbafsitem afhover");

                $contentview .= bb_af_txt(
                    bb_af_a($this->getstr("btncancel"), $faction, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("addappointment", $this->getstr("btncreate"), FALSE, "", "bbacgreen"), "", "bbaftxtsubmit");
            }
            /* ACTION VIEW */
            else if (isset($action)) {
                $bbpathaction = bbpathitem(/*"(" . $action["atname"] . ") " . /**/$action["aname"],
                        gettourl($home, ["action" => $action["id"]]));

                $contentview .= bb_af_sep($this->getstr("sepva1"), "", "", "bbafpsep");
                $contentview .= bb_af_sep($this->getstr("lblva1"), $action["aname"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblva4"), $action["atname"], "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblva2"), date("d/m/Y, H:i:s", strtotime( $action["date"])), "", "bbafstxt");
                $contentview .= bb_af_sep($this->getstr("lblva3"),  $action["uname"], "", "bbafstxt");
                //$contentview .= bb_af_sep("",  $action[""], "", "bbafstxt");

                /* get the answers */
                $contentview .= bb_af_sep($this->getstr("sepva2"), "", "", "bbafpsep");
                $dbres = db_do(self::getsql("getactionanswers"), ["id" => $action["id"]]);
                if (!valdbres($dbres, 0))
                    return bberror($this->getstr("errsql", __LINE__), "");
                $answeritems = $dbres;

                foreach ($answeritems as $answer) {
                    if ($answer["itname"] === "Separator") {
                        $contentview .= bb_af_sep($answer["qquestion"], "", "", "bbafpsep");
                        continue;
                    }
                    if ($answer["info"] === NULL || $answer["info"] === "") {
                        $contentview .= bb_af_sep($answer["qquestion"], $answer["answeritems"], "", "bbafstxt view");
                    } else {
                        $contentview .= bb_af_sep($answer["qquestion"],
                        bb_af_sep($answer["answeritems"], $answer["info"], "", "bbafsobs view"),
                        "", "bbafstxt");
                    }
                }

                /* get the older ones */
                /*$dbres = db_do(self::getsql("getoldactionanswers"), ["id" => $action["id"]]);
                if (!valdbres($dbres, 0))
                    return bberror($this->getstr("errsql", __LINE__), "");
                $olditems = $dbres;
                if (count($olditems) > 0)
                    $contentview .= bb_af_sep("OLDER QUESTIONS", "", "", "bbafpsep");
                foreach ($olditems as $answer) {
                    if ($answer["info"] === NULL || $answer["info"] === "") {
                        $contentview .= bb_af_sep($answer["qquestion"], $answer["answeritems"], "", "bbafstxt view");
                    } else {
                        $contentview .= bb_af_sep($answer["qquestion"],
                        bb_af_sep($answer["answeritems"], $answer["info"], "", "bbafsobs view"),
                        "", "bbafstxt");
                    }
                }*/
            }
            /* DEFAULT VIEW : APPOINTMENT VIEW */
            else {
                if ($appointment["id"] !== $patient["appointmentid"]) { /* not the DPE */
                    $contentview .= bb_af_sep($this->getstr("sepve1"), "", "", "bbafpsep");
                    $contentview .= bb_af_sep($this->getstr("lblve1"), $appointment["ename"], "", "bbafstxt");
                    $contentview .= bb_af_sep($this->getstr("lblve4"), $appointment["etname"], "", "bbafstxt");
                    $contentview .= bb_af_sep($this->getstr("lblve2"), date("d/m/Y, H:i:s", strtotime( $appointment["date"])), "", "bbafstxt");
                    $contentview .= bb_af_sep($this->getstr("lblve3"),  $appointment["uname"], "", "bbafstxt");
                    $contentview .= bb_af_sep($this->getstr("sep7"), "", "", "bbafpsep");
                } else {
                    $contentview .= bb_af_sep($this->getstr("sep5"), "", "", "bbafpsep");
                }
                /* get the list of permitted create items in this appointment */
                $dbres = db_do(self::getsql("getactivityevents"), ["roleid" => $this->role]);
                $aeitems = "";
                foreach ($dbres as $item) {
                    $prefix = $item["type"] === "event" ? "&#10097; " : "";
                    $golink = $item["type"] === "event" ? "addappointment" : "addaction";
                    $aeitems .= bb_af_form_inselboxitem($item["tid"], $prefix . "(" . $item["tname"] . ") " . $item["name"],
                        "golink=\"$golink\"");
                }

                /* add appointment/action button */
                $bbchildren = "";
                if ($aeitems !== "")
                    $bbchildren = bb_listitem(bb_af_form_inselbox("", $aeitems), NULL, "evadd");

                /* now, get all the appointments and actions of this appointment */
                $dbres = db_do(self::getsql("getappointmentchildren"),
                    ["roleid" => $this->role, "id" => $apid]);
                if (!valdbres($dbres, 0))
                    return bberror("S#$% happens", "");
                $echildren = $dbres;

                /* build them */
                foreach ($echildren as $child) {
                    if ($child["type"] === "action") {
                        $bbchildren .= bb_af_a(bb_listitem("(" . $child["atname"] . ") " . $child["aname"],
                                date("d/m/Y", strtotime($child["date"])), "evlist"),
                            gettourl($home, ["action" => $child["id"]]));
                    } else
                    if ($child["type"] === "appointment") {
                        $bbchildren .= bb_af_a(bb_listitem("(" . $child["etname"] . ") " . $child["ename"],
                                date("d/m/Y", strtotime($child["date"])), "evlistsub"),
                            gettourl($home, ["appointment" => $child["id"]]));
                    }
                }
                $contentview .= bb_licontainer($bbchildren);
            }
            $editthis = "";
            /* patient's general info */
            $patinfo  = "";
            $nohide = FALSE;
            if ($nohide || ($appointment["id"] === $patient["appointmentid"])) { /* in the DPE */
                $patinfo .= bb_af_sep($this->getstr("lbl2"), date("d/m/Y, H:i:s", strtotime( $patient["date"])), "", "bbafstxt");
                $patinfo .= bb_af_sep($this->getstr("lbl3"),  $patient["uname"], "", "bbafstxt");
                /* edit button */
                $editurl = "";
                $editthis = ($this->cando("edit") && FALSE ?
                    bb_af_sep("",
                        bb_af_a($this->getstr("btnedit"), $editurl, "width: initial;", "bbafin bfafbtn fright")
                            , "", "bbafstxt") : "");
            }

            /* build the path */
            $bbpath = "";
            $bbpath .= bbpathitem($patient["identifier"], gettourl($home), true); /* home */
            if (count($epath) > 1) {
                for ($i = 1; $i < count($epath); $i++) {
                    $bbpath .= bbpathitem($epath[$i]["name"],
                        gettourl($home, ["appointment" => $epath[$i]["id"]])); /* appointment name */
                }
            } else {
                //at home
            }
            /* append action to the path */
            $bbpath .= isset($bbpathaction) ? $bbpathaction : "";

            /* UI */
            $classes = "bbafstxt";
            $fclass = "bbfedit";
            $res = bb_af_sep($this->getstr("sep3"), "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= bb_af_form(
                bb_af_sep($this->getstr("lbl1"),  $patient["identifier"], "", $classes) .
                $patinfo .
                $editthis .
                bb_licontainer($bbpath, "margin-top: 25px") .
                $contentview,
            $faction, "POST", "", $fclass);
            return $res;
        }

        private function buildaction($questions) { // TODO
            $res = "";
            foreach ($questions as $q) {
                $exc      = strinstr("exclusive", $q["itoptions"])? TRUE : FALSE;
                $qst      = $q["qquestion"];
                $qid      = $q["qid"];
                $values   = explode(";::;;", $q["inputs"]);
                $valuesid = explode(", ", $q["inputsid"]);
                $cmbox    = TRUE;

                $inid = "fnval" . $qid;
                $item  = "";
                $class = "afhover ";

                $obs = TRUE;

                if (count($values) === 1) { /* primitive type */
                    if ($values["0"] === "Number") {
                        $item   = bb_af_form_innum($inid);
                        $class .= "bbafsitem";
                    } else
                    if ($values["0"] === "Text") {
                        $item   = bb_af_form_intxt($inid);
                        $class .= "bbafsitem";
                    } else
                    if ($values["0"] === "Long Text") {
                        $item   = bb_af_form_intextarea($inid);
                        $obs = FALSE;
                        $class .= "bbafsitem";
                    } else
                    if ($values["0"] === "Date") {
                        $item   = bb_af_form_indate($inid);
                        $class .= "bbafsitem";
                    } else
                    if ($values["0"] === "Time") {
                        $item   = bb_af_form_intime($inid);
                        $class .= "bbafsitem";
                    } else
                    if ($values["0"] === "Separator") {
                         $class = "bbafpsep small";
                    } else {
                        /* ERROR unknown type */
                    }
                } else
                if (count($values) === 2 && $exc) { /* boolean type */
                    $item   = bb_af_form_inbool($inid . "[]",
                        $values[0], $values[1], $values[0], $values[1], "&#9760;");
                    $class .= "bbafsitem";
                } else { /* list type */
                    if ($exc === TRUE) { /* exclusive list */
                        if (isset($cmbox) && $cmbox) {
                            foreach ($values as $val) {
                                $item .= bb_af_form_inselboxitem($val, $val);
                            }
                            $item = bb_af_form_inselbox($inid . "[]", $item);
                            $class .= "bbafsitem";
                        } else {
                            foreach ($values as $val) {
                                $item .= bb_af_form_inradio($inid . "[]", $val, $val, "&#9773;");
                            }
                            $class .= "bbafsitem bbafsitemlist";
                        }
                    } else { /* non exclusive */
                        foreach ($values as $val) {
                            $item .= bb_af_form_incheck($inid . "[]", $val, $val, "&#9730;");
                        }
                        $class .= "bbafsitem bbafsitemlist";
                    }
                }
                if ($obs) {
                    $res .= bb_af_sep($qst,
                        bb_af_sep($item, bb_af_form_intextarea("fnobs" . $qid, "", "", "bbacyel small"), "", "bbafsobs"),
                            "", $class);
                }
                else {
                    $res .= bb_af_sep($qst, $item, "", $class);
                }
            }
            return $res;
        }






        private function checkpost($home) {
            $res = "";
            if (issetp("addpatient") && $this->cando("add")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "valp", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_ne", [self::getsql("getpatientbyidentifier"), ["id" => ispd("fn0")]],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                ];
                if ((($validate = validate_form($validate)) !== "")) {
                    //$_GET = arraddrem($home, "add");
                    return $validate;
                }
                $dbres = db_do(self::getsql("addpatient"), [
                    "identifier" => ispd("fn0"),
                    "userid"     => $this->user,
                ], $this->getstr("log1", ispd("fn0")));
                if (!valdbres($dbres)) {
                    return bberror($this->getstr("errsql", __LINE__));
                }
                return bbinfo($this->getstr("inf1", ispd("fn0")));
            } else
            if (issetp("addappointment") && $this->cando("manage")) {
                $validate = [
                    /* permissions */
                    ["valdb_e", [self::getsql("getappointmentpaddbyid"), ["roleid" => $this->role, "id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]],
                ];
                if ((($validate = validate_form($validate)) !== "")) {
                    return $validate;
                }
                $dbres = db_do(self::getsql("addappointment"), [
                    "eventid"  => ispd("fna"),
                    "parentid" => ispd("fnb"),
                    "userid"   => $this->user,
                    "date"     => ispd("fn0") . " " . ispd("fn1"),
                ], $this->getstr("log6", ispd("fnz"), ispd("fny")));
                if (!valdbres($dbres)) {
                    $res .= bberror($this->getstr("errsql", __LINE__));
                    return $res;
                }
                $res .= bbinfo($this->getstr("inf3", ispd("fnz"), ispd("fny")));
            } else
            if (issetp("addaction") && $this->cando("manage")) {
                $validate = [
                    /* permissions */
                    ["valdb_e", [self::getsql("getactionaddbyid"), ["roleid" => $this->role, "id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]],
                ];
                if ((($validate = validate_form($validate)) !== "")) {
                    return $validate;
                }

                $dbres = db_do(self::getsql("addaction"), [
                    "activityid"    => ispd("fna"),
                    "appointmentid" => ispd("fnb"),
                    "userid"        => $this->user,
                    "date"          => ispd("fn0") . " " . ispd("fn1"),
                ], $this->getstr("log5", ispd("fnz"), ispd("fny")));
                if (!valdbres($dbres)) {
                    return bberror("Unknown error");
                }
                $actionid = $dbres["0"]["id"];

                $qitems = "";
                $ct = 0;
                foreach ($_POST as $key => $val) {
                    if (!strinstr("fnval", $key)) continue;
                    /* the answer */
                    $qid = substr($key, 5);
                    $obs = ispd("fnobs" . $qid);
                    $dbres = db_do(self::getsql("addanswer"), [
                        "actionid"   => $actionid,
                        "questionid" => $qid,
                        "info"       => $obs,
                    ], "IGNOREPLAEASE");
                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $aid = $dbres["0"]["id"];

                    /* the answeritems */
                    $vals = is_array(ispd($key)) ? ispd($key) : [ispd($key)];
                    foreach ($vals as $val) {
                        $qitems .= "(:aid$ct, :v$ct), ";
                        $args["aid$ct"] = $aid;
                        $args["v$ct"] = $val;
                        $ct++;
                    }
                }
                $qitems = substr($qitems, 0, -2);
                $dbres = db_do(self::getsql("addansweritems", $qitems), $args, "IGNOREPLAEASE");
                if (!valdbres($dbres)) {
                    return bberror($this->getstr("errsql", __LINE__));
                }
                $res .= bbinfo($this->getstr("inf4", ispd("fnz"), ispd("fny")));
            }
            return $res;
        }


        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what, $pager = "") {
            switch ($what) {
                /* * * * * * * * * * * * * * * * * * * * * PATIENTS * * * * */
                case "getpatientbyid": return <<<EOFF
                SELECT * FROM patientinfo WHERE id = :id;
EOFF;
                case "getpatientbyidentifier": return <<<EOFF
                SELECT * FROM patientinfo WHERE identifier = :id;
EOFF;
                case "addpatient": return <<<EOFF
                INSERT INTO patient (identifier, userid) VALUES (:identifier, :userid);
EOFF;
                case "getpatients": return <<<EOFF
                SELECT id, date, identifier, uname FROM patientinfo ORDER BY identifier;
EOFF;
                case "getactivityevents": return <<<EOFF
                SELECT * FROM activityeventpermissions
                    WHERE roleid = :roleid
                      AND modes LIKE '%add%'
                    ORDER BY type, tname, name;
EOFF;
                case "": return <<<EOFF

EOFF;
                case "": return <<<EOFF

EOFF;
                /* * * * * * * * * * * * * * * * * * * * * APPOINTMENTS * * * * */
                case "getappointmentbyid": return <<<EOFF
                SELECT * FROM appointmentinfo WHERE id = :id;
EOFF;
                case "getdefaultpatientappointmentbyid": return <<<EOFF
                SELECT * FROM defaultpatientappointment WHERE identifier = :identifier;
EOFF;
                case "getappointmentpath": return <<<EOFF
                SELECT * FROM appointmentpath(:id);
EOFF;
                case "getappointmentchildren": return <<<EOFF
                SELECT * FROM appointmentchildren
                    WHERE parentid = :id
                      AND roleid = :roleid
                      AND modes LIKE '%read%'
                    ORDER BY date DESC;
EOFF;
                case "getappointmentpaddbyid": return <<<EOFF
                SELECT * FROM activityeventpermissions
                    WHERE roleid = :roleid
                      AND modes LIKE '%add%'
                      AND type = 'event'
                      AND tid = :id;
EOFF;

                case "addappointment": return <<<EOFF
                INSERT INTO appointment (eventid, parentid, userid, date)
                    VALUES (:eventid, :parentid, :userid, :date);
EOFF;
                /* * * * * * * * * * * * * * * * * * * * * ACTION * * * * */
                case "getactionbyid": return <<<EOFF
                SELECT * FROM actioninfo WHERE id = :id;
EOFF;
                case "getactionanswers": return <<<EOFF
                SELECT * FROM getactionanswers(:id);
EOFF;
                case "getoldactionanswers": return <<<EOFF
                SELECT * FROM getoldactionanswers(:id);
EOFF;
                case "getactionaddbyid": return <<<EOFF
                SELECT * FROM activityeventpermissions
                    WHERE roleid = :roleid
                      AND modes LIKE '%add%'
                      AND type = 'activity'
                      AND tid = :id;
EOFF;
                case "getactivityquestions": return <<<EOFF
                SELECT * FROM activityquestions WHERE aid = :id ORDER BY qprecedence;
EOFF;
                case "getactivityinfo": return <<<EOFF
                SELECT * FROM activityinfo WHERE id = :id;
EOFF;
                case "addaction": return <<<EOFF
                INSERT INTO action (appointmentid, activityid, userid, date)
                    VALUES (:appointmentid, :activityid, :userid, :date)
                    RETURNING id;
EOFF;
                case "addanswer": return <<<EOFF
                INSERT INTO answer (actionid, questionid, info)
                    VALUES (:actionid, :questionid, :info)
                    RETURNING id;
EOFF;
                case "addansweritems": return <<<EOFF
                INSERT INTO answeritem (answerid, val) VALUES $pager;
EOFF;
                case "": return <<<EOFF

EOFF;
            }
            return "";
        }

        /****************************************************
                           STRINGS
        /****************************************************/
    }
    return new uipatients();
?>
