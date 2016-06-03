<?php
    class uievents extends uimodule {
        protected $moduleid    = 'events';
        protected $modulename  = 'Events Management';
        protected $menutxt     = 'Events';
        protected $url         = 'events';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",          $this->getstr("perm1")],
                ["manage",      $this->getstr("perm2")],
            ];
        }

        public function getpage($home) {
            if (!$this->cando("dv")) { /* no permissions to be here */
                return error404();
            }
            $ctt = "";
            /* An up menu */
            if (!valg("up")) $_GET["up"] = "events";
            $ctt = upmenu($home, isgd("up"), [
                $this->getstr("menu1"), "events",
                $this->getstr("menu2"), "eventtypes",
                ""]);
            $home["up"] = $_GET["up"];

            /* check for POST stuff */
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();

            if (valg("up", "events") && $this->cando("dv")) {
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_events($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_events($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_events($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_events($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_events($home);
                }
            } else
            if (valg("up", "eventtypes") && $this->cando("dv")) {
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_eventtypes($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_eventtypes($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_eventtypes($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_eventtypes($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_eventtypes($home);
                }
            }
            else {
                return error404();
            }
            return $ctt;
        }













        /* * * * * * * * * * * * * * * EVENTS * * * * * * * * * * * * * * */
        private function dv_events($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep7"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";

            /* get the event types */
            $dbres = db_do(self::getsql("geteventtypes"));
            if (!valdbres($dbres, 0))
                return bberror($this->getstr("errsql"));
            $soptions = "";
            foreach ($dbres as $item) {
                $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"]);
            }

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_inselbox("fn0", $soptions, "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    bb_af_form_intxt("fn2", "", "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getevents"), "", ["etname", "name", "description", ""],
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

        private function viewaddeditdel_events($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $postpos  = "event";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);
            $editinfo = $this->getstr("war3");


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* eventtypeid */
            $fn["1"] = ispd("fn1"); /* name */
            $fn["2"] = ispd("fn2"); /* description */
            //$fn["4"] = ispd("fn4"); /* options */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep8");

                $dbres = db_do(self::getsql("geteventbyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["eventtypeid"];
                    $fn["1"] = $item["name"];
                    $fn["2"] = $item["description"];
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                $fn["z"] = $item["etname"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedevent"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep10");
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
                    $title   = $this->getstr("sep11");
                    $desc    = bbwarn($this->getstr("war4", $fn["1"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep9");
                $fclass  = "bbfedit";
                $subline =
                    bb_af_a($this->getstr("btncancel"), $homeurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("add$postpos", $this->getstr("btncreate"), FALSE, "", "bbacgreen");
            }

            /* formify */
            if ($id == FALSE || $action === "edit") { /* add or edit */
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";

                $dbres = db_do(self::getsql("geteventtypes"));
                if (!valdbres($dbres, 0))
                    return bberror($this->getstr("errsql", 1));
                $soptions = "";
                foreach ($dbres as $item) {
                    $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"],
                    ($item["id"] == $fn["0"] ? "selected" : ""));
                }
                $fn["0"] = bb_af_form_inselbox("fn0", $soptions);
                $fn["1"] = bb_af_form_intxt("fn1", $fn["1"], "", fia("fn1"));
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", fia("fn2"));
            } else {
                /* view mode */
                $fn["0"] = $fn["z"];
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_sep($this->getstr("lbl3"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl4"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl5"), $fn["2"], "", $classes) .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }



















        /* * * * * * * * * * * * * * * EVENTTYPES * * * * * * * * * * * * * * */
        private function dv_eventtypes($home) {
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
            $trs .= bb_atabler_fromquery(self::getsql("geteventtypes"), "",
                ["name", "description", ["bbdbrolestoballs", ["readperm", $roles]], ["bbdbrolestoballs", ["addperm", $roles]], ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl1"),  "",
                $this->getstr("lbl2"),  "",
                $this->getstr("perm13"), "width: $roleswith;",
                $this->getstr("perm14"), "width: $roleswith;",
                "", "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_eventtypes($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $postpos  = "eventtype";
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

                $dbres = db_do(self::getsql("geteventtypebyid"), ["id" => $id]);
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

                $editwarning = valdbres(db_do(self::getsql("isusedeventtype"), ["id" => $fn["a"]]));
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
                    bb_af_form_submit("add$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbacgreen");
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
                bb_af_sep($this->getstr("lbl1"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("sep3"), "", "", "bbafpsep") .
                bb_af_sep($this->getstr("perm11"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("perm12"), $fn["3"], "", $classes) .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }

















        /* * * * * * * * * * * * * * * CHECKPOST * * * * * * * * * * * * * * */
        private function checkpost($home) {
            $res = "";
            
            if ((issetp("addevent") || issetp("editevent") || issetp("deleteevent")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err3")], "formalert", ["fn1"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn1"), self::getsql("geteventbyname"), ["name" => ispd("fn1")]],
                        "bberror", [$this->getstr("err4")], "formalert", ["fn1"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("geteventbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedevent"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("addevent")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addevent"), [
                        "eventtypeid" => ispd("fn0"),
                        "name"        => ispd("fn1"),
                        "description" => ispd("fn2"),
                    ], $this->getstr("log4", ispd("fn1")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf4", ispd("fn1")));
                }
                if (issetp("editevent")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editevent"), [
                        "id"          => ispd("fna"),
                        "eventtypeid" => ispd("fn0"),
                        "name"        => ispd("fn1"),
                        "description" => ispd("fn2"),
                    ], $this->getstr("log5", ispd("fn1")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf5", ispd("fn1")));
                }
                if (issetp("deleteevent")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("delevent"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log6", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf5", ispd("fnb")));
                }
            } else
            if ((issetp("addeventtype") || issetp("editeventtype") || issetp("deleteeventtype")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("geteventtypebyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("geteventtypebyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedeventtype"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("addeventtype")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addeventtype"), [
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log1", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf1", ispd("fn0")));
                }
                if (issetp("editeventtype")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editeventtype"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log2", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }

                    /* update permissions */
                    $dbres = db_do(self::getsql("deleventtypepermissions"), ["id" => ispd("fna")],
                    $this->getstr("log7", ispd("fn0")));
                    if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", __LINE__));

                    /* read permissions */
                    foreach ((is_array(ispd("fn2")) ? ispd("fn2") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addeventtypepermissionread"),
                            ["objid" => ispd("fna"),
                             "roleid" => $rid],
                            $this->getstr("log8", ispd("fn0"), $rid));
                    }

                    /* add permissions */
                    foreach ((is_array(ispd("fn3")) ? ispd("fn3") : []) as $rid) {
                        if ($rid === "") continue;
                        $dbres = db_do(self::getsql("addeventtypepermissionadd"),
                            ["objid" => ispd("fna"),
                             "roleid" => $rid],
                            $this->getstr("log9", ispd("fn0"), $rid));
                    }

                    return bbinfo($this->getstr("inf2", ispd("fn0")));
                }
                if (issetp("deleteeventtype")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("deleventtype"), [
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
                /* * * * * * * * * * * * * * * * * * * * * EVENTTYPES * * * * */
                case "getevents": return <<<EOFF
                SELECT * FROM eventinfo ORDER BY name;
EOFF;
                case "geteventbyid": return <<<EOFF
                SELECT * FROM eventinfo WHERE id = :id;
EOFF;
                case "isusedevent": return <<<EOFF
                SELECT * FROM appointment WHERE eventid = :id;
EOFF;
                case "geteventbyname": return <<<EOFF
                SELECT * FROM eventinfo WHERE name = :name;
EOFF;
                case "addevent": return <<<EOFF
                INSERT INTO event (eventtypeid, name, description) VALUES (:eventtypeid, :name, :description);
EOFF;
                case "editevent": return <<<EOFF
                UPDATE event SET
                        eventtypeid = :eventtypeid,
                        name        = :name,
                        description = :description
                    WHERE id = :id;
EOFF;
                case "delevent": return <<<EOFF
                DELETE FROM event WHERE id = :id;
EOFF;
                /* * * * * * * * * * * * * * * * * * * * * EVENTTYPES * * * * */
                case "geteventtypes": return <<<EOFF
                SELECT eventtype.*, readperm, addperm
                    FROM eventtype
                        JOIN eventtyperolepermissions
                          ON eventtyperolepermissions.id = eventtype.id
                WHERE eventtype.id > 0 ORDER BY name $pager;
EOFF;
                case "geteventtypebyid": return <<<EOFF
                SELECT eventtype.*, readperm, addperm
                    FROM eventtype
                        JOIN eventtyperolepermissions
                          ON eventtyperolepermissions.id = eventtype.id
                    WHERE eventtype.id = :id;
EOFF;
                case "geteventtypebyname": return <<<EOFF
                SELECT * FROM eventtype WHERE name = :name;
EOFF;
                case "isusedeventtype": return <<<EOFF
                SELECT * FROM event WHERE eventtypeid = :id;
EOFF;
                case "addeventtype": return <<<EOFF
                INSERT INTO eventtype (name, description) VALUES (:name, :description);
EOFF;
                case "editeventtype": return <<<EOFF
                UPDATE eventtype SET
                        name        = :name,
                        description = :description
                    WHERE id = :id;
EOFF;
                case "deleventtype": return <<<EOFF
                DELETE FROM eventtype WHERE id = :id;
EOFF;
                case "getroles": return <<<EOFF
                SELECT * FROM role WHERE id > 0 ORDER BY letter;
EOFF;
                case "addeventtypepermissionread": return <<<EOFF
                INSERT INTO permissions (roleid, objid, module, mode)
                    VALUES (:roleid, :objid, 'oi_eventtype', 'read');
EOFF;
                case "addeventtypepermissionadd": return <<<EOFF
                INSERT INTO permissions (roleid, objid, module, mode)
                    VALUES (:roleid, :objid, 'oi_eventtype', 'add');
EOFF;
                case "deleventtypepermissions": return <<<EOFF
                DELETE FROM permissions WHERE module = 'oi_eventtype'
                    AND objid = :id;
EOFF;
            }
            return "";
        }
    }
    return new uievents();
?>

