<?php
    class uiorganizations extends uimodule {
        protected $moduleid    = 'organizations';
        protected $modulename  = 'Organizations';
        protected $menutxt     = 'Organizations';
        protected $url         = 'organizations';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",              $this->getstr("perm1")],
                ["add",             $this->getstr("perm2")],
                ["view",            $this->getstr("perm3")],
                ["edit",            $this->getstr("perm4")],
                ["delete",          $this->getstr("perm5")],
                ["viewdv",          $this->getstr("perm7")],
                ["viewuadd",        $this->getstr("perm6")],
                ["viewu",           $this->getstr("perm8")],
                ["viewuedit",       $this->getstr("perm9")],
                ["viewudel",        $this->getstr("perm10")],
            ];
        }

        public function getpage($home) {
            $ctt = "";
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();

            /* selector */
            if (issetg("view") && $this->cando("view")) {
                $ctt .= $this->viewaddeditdel($home, isgd("view"));
            } else
            if (issetg("add") && $this->cando("add")) {
                $ctt .= $this->viewaddeditdel($home);
            } else
            if (issetg("edit") && $this->cando("edit")) {
                $ctt .= $this->viewaddeditdel($home, isgd("edit"), "edit");
            } else
            if (issetg("delete") && $this->cando("delete")) {
                $ctt .= $this->viewaddeditdel($home, isgd("delete"), "delete");
            } else
            if ($this->cando("dv")) {
                $ctt .= $this->defaultview($home);
            } else {
                $ctt = error404();
            }
            return $ctt;
        }

        private function defaultview($home) {
            $res = "";
            $res .= bb_af_sep($this->getstr("sep1"), "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $addurl    = gettourl($home, "add");
            $viewurl   = gettourl($home, "view") . "=";
            $trs = "";

            if ($this->cando("add")) {
                $trs .= bb_af_form_tabler(bb_atablecs(
                    bb_af_form_intxt("fn0", "", "", "bbaffot"),
                    bb_af_form_intxt("fn3", "", "", "bbaffot"),
                    bb_af_form_intxt("fn4", "", "", "bbaffot"),
                    bb_af_form_intxt("fn5", "", "", "bbaffot"),
                    bb_af_form_intxt("fn7", "", "", "bbaffot"),
                    ($this->cando("add") ? bb_af_form_submit("addorganization", bb_gets("+"), FALSE, "", "bbaffot bbafroundbtn bbacgreen") : "")
                ), $addurl);
            }
            $trs .= bb_atabler_fromquery(self::getsql("getorgs"), "", ["name", "location", "email", "phone", "url", ""],
                ($this->cando("view") ? $viewurl : ""));
            /* the table */
            $res .= bb_atable_hfi(array(
                $this->getstr("lbl1"), "",
                $this->getstr("lbl4"), "",
                $this->getstr("lbl5"), "",
                $this->getstr("lbl6"), "",
                $this->getstr("lbl8"), "",
                "",                    "width: 40px;"
            ), $trs);

            return $res;
        }

        private function viewaddeditdel($home, $id = FALSE, $action = FALSE) {
            $ohome = arraddrem($home, ["view" => isgd("view")]);
            if (issetg("viewuserorg") && $this->cando("viewu")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("viewuserorg"));
            } else
            if (issetg("adduserorg") && $this->cando("viewuadd")) {
                return $this->viewaddeditdel_userorg($ohome);
            } else
            if (issetg("edituserorg") && $this->cando("viewuedit")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("edituserorg"), "edit");
            } else 
            if (issetg("deleteuserorg") && $this->cando("viewudel")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("deleteuserorg"), "delete");
            }

            $res = "";
            $postpos = "org";
            $getpos = "";
            $homeurl = gettourl($home);
            $thisurl = gettourl($home, ["view$getpos" => $id]);

            $fn["a"] = ispd("fna"); /* oid */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0");
            $fn["1"] = ispd("fn1");
            $fn["2"] = ispd("fn2");
            $fn["3"] = ispd("fn3");
            $fn["4"] = ispd("fn4");
            $fn["5"] = ispd("fn5");
            $fn["6"] = ispd("fn6");
            $fn["7"] = ispd("fn7");
            $fn["8"] = ispd("fn8");
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep2");

                $dbres = db_do(self::getsql("getorgbyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];
                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["address"];
                    $fn["2"] = $item["postcode"];
                    $fn["3"] = $item["location"];
                    $fn["4"] = $item["email"];
                    $fn["5"] = $item["phone"];
                    $fn["6"] = $item["fax"];
                    $fn["7"] = $item["url"];
                    $fn["8"] = $item["info"];
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline = $this->cando("edit") ?
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn") : "";

                $editwarning = valdbres(db_do(self::getsql("getuserorgsbyoid"), ["oid" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep5");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war1"));

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        ($this->cando("delete") ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete") { /* and delete */
                    $title   = $this->getstr("sep6");
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war4", $fn["0"]));
                    else
                        $desc = bbwarn($this->getstr("war2", $fn["0"]));
                    if ($this->cando("delete"))
                        $subline =
                            bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                            bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep4");
                $fclass  = "bbfedit";
                $subline = $this->cando("add") ? (
                    bb_af_a($this->getstr("btncancel"), $homeurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("add$postpos", $this->getstr("btncreate"), FALSE, "", "bbacgreen")) : "";
            }

            /* formify */
            if ($id == FALSE || $action === "edit") {
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";
                $fn["0"] = bb_af_form_intxt("fn0", $fn["0"], "", fia("fn0"));
                $fn["1"] = bb_af_form_intxt("fn1", $fn["1"], "", fia("fn1"));
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", fia("fn2"));
                $fn["3"] = bb_af_form_intxt("fn3", $fn["3"], "", fia("fn3"));
                $fn["4"] = bb_af_form_intxt("fn4", $fn["4"], "", fia("fn4"));
                $fn["5"] = bb_af_form_intxt("fn5", $fn["5"], "", fia("fn5"));
                $fn["6"] = bb_af_form_intxt("fn6", $fn["6"], "", fia("fn6"));
                $fn["7"] = bb_af_form_intxt("fn7", $fn["7"], "", fia("fn7"));
                $fn["8"] = bb_af_form_intxt("fn8", $fn["8"], "", fia("fn8"));
            } else {

            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_sep($this->getstr("lbl1"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl3"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("lbl4"), $fn["3"], "", $classes) .
                bb_af_sep($this->getstr("lbl5"), $fn["4"], "", $classes) .
                bb_af_sep($this->getstr("lbl6"), $fn["5"], "", $classes) .
                bb_af_sep($this->getstr("lbl7"), $fn["6"], "", $classes) .
                bb_af_sep($this->getstr("lbl8"), $fn["7"], "", $classes) .
                bb_af_sep($this->getstr("lbl9"), $fn["8"], "", $classes) .
                ($subline !== "" ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);

            if ($id !== FALSE && $action == FALSE) {
                if ($this->cando("viewdv"))
                    $res .= $this->view_userorg($ohome, $id);
            }
            return $res;
        }

        private function view_userorg($home, $id) {
            $res = bb_af_sep($this->getstr("sep3"), "", "", "bbafpsep bbafpsepbig");
            $viewurl = gettourl($home, "viewuserorg") . "=";
            $addurl  = gettourl($home, "adduserorg");
            $dbres = db_do(uiusermanagement::getsql("listusers"));
            $soptions = "";
            foreach ($dbres as $item) {
                $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"]);
            }
            /* form */
            $trs = "";
            if ($this->cando("viewuadd")) {
                $trs .= bb_af_form_tabler(bb_atablecs(
                    bb_af_form_hidden("fnb", $id) .
                    bb_af_form_inselbox("fn1", $soptions, "", "bbaffot"),
                    bb_af_form_intxt("fn2", "", "", "bbaffot"),
                    bb_af_form_intxt("fn3", "", "", "bbaffot"),
                    bb_af_form_intxt("fn4", "", "", "bbaffot"),
                    bb_af_form_intxt("fn5", "", "", "bbaffot"),
                    bb_af_form_intxt("fn6", "", "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), FALSE, "", "bbaffot bbafroundbtn bbacgreen")
                ), $addurl);
            }
            $trs .= bb_atabler_fromquery(self::getsql("getuserorgsbyoid"), ["oid" => $id], ["uname", "function", "email", "phone", "mobile", "info", ""],
            ($this->cando("viewu") ? $viewurl : ""));
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbluser1"),  "",
                $this->getstr("lbl11"), "",
                $this->getstr("lbl12"), "",
                $this->getstr("lbl13"), "",
                $this->getstr("lbl14"), "",
                $this->getstr("lbl15"), "",
                "",                     "width:40px;",
            ], $trs);
            return $res;
        }

        private function viewaddeditdel_userorg($home, $id = FALSE, $action = FALSE) {
            $res = "";
            $postpos = "userorg";
            $getpos = "userorg";
            $homeurl = gettourl($home);
            $thisurl = gettourl($home, ["view$getpos" => $id]);


            $fn["a"] = isgd("view"); /* oid */
            $fn["b"] = ispd("fnb"); /* uid */
            $fn["c"] = isgd("fnc"); /* uoid */
            $fn["0"] = ispd("fn0");
            $fn["1"] = ispd("fn1");
            $fn["2"] = ispd("fn2");
            $fn["3"] = ispd("fn3");
            $fn["4"] = ispd("fn4");
            $fn["5"] = ispd("fn5");
            $fn["6"] = ispd("fn6");
            $fn["z"] = $fn["y"] = "";
            $subline = "";
            $desc    = "";
            $classesorg =
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            $dbres = db_do(self::getsql("getorgbyid"), ["id" => $fn["a"]]);
            if (!valdbres($dbres, 1, 1))
                return bberror($this->getstr("errnoexist", ""));
            $item = $dbres[0];
            $fn["a"] = $item["id"];
            $fn["0"] = $fn["z"] = $item["name"];

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep8");

                $dbres = db_do(self::getsql("getuserorg"), ["uid" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist", ""));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["1"] = $item["uname"];
                    $fn["2"] = $item["function"];
                    $fn["3"] = $item["email"];
                    $fn["4"] = $item["phone"];
                    $fn["5"] = $item["mobile"];
                    $fn["6"] = $item["info"];
                }
                $fn["1"] = $fn["y"] = $item["uname"];
                $fn["a"] = $item["organizationid"];
                $fn["c"] = $id;
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline = $this->cando("viewuedit") ?
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn") : "";


                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep9");
                    $faction = $thisurl;

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        ($this->cando("viewudel") ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bbafin bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete") { /* and delete */
                    $title   = $this->getstr("sep10");
                    $desc    = bbwarn($this->getstr("war3", $fn["z"], $fn["y"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep7");
                $fclass  = "bbfedit";
                $subline = $this->cando("viewuadd") ? (
                    bb_af_a($this->getstr("btncancel"), $homeurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                    bb_af_form_submit("add$postpos", $this->getstr("btnassociate"), FALSE, "", "bbacgreen")) : "";
            }

            /* formify */
            if ($id == FALSE || $action === "edit") {
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", fia("fn2"));
                $fn["3"] = bb_af_form_intxt("fn3", $fn["3"], "", fia("fn3"));
                $fn["4"] = bb_af_form_intxt("fn4", $fn["4"], "", fia("fn4"));
                $fn["5"] = bb_af_form_intxt("fn5", $fn["5"], "", fia("fn5"));
                $fn["6"] = bb_af_form_intxt("fn6", $fn["6"], "", fia("fn6"));

                /* get a select with all organizations */
                if ($id == FALSE) {
                    $classesorg = "bbafsitem afhover";
                    $dbres = db_do(uiusermanagement::getsql("listusers"));
                    $soptions = "";
                    foreach ($dbres as $item) {
                        $seled = $fn["1"] == $item["id"] ? "selected" : "";
                        $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"], $seled);
                    }
                    $fn["1"] = bb_af_form_inselbox("fn1", $soptions);
                }
            } else {

            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna",  $fn["a"]) .
                bb_af_form_hidden("fnb",  $fn["b"]) .
                bb_af_form_hidden("fnc",  $fn["c"]) .
                bb_af_form_hidden("fnz",  $fn["z"]) .
                bb_af_form_hidden("fny",  $fn["y"]) .
                bb_af_sep($this->getstr("lbl1"),     $fn["0"], "", "bbafstxt") .
                bb_af_sep($this->getstr("lbluser1"), $fn["1"], "", $classesorg) .
                bb_af_sep($this->getstr("lbl11"),    $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("lbl12"),    $fn["3"], "", $classes) .
                bb_af_sep($this->getstr("lbl13"),    $fn["4"], "", $classes) .
                bb_af_sep($this->getstr("lbl14"),    $fn["5"], "", $classes) .
                bb_af_sep($this->getstr("lbl15"),    $fn["6"], "", $classes) .
                ($subline !== "" ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }




















        function checkpost($home) {
            $res = "";
            if (issetp("addorg") || issetp("editorg") || issetp("deleteorg")) {
                $validate = [
                    /* new name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getorgbyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getorgbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando", 1)]]
                ];
                if (issetp("addorg") && $this->cando("add")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addorg"), [
                        "name"     => ispd("fn0"),
                        "address"  => ispd("fn1"),
                        "postcode" => ispd("fn2"),
                        "location" => ispd("fn3"),
                        "email"    => ispd("fn4"),
                        "phone"    => ispd("fn5"),
                        "fax"      => ispd("fn6"),
                        "url"      => ispd("fn7"),
                        "info"     => ispd("fn8"),
                    ], $this->getstr("log1", ispd("fn0")));
                    if (valdbres($dbres)) {
                        $res = bbinfo($this->getstr("inf1", ispd("fn0")));
                    } else {
                        $res = bberror($this->getstr("errsql", 1));
                    }
                } else
                if (issetp("editorg") && $this->cando("edit")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")], "view");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editorg"), [
                        "id"       => ispd("fna"),
                        "name"     => ispd("fn0"),
                        "address"  => ispd("fn1"),
                        "postcode" => ispd("fn2"),
                        "location" => ispd("fn3"),
                        "email"    => ispd("fn4"),
                        "phone"    => ispd("fn5"),
                        "fax"      => ispd("fn6"),
                        "url"      => ispd("fn7"),
                        "info"     => ispd("fn8"),
                    ], $this->getstr("log2",ispd("fn0")));
                    if (valdbres($dbres)) {
                        $res = bbinfo($this->getstr("inf2", ispd("fn0")));
                    } else {
                        $res = bberror($this->getstr("errsql", 2));
                    }
                } else
                if (issetp("deleteorg") && $this->cando("delete")) {
                    if ((($validate = validate_form($valexists)) !== "")) {
                        return $validate;
                    }
                    /* any pre-check before deleting */
                    if (valdbres(db_do(self::getsql("deleteorg"), ["id" => ispd("fna")],
                        $this->getstr("log3", ispd("fnb"))))) {
                        $res .= bbinfo($this->getstr("inf3", ispd("fnb")));
                    } else {
                        $res .= bberror($this->getstr("errsql", 6));
                    }
                }
            } else
            if (issetp("edituserorg") || issetp("deleteuserorg") || issetp("adduserorg")) {
                $valuando = [
                    /* if user exists */
                    ["valdb_e", [uiusermanagement::getsql("getuserbyid"), ["id" => ispd("fn1")]],
                        "bberror", [$this->getstr("errsql", 2)]],
                    /* if organization exists */
                    ["valdb_e", [self::getsql("getorgbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errsql", 3)]],
                ];

                $valuserorg = [
                    /* if userorg exists */
                    ["valdb_e", [self::getsql("getuserorg"), ["uid" => ispd("fnc")]],
                        "bberror", [$this->getstr("errsql", 4)]],

                ];
                if (issetp("edituserorg") && $this->cando("viewuedit")) {
                    if (($validate = validate_form($valuserorg)) !== "") {
                        $_GET = arraddrem($home, ["edituserorg" => ispd("fnc")], "viewuserorg");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("edituserorg"), [
                        "uoid"     => ispd("fnc"),
                        "function" => ispd("fn2"),
                        "email"    => ispd("fn3"),
                        "phone"    => ispd("fn4"),
                        "mobile"   => ispd("fn5"),
                        "info"     => ispd("fn6"),
                    ], $this->getstr("log5", ispd("fnz"), ispd("fny")));
                    if (!valdbres($dbres)) {
                        $res .= bberror(getstr("errsql", 3));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf5", ispd("fnz"), ispd("fny")));
                } else
                if (issetp("deleteuserorg") && $this->cando("viewudel")) {
                    if (($validate = validate_form($valuserorg)) !== "") {
                        $_GET = arraddrem($home, ["deleteuserorg" => ispd("fnc")], "viewuserorg");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("deleteuserorg"), ["uoid"   => ispd("fnc")],
                        $this->getstr("log6", ispd("fnz"), ispd("fny")));
                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 4));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf6", ispd("fnz"), ispd("fny")));
                } else
                if (issetp("adduserorg") && $this->cando("viewuadd")) {
                    if (($validate = validate_form($valuando)) !== "") {
                        $_GET = arraddrem($home, "adduserorg", "viewuserorg");
                        return $validate;
                    }
                    $dbuser = db_do(uiusermanagement::getsql("getuserbyid"), ["id" => ispd("fn1")]);
                    $dbres = db_do(self::getsql("adduserorg"), [
                        "organizationid" => ispd("fna"),
                        "userid"         => ispd("fn1"),
                        "function"       => ispd("fn2"),
                        "email"          => ispd("fn3"),
                        "phone"          => ispd("fn4"),
                        "mobile"         => ispd("fn5"),
                        "info"           => ispd("fn6"),
                    ], $this->getstr("log4", ispd("fnz"), $dbuser[0]["name"]));
                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 5));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf4", ispd("fnz"), $dbuser[0]["name"]));
                }
            }
            return $res;
        }
        public static function getsql($what) {
            switch ($what) {
                case "getorgbyid": return <<<EOFF
                SELECT * FROM organization WHERE id = :id;
EOFF;
                case "getorgbyname": return <<<EOFF
                SELECT * FROM organization WHERE name = :name;
EOFF;
                case "getorgs": return <<<EOFF
                SELECT * FROM organization
                    ORDER BY name;
EOFF;
                case "addorg": return <<<EOFF
                INSERT INTO organization (name, address, postcode, location, email, phone, fax, url, info)
                    VALUES (:name, :address, :postcode, :location, :email, :phone, :fax, :url, :info);
EOFF;
                case "editorg": return <<<EOFF
                UPDATE organization SET
                        name     = :name,
                        address  = :address,
                        postcode = :postcode,
                        location = :location,
                        email    = :email,
                        phone    = :phone,
                        fax      = :fax,
                        url      = :url,
                        info     = :info
                    WHERE id = :id;
EOFF;
                case "deleteorg": return <<<EOFF
                DELETE FROM organization WHERE id = :id;
EOFF;
                case "adduserorg": return <<<EOFF
                INSERT INTO userorganization (userid, organizationid, function, email, phone, mobile, info)
                    VALUES (:userid, :organizationid, :function, :email, :phone, :mobile, :info);
EOFF;
                case "edituserorg": return <<<EOFF
                UPDATE userorganization SET
                    function = :function,
                    email    = :email,
                    phone    = :phone,
                    mobile   = :mobile,
                    info     = :info
                WHERE id = :uoid;
EOFF;
                case "getuserorg": return <<<EOFF
                SELECT * FROM userorgs WHERE id = :uid;
EOFF;
                case "getuserorgsbyuid": return <<<EOFF
                SELECT * FROM userorgs WHERE userid = :uid;
EOFF;
                case "getuserorgsbyoid": return <<<EOFF
                SELECT * FROM userorgs WHERE organizationid = :oid;
EOFF;
                case "deleteuserorg": return <<<EOFF
            DELETE FROM userorganization
                WHERE id = :uoid;
EOFF;
            }
            return "";
        }
    }
    return new uiorganizations();
?>




