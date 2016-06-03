<?php
    class uiactivities extends uimodule {
        protected $moduleid    = 'activities';
        protected $modulename  = 'Activities Management';
        protected $menutxt     = 'Activities';
        protected $url         = 'activities';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",          $this->getstr("perm2")],
                ["manage",      $this->getstr("perm3")],
            ];
        }

        public function getpage($home) {
            if (!$this->cando("dv")) { /* no permissions to be here */
                return error404();
            }
            $ctt = "";
            /* An up menu */
            if (!valg("up")) $_GET["up"] = "activities";
            $ctt = upmenu($home, isgd("up"), [
                $this->getstr("menu1"), "activities",
                $this->getstr("menu2"), "activitytypes",
                ""]);
            $home["up"] = $_GET["up"];

            /* check for POST stuff */
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();


            if (valg("up", "activities") && $this->cando("dv")) {
                //$home = arraddrem($home, ["view" => isgd("view")]);
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_activities($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activities($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activities($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activities($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_activities($home);
                }
            } else
            if (valg("up", "activitytypes") && $this->cando("dv")) {
                //$home = arraddrem($home, ["view" => isgd("view")]);
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_activitytypes($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activitytypes($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activitytypes($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_activitytypes($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_activitytypes($home);
                }
            }
            else {
                return error404();
            }
            return $ctt;
        }












        /* * * * * * * * * * * * * * * ACTIVITIES * * * * * * * * * * * * * * */
        private function dv_activities($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep7"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";

            /* get activitytypes */
            $activitytypes = bbselitems_fromquery(self::getsql("getactivitytypes"), [], "id", "name");

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_inselbox("fn0", $activitytypes, "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    bb_af_form_intxt("fn2", "", "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getactivities"), "", ["atname", "name", "description", ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl3"), "",
                $this->getstr("lbl4"), "",
                $this->getstr("lbl5"), "",
                "", "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_activities($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $refer    = "Activity";
            $postpos  = "activity";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* type */
            $fn["1"] = ispd("fn1"); /* name */
            $fn["2"] = ispd("fn2"); /* description */
            $fn["3"] = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")]; /* questions list */
            $fn["4"] = is_array(ispd("fn4")) ? ispd("fn4") : [ispd("fn4")]; /* options */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep8");

                $dbres = db_do(self::getsql("getactivitybyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["activitytypeid"];
                    $fn["1"] = $item["name"];
                    $fn["2"] = $item["description"];
                    $fn["3"] = explode(", ", $item["qids"]);
                    $fn["4"] = explode(", ", $item["options"]);
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                $fn["z"] = $item["atname"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedactivity"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep11");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war3"));

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        (!$editwarning ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete" && !$editwarning) { /* and delete */
                    $title   = $this->getstr("sep12");
                    $desc    = bbwarn($this->getstr("war4", $fn["1"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep10");
                $fclass  = "bbfedit";
                $subline =
                    bb_af_a($this->getstr("btncancel"), $homeurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("add$postpos", $this->getstr("btncreate"), FALSE, "", "bbacgreen");
            }
            $subform = "";
            /* formify */
            if ($id == FALSE || $action === "edit") { /* add or edit */
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";
                $fn["1"] = bb_af_form_intxt("fn1", $fn["1"], "", fia("fn1"));
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", fia("fn2"));

                /* activity types */
                $activitytypes = bbselitems_fromquery(self::getsql("getactivitytypes"), [], "id", "name", $fn["0"]);
                $fn["0"] = bb_af_form_inselbox("fn0", $activitytypes, "", fia("fn0"));

                /* questionid to beautiful divs, fn3 */
                /* get all questions to map them and build the list */
                $dbres = db_do(self::getsql("getquestions"));
                $itemslist = [];
                foreach ($dbres as $item) {
                    $used = in_array($item["id"], $fn["3"]) ? true : false;
                    $itemslist[] = [$item["id"], $item["name"], $item["itname"], $used];
                }
                $subform .= bbbuildexclusivelist("fn3[]", $itemslist, "fn3");
            } else {
                /* print view */
                $fn["0"] = $fn["z"];

                /* list of questions */
                $dbres = db_do(self::getsql("getactivityquestionsbyid"), ["id" => $id]);
                foreach ($dbres as $item) {
                    $subform .= bb_listitem($item["qname"], $item["itname"]);
                }
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_sep($this->getstr("lbl3"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl4"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl5"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("sep9"), "", "", "bbafpsep") .
                $subform .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }



















        /* * * * * * * * * * * * * * * ACTIVITYTYPES * * * * * * * * * * * * * * */
        private function dv_activitytypes($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep1"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";


            /* prepare roles */
            $dbres = db_do(self::getsql("getroles"));
            $roles = [];
            $rolesformread = "";
            $rolesformadd = "";
            foreach ($dbres as $item) {
                $roles[$item["id"]] = $item;
                $rolesformread .= bb_af_form_incheck(
                    "fn2[]", $item["id"], $item["letter"], "", "",
                    "bfafbtn bbaffot bbafroundbtn small bbacyel", "");
                $rolesformadd .= bb_af_form_incheck(
                    "fn3[]", $item["id"], $item["letter"], "", "",
                    "bfafbtn bbaffot bbafroundbtn small bbacyel", "");
            }
            $roleswith = (max(count($dbres) * 27, 45)) . "px";

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_intxt("fn0", "", "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    $rolesformread,
                    $rolesformadd,
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getactivitytypes"), "",
                ["name", "description", ["bbdbrolestoballs", ["readperm", $roles]], ["bbdbrolestoballs", ["addperm", $roles]], ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl1"),   "",
                $this->getstr("lbl2"),   "",
                $this->getstr("perm13"), "width: $roleswith;",
                $this->getstr("perm14"), "width: $roleswith;",
                "",                      "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_activitytypes($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $refer    = "Activity Type";
            $postpos  = "activitytype";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);
            $editinfo = $this->getstr("war1");


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* name */
            $fn["1"] = ispd("fn1"); /* description */
            $fn["2"] = is_array(ispd("fn2")) ? ispd("fn2") : [ispd("fn2")]; /* read permissions */
            $fn["3"] = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")]; /* add permissions */
            //$fn["4"] = ispd("fn4"); /* options */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            /* prepare roles */
            $dbres = db_do(self::getsql("getroles"));
            $roles = [];
            foreach ($dbres as $item) {
                $roles[$item["id"]] = $item;
            }

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep2");

                $dbres = db_do(self::getsql("getactivitytypebyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["description"];
                    $fn["2"] = explode(", ", $item["readperm"]);
                    $fn["3"] = explode(", ", $item["addperm"]);
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedactivitytype"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep5");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($editinfo);

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        (!$editwarning ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete" && !$editwarning) { /* and delete */
                    $title   = $this->getstr("sep6");
                    $desc    = bbwarn($this->getstr("war2", $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep4");
                $fclass  = "bbfedit";
                $subline =
                    bb_af_a($this->getstr("btncancel"), $homeurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("add$postpos", $this->getstr("btncreate"), FALSE, "", "bbacgreen");
            }

            /* formify */
            if ($id == FALSE || $action === "edit") { /* add or edit */
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";
                $fn["0"] = bb_af_form_intxt("fn0", $fn["0"], "", fia("fn0"));
                $fn["1"] = bb_af_form_intxt("fn1", $fn["1"], "", fia("fn1"));

                /* permissions */
                $balls = "";
                foreach ($roles as $role) {
                    $check = in_array($role["id"], $fn["2"]) ? "checked" : "";
                    $balls .= bb_af_form_incheck(
                            "fn2[]", $role["id"], $role["letter"],
                            "", "", "bfafbtn bbafroundbtn bbacyel", "", $check);
                }
                $fn["2"] = $balls;

                $balls = "";
                foreach ($roles as $role) {
                    $check = in_array($role["id"], $fn["3"]) ? "checked" : "";
                    $balls .= bb_af_form_incheck(
                            "fn3[]", $role["id"], $role["letter"],
                            "", "", "bfafbtn bbafroundbtn bbacyel", "", $check);
                }
                $fn["3"] = $balls;
            } else {
                /* permissions */
                $balls = "";
                foreach ($fn["2"] as $rid) {
                    if (!isset($roles[$rid])) continue;
                    $balls .= bb_af_txt($roles[$rid]["letter"], "", "bbafin bbafroundbtn");
                }
                $fn["2"] = $balls;

                $balls = "";
                foreach ($fn["3"] as $rid) {
                    if (!isset($roles[$rid])) continue;
                    $balls .= bb_af_txt($roles[$rid]["letter"], "", "bbafin bbafroundbtn");
                }
                $fn["3"] = $balls;
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_sep($this->getstr("lbl1"),   $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"),   $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("sep3"),   "", "", "bbafpsep") .
                bb_af_sep($this->getstr("perm11"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("perm12"), $fn["3"], "", $classes) .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }





















        /* * * * * * * * * * * * * * * CHECKPOST * * * * * * * * * * * * * * */
        private function checkpost($home) {
            $res = "";

            if ((issetp("addactivity") || issetp("editactivity") || issetp("deleteactivity")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn1"],
                        "bberror", [$this->getstr("err3")], "formalert", ["fn1"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn1"), self::getsql("getactivitybyname"), ["name" => ispd("fn1")]],
                        "bberror", [$this->getstr("err4")], "formalert", ["fn1"]],
                    ["valdb_e", [self::getsql("getactivitytypebyid"), ["id" => ispd("fn0")]],
                        "bberror", [$this->getstr("errnocando")], "formalert", ["fn0"]]
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getactivitybyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedactivity"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("deleteactivity")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("delactivity"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log6", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf6", ispd("fnb")));
                }
                /* prepare input: (fn4) the options */
                $opts = is_array(ispd("fn4")) ? ispd("fn4") : [ispd("fn4")];
                $opts = implode(", ", $opts);

                /* prepare input: (fn3) question list */
                $_POST["fn3"] = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")];
                foreach (ispd("fn3") as $key => $val) {
                    if ($val === "")
                        unset($_POST["fn3"][$key]);
                }

                $validate[] = ["valpa", ["fn3", "1"],
                    "bberror", [$this->getstr("err5")], "formalert", ["fn3"]];

                if (issetp("addactivity")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addactivity"), [
                        "activitytypeid" => ispd("fn0"),
                        "name"           => ispd("fn1"),
                        "description"    => ispd("fn2"),
                        "options"        => $opts,
                    ], $this->getstr("log4", ispd("fn1")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $id = $dbres[0]["id"];
                } else
                if (issetp("editactivity")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editactivity"), [
                        "id"             => ispd("fna"),
                        "activitytypeid" => ispd("fn0"),
                        "name"           => ispd("fn1"),
                        "description"    => ispd("fn2"),
                        "options"        => $opts,
                    ], $this->getstr("log5", ispd("fn1")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $id = ispd("fna");
                }

                /* build query */
                $aqsquery = "";
                $aqsargs  = [];
                $ct = 0;
                foreach (ispd("fn3") as $ii) {
                    $aqsquery .= "(:ai$ct, :qi$ct, :p$ct), ";
                    $aqsargs["ai$ct"] = $id;
                    $aqsargs["qi$ct"] = $ii;
                    $aqsargs["p$ct"]  = $ct;
                    $ct++;
                }
                $aqsquery = substr($aqsquery, 0, -2);

                /* update activityquestions */
                $dbdel = db_do(self::getsql("delactivityquestions"), ["id" => $id],
                    $this->getstr("log7", ispd("fn1")));
                $dbadd = db_do(self::getsql("addactivityquestions", $aqsquery), $aqsargs,
                    $this->getstr("log8", ispd("fn1")));
                if (!valdbres($dbdel, 0) || !valdbres($dbadd))
                    return bberror($this->getstr("errsql", __LINE__));

                if (issetp("addactivity"))
                    return bbinfo($this->getstr("inf4", ispd("fn1")));
                else if (issetp("editactivity"))
                    return bbinfo($this->getstr("inf5", ispd("fn1")));
            }
            if ((issetp("addactivitytype") || issetp("editactivitytype") || issetp("deleteactivitytype")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getactivitytypebyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getactivitytypebyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedactivitytype"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("addactivitytype")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addactivitytype"), [
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log1", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $oid = $dbres[0]["id"];

                    /* read permissions */
                    foreach ((is_array(ispd("fn2")) ? ispd("fn2") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addactivitytypepermissionread"),
                            ["objid" => $oid,
                             "roleid" => $rid],
                            $this->getstr("log10", ispd("fn0"), $rid));
                    }

                    /* add permissions */
                    foreach ((is_array(ispd("fn3")) ? ispd("fn3") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addactivitytypepermissionadd"),
                            ["objid" => $oid,
                             "roleid" => $rid],
                            $this->getstr("log11", ispd("fn0"), $rid));
                    }

                    return bbinfo($this->getstr("inf1", ispd("fn0")));
                }
                if (issetp("editactivitytype")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editactivitytype"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log2", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }

                    /* update permissions */
                    $dbres = db_do(self::getsql("delactivitytypepermissions"), ["id" => ispd("fna")],
                        $this->getstr("log9", ispd("fn0")));
                    if (!valdbres($dbres, 0)) return bberror("Unknown error");

                    /* read permissions */
                    foreach ((is_array(ispd("fn2")) ? ispd("fn2") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addactivitytypepermissionread"),
                            ["objid" => ispd("fna"),
                             "roleid" => $rid],
                            $this->getstr("log10", ispd("fn0"), $rid));
                    }

                    /* add permissions */
                    foreach ((is_array(ispd("fn3")) ? ispd("fn3") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addactivitytypepermissionadd"),
                            ["objid" => ispd("fna"),
                             "roleid" => $rid],
                            $this->getstr("log11", ispd("fn0"), $rid));
                    }

                    return bbinfo($this->getstr("inf2", ispd("fn0")));
                }
                if (issetp("deleteactivitytype")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("delactivitytype"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log3", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf3", ispd("fnb")));
                }
            }
            return $res;
        }























        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what, $pager = "") {
            switch ($what) {
                /* * * * * * * * * * * * * * * * * * * * * ACTIVITYTYPES * * */
                case "getactivitytypes": return <<<EOFF
                SELECT *, readperm, addperm
                    FROM activitytype
                        JOIN activitytyperolepermissions
                          ON activitytyperolepermissions.id = activitytype.id
                ORDER BY name $pager;
EOFF;
                case "getactivitytypebyid": return <<<EOFF
                SELECT activitytype.*, readperm, addperm
                    FROM activitytype
                        JOIN activitytyperolepermissions
                          ON activitytyperolepermissions.id = activitytype.id
                    WHERE activitytype.id = :id;
EOFF;
                case "getactivitytypebyname": return <<<EOFF
                SELECT * FROM activitytype WHERE name = :name;
EOFF;
                case "isusedactivitytype": return <<<EOFF
                SELECT * FROM activity WHERE activitytypeid = :id;
EOFF;
                case "addactivitytype": return <<<EOFF
                INSERT INTO activitytype (name, description) VALUES (:name, :description)
                    RETURNING id;
EOFF;
                case "editactivitytype": return <<<EOFF
                UPDATE activitytype SET
                        name        = :name,
                        description = :description
                    WHERE id = :id;
EOFF;
                case "delactivitytype": return <<<EOFF
                DELETE FROM activitytype WHERE id = :id;
EOFF;
                case "getroles": return <<<EOFF
                SELECT * FROM role WHERE id > 0 ORDER BY letter;
EOFF;
                case "addactivitytypepermissionread": return <<<EOFF
                INSERT INTO permissions (roleid, objid, module, mode)
                    VALUES (:roleid, :objid, 'oi_activitytype', 'read');
EOFF;
                case "addactivitytypepermissionadd": return <<<EOFF
                INSERT INTO permissions (roleid, objid, module, mode)
                    VALUES (:roleid, :objid, 'oi_activitytype', 'add');
EOFF;
                case "delactivitytypepermissions": return <<<EOFF
                DELETE FROM permissions WHERE module = 'oi_activitytype'
                    AND objid = :id;
EOFF;
                /* * * * * * * * * * * * * * * * * * * * * ACTIVITIES * * * * */
                case "getactivities": return <<<EOFF
                SELECT * FROM activityinfo
                    $pager;
EOFF;
                case "getactivitybyid": return <<<EOFF
                SELECT * FROM activityinfo WHERE id = :id;
EOFF;
                case "getactivitybyname": return <<<EOFF
                SELECT * FROM activityinfo WHERE name = :name;
EOFF;
                case "isusedactivity": return <<<EOFF
                SELECT * FROM action WHERE activityid = :id;
EOFF;
                case "addactivity": return <<<EOFF
                INSERT INTO activity (name, description, options, activitytypeid)
                    VALUES (:name, :description, :options, :activitytypeid)
                RETURNING id;
EOFF;
                case "editactivity": return <<<EOFF
                UPDATE activity SET
                        name           = :name,
                        description    = :description,
                        options        = :options,
                        activitytypeid = :activitytypeid
                    WHERE id = :id;
EOFF;
                case "delactivity": return <<<EOFF
                DELETE FROM activity WHERE id = :id;
EOFF;
                case "getactivityquestionsbyid": return <<<EOFF
                SELECT * FROM activityquestions
                    WHERE aid = :id
                    ORDER BY qprecedence;
EOFF;
                case "getquestions": return <<<EOFF
                SELECT * FROM questionsinfo;
EOFF;
                case "addactivityquestions": return <<<EOFF
                INSERT INTO activityquestion (activityid, questionid, precedence) VALUES $pager;
EOFF;
                case "delactivityquestions": return <<<EOFF
                DELETE FROM activityquestion WHERE activityid = :id;
EOFF;
            }
            return "";
        }

        /****************************************************
                           STRINGS
        /****************************************************/
    }
    return new uiactivities();
?>
