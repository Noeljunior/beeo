<?php
    class uiusermanagement extends uimodule {
        protected $moduleid    = 'usermanagement';
        protected $modulename  = 'User Management';
        protected $menutxt     = 'Users';
        protected $url         = 'users';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",              $this->getstr("perm1")],
                ["add",             $this->getstr("perm2")],
                ["view",            $this->getstr("perm3")],
                ["edit",            $this->getstr("perm4")],
                ["delete",          $this->getstr("perm5")],
                ["respasswd",       $this->getstr("perm6")],
                ["viewoadd",        $this->getstr("perm7")],
                ["viewodv",         $this->getstr("perm8")],
                ["viewo",           $this->getstr("perm9")],
                ["viewoedit",       $this->getstr("perm10")],
                ["viewodel",        $this->getstr("perm11")],
                ["viewl",           $this->getstr("perm12")],
            ];
        }

        public function getpage($home) {
            $ctt = "";
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();

            /* selector */
            if (issetg("add") && $this->cando("add")) {
                $ctt .= $this->viewaddeditdel($home);
            } else
            if (issetg("view") && $this->cando("view")) {
                $ctt .= $this->viewaddeditdel($home, isgd("view"));
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
            $searchurl = gettourl($home, "search");
            $viewurl   = gettourl($home, "view") . "=";

            $queryname = "listusers";
            $r         = "";
            $args      = [];
            if (issetg("search") && ispd("fn0")) {
                $names = explode(" ", $_POST["fn0"]);
                $ct = 0;
                foreach ($names AS $name) {
                    $name = trim($name);
                    if ($name === "") continue;
                    $r .= "SELECT * FROM userinfo WHERE name ILIKE :n$ct UNION ALL\n";
                    $args["n$ct"] = "%" . $name . "%";
                    $ct++;
                }
                if ($ct > 0) {
                    $r .= substr($r, 0, strrpos($r, "UNION ALL"));
                    $queryname = "searchusers";
                }
            } else
            if (issetp("adduser")) { // TODO WORKAROUND: ignore repost if added
                $_POST["fn0"] = "";
                $_POST["fn2"] = "";
                $_POST["fn3"] = "";
                $_POST["fn4"] = "";
                $_POST["fn7"] = "";
            }

            $allusers = db_do(self::getsql($queryname, $r), $args);

            $allroles = db_do(self::getsql("getroles"));
            $rolesform = "";
            $previousroles = is_array(ispd("fn7")) ? ispd("fn7") : [];
            foreach ($allroles as $eachrole) {
                $rolesform .= bb_af_form_incheck(
                    "fn7[]", $eachrole["id"], $eachrole["letter"], "", "",
                    "bfafbtn bbaffot bbafroundbtn small bbacyel", "",
                    (in_array($eachrole["id"], $previousroles) ? "checked" : ""));
            }

            /* form */
            $trs = bb_af_form_tabler(bb_atablecs(
                bb_af_form_intxt("fn0", ispd("fn0"), "", "bbaffot"),
                bb_af_form_intxt("fn2", ispd("fn2"), "", "bbaffot"),
                bb_af_form_intxt("fn3", ispd("fn3"), "", "bbaffot"),
                bb_af_form_intxt("fn4", ispd("fn4"), "", "bbaffot"),
                $rolesform,
                bb_af_form_submit("", bb_gets("?"), $searchurl, "", "bbaffot bbafroundbtn bbacblue rotate45") .
                ($this->cando("add") ? bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft") : "")
            ));

            /* users list */
            foreach ($allusers as $user) {
                $roles = explode(", ", $user["roles"]);
                $bbroles = "";
                foreach ($allroles as $eachrole) {
                    $bbroles .= bb_af_txt($eachrole["letter"], "",
                        "bbafin bbafroundbtn small " . (in_array($eachrole["id"], $roles) ? "" : "hidethis"));
                }

                $cells = bb_atablecs(
                            $user["name"],  /*$user["username"], */$user["email"],
                            $user["phone"], $user["mobile"],  /* $user["url"],
                            $user["info"],  */$bbroles, ""
                        );

                if ($this->cando("view"))
                    $trs .= bb_atabler_link($cells, $viewurl . $user["id"]);
                else
                    $trs .= bb_atabler($cells);
            }
            /* the table */
            $roleswith = (max(count($allroles) * 27, 45)) . "px";
            $res .= bb_atable_hfi(array(
                $this->getstr("lbl1"),     "",
                $this->getstr("lbl3"),   "",
                $this->getstr("lbl4"),    "",
                $this->getstr("lbl5"),   "",
                $this->getstr("lbl8"),    "width: $roleswith;",
                "",         "width: 80px;"
            ), $trs);

            return $res;
        }

        private function viewaddeditdel($home, $id = FALSE, $action = FALSE) {
            $ohome = arraddrem($home, ["view" => isgd("view")]);
            if (issetg("adduserorg") && $this->cando("viewoadd")) {
                return $this->viewaddeditdel_userorg($ohome);
            } else
            if (issetg("viewuserorg") && $this->cando("viewo")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("viewuserorg"));
            } else
            if (issetg("edituserorg") && $this->cando("viewoedit")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("edituserorg"), "edit");
            } else
            if (issetg("deleteuserorg") && $this->cando("viewodel")) {
                return $this->viewaddeditdel_userorg($ohome, isgd("deleteuserorg"), "delete");
            }

            $res      = "";
            $postpos  = "user";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);

            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = ispd("fnb"); /* old username */
            $fn["0"] = ispd("fn0");
            $fn["1"] = ispd("fn1");
            $fn["2"] = ispd("fn2");
            $fn["3"] = ispd("fn3");
            $fn["4"] = ispd("fn4");
            $fn["5"] = ispd("fn5");
            $fn["6"] = ispd("fn6");
            $fn["7"] = is_array(ispd("fn7")) ? ispd("fn7") : [ispd("fn7")];
            $fn["z"] = $fn["0"];
            $fn["y"] = $fn["1"];
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;
            $resetpasswdbtn = "";

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep2");

                $dbres = db_do(self::getsql("getuserbyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["username"];
                    $fn["2"] = $item["email"];
                    $fn["3"] = $item["phone"];
                    $fn["4"] = $item["mobile"];
                    $fn["5"] = $item["url"];
                    $fn["6"] = $item["info"];
                    $fn["7"] = ($item["roles"] !== NULL ? explode(", ", $item["roles"]) : []);
                }
                $fn["a"] = $id;
                $fn["b"] = $item["username"];
                $fn["z"] = ($item["rolesletters"] !== NULL ? explode(", ", $item["rolesletters"]) : []);
                $fn["y"] = $item["name"];
                $editurl  = gettourl($home, ["edit" => $id]);
                $subline =
                    $this->cando("edit") ? bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn") : "";
                if ($item["roles"] === NULL)
                    $res .= bbwarn($this->getstr("war1", $fn["0"]));


                $editwarning = valdbres(db_do(self::getsql("checkactivity"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep6");
                    $faction = $thisurl;

                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war2"));

                    $delurl  = gettourl($home, ["delete" => $id]);

                    $resetpasswdbtn = bb_af_sep(($this->cando("respasswd") ? bb_af_form_submit("resetuserpass", $this->getstr("btn1"), FALSE, "margin-top: 10px;", "bbacred") : ""), "", "", $classes);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        ($this->cando("delete") && !$editwarning ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete" && !$editwarning) { /* and delete */
                    $title   = $this->getstr("sep7");
                    $desc    = bbwarn($this->getstr("war3", $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep5");
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

                /* roles form */
                $dbres = db_do(self::getsql("getroles"));
                $rolesform = "";
                foreach ($dbres as $eachrole) {
                    $rolesform .= bb_af_form_incheck(
                        "fn7[]", $eachrole["id"], $eachrole["letter"], "", "",
                        "bfafbtn bbafroundbtn bbacyel" . fia("fn7"), "",
                        (in_array($eachrole["id"], $fn["7"]) ? "checked" : ""));/**/
                    /*$rolesform .= bb_af_form_incheck(
                        "fn7[]", $eachrole["id"],
                        $eachrole["name"], "", "",
                        "" . fia("fn7"), "",
                        (in_array($eachrole["id"], $theuser["roles"]) ? "checked" : ""));/**/
                }
                $fn["7"] = $rolesform;
            } else {
                $bbroles = "";
                foreach ($fn["z"] as $eachrole) {
                    $bbroles .= bb_af_txt($eachrole, "", "bbafin bbafroundbtn");
                }
                $fn["7"] = $bbroles;
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_form_hidden("fny", $fn["y"]) .
                bb_af_sep($this->getstr("lbl1"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl3"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("lbl4"), $fn["3"], "", $classes) .
                bb_af_sep($this->getstr("lbl5"), $fn["4"], "", $classes) .
                bb_af_sep($this->getstr("lbl6"), $fn["5"], "", $classes) .
                bb_af_sep($this->getstr("lbl7"), $fn["6"], "", $classes) .
                bb_af_sep($this->getstr("lbl8"), $fn["7"], "", $classes) .
                $resetpasswdbtn .
                ($subline !== "" ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);

            if ($id !== FALSE && $action == FALSE) {
                if ($this->cando("viewodv"))
                    $res .= $this->view_userorg($ohome, $id);
                if ($this->cando("viewl"))
                    $res .= $this->view_log($id);
            }
            return $res;
        }

        private function view_userorg($home, $id) {
            $res = bb_af_sep($this->getstr("sep3"), "", "", "bbafpsep bbafpsepbig");
            $viewurl = gettourl($home, "viewuserorg") . "=";
            $addurl  = gettourl($home, "adduserorg");
            $dbres = db_do(uiorganizations::getsql("getorgs"));
            $soptions = "";
            foreach ($dbres as $item) {
                $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"]);
            }
            /* form */
            $trs = "";
            if ($this->cando("viewoadd")) {
                $trs .= bb_af_form_tabler(bb_atablecs(
                    bb_af_form_hidden("fnb", $id) .
                    bb_af_form_inselbox("fna", $soptions, "", "bbaffot"),
                    bb_af_form_intxt("fn2", "", "", "bbaffot"),
                    bb_af_form_intxt("fn3", "", "", "bbaffot"),
                    bb_af_form_intxt("fn4", "", "", "bbaffot"),
                    bb_af_form_intxt("fn5", "", "", "bbaffot"),
                    bb_af_form_intxt("fn6", "", "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), FALSE, "", "bbaffot bbafroundbtn bbacgreen")
                ), $addurl);
            }

 
            $trs .= bb_atabler_fromquery(uiorganizations::getsql("getuserorgsbyuid"), ["uid" => $id], ["oname", "function", "email", "phone", "mobile", "info", ""],
            ($this->cando("viewo") ? $viewurl : ""));

            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lblorg1") , "",
                $this->getstr("lblorg2") , "",
                $this->getstr("lblorg3") , "",
                $this->getstr("lblorg4") , "",
                $this->getstr("lblorg5") , "",
                $this->getstr("lblorg6") , "",
                "",             "width:40px;",
            ], $trs);
            return $res;
        }

        private function view_log($id) {
            $res = bb_af_sep($this->getstr("sep4"), "", "", "bbafpsep bbafpsepbig");
            $trs = "";
            $trs .= bb_atabler_fromquery(self::getsql("useractivity"), ["user" => $id, "limit" => "100"], [["bbdbdatetostr"], "rolename", "description"]);
            $res .= bb_atable_hfi([
                $this->getstr("lbl9"),  "width: 210px;",
                $this->getstr("lbl10"), "width: 100px;",
                $this->getstr("lbl11"), ""
            ], $trs);
            return $res;
        }

        private function viewaddeditdel_userorg($home, $id = FALSE, $action = FALSE) {
            $res     = "";
            $postpos = "userorg";
            $getpos  = "userorg";
            $homeurl = gettourl($home);
            $thisurl = gettourl($home, ["view$getpos" => $id]);


            $fn["a"] = ispd("fna"); /* oid */
            $fn["b"] = isgd("view"); /* uid */
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

            $dbres = db_do(self::getsql("getuserbyid"), ["id" => $fn["b"]]);
            if (!valdbres($dbres, 1, 1))
                return bberror($this->getstr("errnoexist"));
            $item = $dbres[0];
            $fn["b"] = $item["id"];
            $fn["1"] = $fn["z"] = $item["name"];

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep9");

                $dbres = db_do(uiorganizations::getsql("getuserorg"), ["uid" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["2"] = $item["function"];
                    $fn["3"] = $item["email"];
                    $fn["4"] = $item["phone"];
                    $fn["5"] = $item["mobile"];
                    $fn["6"] = $item["info"];
                }
                $fn["0"] = $fn["y"] = $item["oname"];
                $fn["a"] = $item["organizationid"];
                $fn["c"] = $id;
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline = $this->cando("viewoedit") ?
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn") : "";

                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep10");
                    $faction = $thisurl;

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        ($this->cando("viewodel") ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete") { /* and delete */
                    $title   = $this->getstr("sep11");
                    $desc    = bbwarn($this->getstr("war4", $fn["1"], $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep8");
                $fclass  = "bbfedit";
                $subline = $this->cando("viewoadd") ? (
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
                    $dbres = db_do(uiorganizations::getsql("getorgs"));
                    $soptions = "";
                    foreach ($dbres as $item) {
                        $seled = $fn["a"] == $item["id"] ? "selected" : "";
                        $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"], $seled);
                    }
                    $fn["0"] = bb_af_form_inselbox("fna", $soptions);
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
                bb_af_sep($this->getstr("lbl1"),    $fn["1"], "", "bbafstxt") .
                bb_af_sep($this->getstr("lblorg1"), $fn["0"], "", $classesorg) .
                bb_af_sep($this->getstr("lblorg2"), $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("lblorg3"), $fn["3"], "", $classes) .
                bb_af_sep($this->getstr("lblorg4"), $fn["4"], "", $classes) .
                bb_af_sep($this->getstr("lblorg5"), $fn["5"], "", $classes) .
                bb_af_sep($this->getstr("lblorg6"), $fn["6"], "", $classes) .
                ($subline !== "" ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }

        private function checkpost($home) {
            $res = "";
            if (issetp("adduser") || issetp("edituser")) {
                $uservalid = [
                    /* new name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                    /* new username */
                    ["valdb_upname", [ispd("fnb"), ispd("fn1"), self::getsql("getuserbyusername"), ["user" => ispd("fn1")]],
                        "bberror", [$this->getstr("err3")], "formalert", ["fn1"]],
                    /* new username */
                    ["valp", ["fn1"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn1"]],
                ];

                $usereditvalid = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getuserbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];

                if (issetp("adduser")) {
                    if ((($validate = validate_form($uservalid)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("adduser"), [
                        "name"   => ispd("fn0"),
                        "user"   => ispd("fn1"),
                        "email"  => ispd("fn2"),
                        "phone"  => ispd("fn3"),
                        "mobile" => ispd("fn4"),
                        "url"    => ispd("fn5"),
                        "info"   => ispd("fn6"),
                    ], $this->getstr("log1", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 1));
                        return $res;
                    }
                    $uid = $dbres[0]["id"];
                    $dbroles = db_do(self::getsql("getroles"));
                    $roles = [];
                    foreach ($dbroles as $role) {
                        $roles[$role["id"]] = $role["name"];
                    }
                    $rolescount = 0;
                    foreach ((is_array(ispd("fn7")) ? ispd("fn7") : []) as $urid) {
                        $urres = db_do(self::getsql("adduserrole"), [
                            "uid" => $uid,
                            "rid" => $urid],
                                $this->getstr("log8", ispd("fn0"), $roles[$role["id"]]));
                        if (!valdbres($urres)) {
                            $res = bberror($this->getstr("errsql", 2));
                            return $res;
                        }
                        $rolescount++;
                    }
                    if ($rolescount <= 0) {
                        $res .= bbinfo($this->getstr("inf8", ispd("fn0")));
                        $res .= bbwarn($this->getstr("war1", ispd("fn0")));
                    } else {
                        $res .= bbinfo($this->getstr("inf1", ispd("fn0")));
                    }
                } else
                if (issetp("edituser")) {
                    if ((($validate = validate_form(array_merge($usereditvalid, $uservalid))) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")], "view");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("edituser"), [
                        "name"   => ispd("fn0"),
                        "user"   => ispd("fn1"),
                        "email"  => ispd("fn2"),
                        "phone"  => ispd("fn3"),
                        "mobile" => ispd("fn4"),
                        "url"    => ispd("fn5"),
                        "info"   => ispd("fn6"),
                        "uid"    => ispd("fna")
                    ], $this->getstr("log2", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 3));
                        return $res;
                    }
                    /* remove all roles, then added the new ones */
                    $dbrdel = db_do(self::getsql("deluserroles"), [
                        "uid" => ispd("fna")
                    ], $this->getstr("log9", ispd("fn0")));
                    if (!valdbres($dbres, 0)) {
                        $res .= bberror($this->getstr("errsql, 4"));
                        return $res;
                    }
                    /* update the user roles */
                    $uid = ispd("fna");
                    $dbroles = db_do(self::getsql("getroles"));
                    $roles = [];
                    foreach ($dbroles as $role) {
                        $roles[$role["id"]] = $role["name"];
                    }
                    foreach ((is_array(ispd("fn7")) ? ispd("fn7") : []) as $urid) {
                        $urres = db_do(self::getsql("adduserrole"), [
                            "uid" => $uid,
                            "rid" => $urid],
                                $this->getstr("log8", ispd("fny"), $roles[$role["id"]]));
                        if (!valdbres($urres)) {
                            $res = bberror($this->getstr("errsql", 5));
                            return $res;
                        }
                    }
                    $res .= bbinfo($this->getstr("inf3", ispd("fn0")));
                }
            } else
            if (issetp("resetuserpass")) {
                $dbres = db_do(self::getsql("resetuserpass"), ["uid" => ispd("fna")],
                    $this->getstr("log4", ispd("fny")));
                if (!valdbres($dbres))
                    return bberror($this->getstr("errsql", 6));
                $res .= bbinfo($this->getstr("inf2", ispd("fny")));
            } else
            if (issetp("deleteuser")) {
                if (valdbres(db_do(self::getsql("deleteuser"), ["uid" => ispd("fna")],
                    $this->getstr("log3", ispd("fny"))))) {
                    return bberror($this->getstr("errsql", 7));
                }
                $res .= bbinfo($this->getstr("inf4", ispd("fny")));
            } else
            if (issetp("edituserorg") || issetp("deleteuserorg") || issetp("adduserorg")) {
                $valuando = [
                    /* if user exists */
                    ["valdb_e", [self::getsql("getuserbyid"), ["id" => ispd("fnb")]],
                        "bberror", [$this->getstr("errnoexist")]],
                    /* if organization exists */
                    ["valdb_e", [uiorganizations::getsql("getorgbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnoexist")]],
                ];

                $valuserorg = [
                    /* if userorg exists */
                    ["valdb_e", [uiorganizations::getsql("getuserorg"), ["uid" => ispd("fnc")]],
                        "bberror", [$this->getstr("errnoexist")]],

                ];
                if (issetp("edituserorg") && $this->cando("viewoedit")) {
                    if (($validate = validate_form($valuserorg)) !== "") {
                        $_GET = arraddrem($home, ["edituserorg" => ispd("fnc")], "viewuserorg");
                        return $validate;
                    }
                    $dbres = db_do(uiorganizations::getsql("edituserorg"), [
                        "uoid"     => ispd("fnc"),
                        "function" => ispd("fn2"),
                        "email"    => ispd("fn3"),
                        "phone"    => ispd("fn4"),
                        "mobile"   => ispd("fn5"),
                        "info"     => ispd("fn6"),
                    ], $this->getstr("log6", ispd("fnz"), ispd("fny")));
                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 8));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf6", ispd("fnz"), ispd("fny")));
                } else
                if (issetp("deleteuserorg") && $this->cando("viewodel")) {
                    if (($validate = validate_form($valuserorg)) !== "") {
                        $_GET = arraddrem($home, ["deleteuserorg" => ispd("fnc")], "viewuserorg");
                        return $validate;
                    }
                    $dbres = db_do(uiorganizations::getsql("deleteuserorg"), ["uoid"   => ispd("fnc")],
                        $this->getstr("log7", ispd("fnz"), ispd("fny")));
                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", 9));
                    }
                    $res .= bbinfo($this->getstr("inf7", ispd("fnz"), ispd("fny")));
                } else
                if (issetp("adduserorg") && $this->cando("viewoadd")) {
                    if (($validate = validate_form($valuando)) !== "") {
                        $_GET = arraddrem($home, "adduserorg", "viewuserorg");
                        return $validate;
                    }
                    $dborg = db_do(uiorganizations::getsql("getorgbyid"), ["id" => ispd("fna")]);
                    $dbres = db_do(uiorganizations::getsql("adduserorg"), [
                        "organizationid" => ispd("fna"),
                        "userid"         => ispd("fnb"),
                        "function"       => ispd("fn2"),
                        "email"          => ispd("fn3"),
                        "phone"          => ispd("fn4"),
                        "mobile"         => ispd("fn5"),
                        "info"           => ispd("fn6"),
                    ], $this->getstr("log5", ispd("fnz"), $dborg[0]["name"]));
                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", 8));
                    }
                    $res .= bbinfo($this->getstr("inf5", ispd("fnz"), $dborg[0]["name"]));
                }
            }
            return $res;
        }

        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what, $r = "") {
            switch ($what) {
                case "listusers": return <<<EOFF
                SELECT * FROM userinfo ORDER BY name, username, id ASC;
EOFF;
                case "searchusers": return <<<EOFF
                SELECT count(results.id) AS qty, results.*
                    FROM ( $r ) AS results
                    GROUP BY id, name, username, email, phone, mobile, url, info, roles, rolesletters
                    ORDER BY qty DESC, name ASC;
EOFF;
                case "getuserbyid": return <<<EOFF
                SELECT * FROM userinfo WHERE  id = :id;
EOFF;
                case "getuserbyusername": return <<<EOFF
                SELECT * FROM userinfo WHERE  username = :user;
EOFF;
                case "useractivity": return <<<EOFF
                SELECT * FROM userlogs WHERE userid = :user
                    ORDER BY date DESC
                    LIMIT :limit;
EOFF;
                case "adduser": return <<<EOFF
                INSERT INTO useraccount (name, username, email, phone, mobile, url, info, password)
                    VALUES (:name, :user, :email, :phone, :mobile, :url, :info, '')
                    RETURNING id;
EOFF;
                case "adduserrole": return <<<EOFF
                INSERT INTO userroles (userid, roleid)
                    VALUES (:uid, :rid);
EOFF;
                case "edituser": return <<<EOFF
                UPDATE useraccount SET
                        name     = :name,
                        username = :user,
                        email    = :email,
                        phone    = :phone,
                        mobile   = :mobile,
                        url      = :url,
                        info     = :info
                    WHERE id = :uid;
EOFF;
                case "deleteuser": return <<<EOFF
                DELETE FROM useraccount
                    WHERE id = :uid;
EOFF;
                case "deluserroles": return <<<EOFF
                DELETE FROM userroles
                    WHERE userid = :uid;
EOFF;
                case "resetuserpass": return <<<EOFF
                UPDATE useraccount SET
                        password = ''
                    WHERE id = :uid;
EOFF;
                case "checkuseractions": return <<<EOFF
                 SELECT * FROM event
                    WHERE userid = :uid;
EOFF;
                case "checkuserevents": return <<<EOFF
                 SELECT * FROM action
                    WHERE userid = :uid;
EOFF;
                case "getroles": return <<<EOFF
                SELECT letter, name, id FROM role
                    WHERE role.id >= 1
                    ORDER BY letter;
EOFF;
                case "checkactivity": return <<<EOFF
                SELECT foo.userid, sum(foo.count) FROM (
                        SELECT userid, count(*) FROM action GROUP BY userid
                        UNION
                        SELECT userid, count(*) FROM appointment GROUP BY userid
                        UNION
                        SELECT userid, count(*) FROM patient GROUP BY userid
                    ) AS foo
                    WHERE foo.userid = :id
                    GROUP BY foo.userid;
EOFF;
            }
            return "";
        }
    }
    return new uiusermanagement();
?>
