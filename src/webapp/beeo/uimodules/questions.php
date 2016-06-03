<?php
    class uiquestions extends uimodule {
        protected $moduleid    = 'questions';
        protected $modulename  = 'Questions Management';
        protected $menutxt     = 'Questions';
        protected $url         = 'questions';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",
                    $this->getstr("perm2")],
                ["manage",
                    $this->getstr("perm3")],
            ];
        }

        public function getpage($home) {
            if (!$this->cando("dv")) { /* no permission to be here */
                return error404();
            }
            $ctt = "";
            /* An up menu */
            if (!valg("up")) $_GET["up"] = "questions";
            $ctt = upmenu($home, isgd("up"), [
                $this->getstr("menu1"), "questions",
                $this->getstr("menu2"), "inputtype",
                $this->getstr("menu3"), "inputitems",
                ""]);
            $home["up"] = $_GET["up"];

            /* check for POST stuff */
            db_beggin();
            $ctt .= $this->checkpost($home);
            db_commit();

            if (valg("up", "questions") && $this->cando("dv")) {
                //$home = arraddrem($home, ["view" => isgd("view")]);
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_questions($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_questions($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_questions($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_questions($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_questions($home);
                }
            } else
            if (valg("up", "inputtype") && $this->cando("dv")) {
                //$home = arraddrem($home, ["view" => isgd("view")]);
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_inputtype($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputtype($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputtype($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputtype($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_inputtype($home);
                }
            } else
            if (valg("up", "inputitems") && $this->cando("dv")) {
                //$home = arraddrem($home, ["view" => isgd("view")]);
                if (issetg("view")) {
                    $ctt .= $this->viewaddeditdel_inputitem($home, isgd("view"));
                } else
                if (issetg("add") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputitem($home);
                } else
                if (issetg("edit") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputitem($home, isgd("edit"), "edit");
                } else
                if (issetg("delete") && $this->cando("manage")) {
                    $ctt .= $this->viewaddeditdel_inputitem($home, isgd("delete"), "delete");
                } else {
                    $ctt .= $this->dv_inputitem($home);
                }
            } else {
                return error404();
            }
            return $ctt;
        }



        /* * * * * * * * * * * * * * * QUESTIONS * * * * * * * * * * * * * * */
        private function dv_questions($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep1"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";

            $allitems = db_do(self::getsql("getquestions", gpager()), wpage());

            /* questions types */
            $inputtypes = bbselitems_fromquery(self::getsql("getinputtypes"), [], "id", "name");

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_intxt("fn0", "", "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    bb_af_form_inselbox("fn3", $inputtypes, "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getquestions"), "", ["name", "description", "itname", ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl1"), "",
                $this->getstr("lbl2"), "",
                $this->getstr("lbl4"), "",
                "",                    "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_questions($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $postpos  = "question";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* name */
            $fn["1"] = ispd("fn1"); /* description */
            $fn["2"] = ispd("fn2"); /* question */
            $fn["3"] = ispd("fn3"); /* inputtypeid */
            //$fn["4"] = ispd("fn4"); /* options */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep2");

                $dbres = db_do(self::getsql("getquestionbyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["description"];
                    $fn["2"] = $item["question"];
                    $fn["3"] = $item["itid"];
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                //$fn["4"] = explode(", ", $item["options"]);
                $fn["z"] = $item["itname"];
                //$fn["y"] = $item["itdescription"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedactivity"), ["id" => $fn["a"]])) ||
                    valdbres(db_do(self::getsql("isusedanswer"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep4");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war1"));

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        (!$editwarning ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete" && !$editwarning) { /* and delete */
                    $title   = $this->getstr("sep5");
                    $desc    = bbwarn($this->getstr("war2", $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep3");
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
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", fia("fn2"));
                $fn["3"] = bb_af_form_inselbox("fn3",
                    bbselitems_fromquery(self::getsql("getinputtypes"), [], "id", "name", $fn["3"]));
            } else {
                $fn["3"] = $fn["z"];
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_sep($this->getstr("lbl1"),        $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl3"),    $fn["2"], "", $classes) .
                bb_af_sep($this->getstr("lbl4"),  $fn["3"], "", $classes) .
                //bb_af_sep("Options",     $fn["4"], "", $classes) .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }














        /* * * * * * * * * * * * * * * INPUTYPE * * * * * * * * * * * * * * */
        private function dv_inputtype($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep6"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";

            /* input items types */
            $inputtypes = bbselitems_fromquery(self::getsql("getinputitemprimitives"), [], "id", "name") .
                          bb_af_form_inselboxitem("multiple", $this->getstr("lbl11"));

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_intxt("fn0", "", "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    bb_af_form_inselbox("fn2", $inputtypes, "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getinputtypes"), "", ["name", "description", "inputs", ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl5"), "",
                $this->getstr("lbl6"), "",
                $this->getstr("lbl7"), "",
                "",                    "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_inputtype($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $postpos  = "inputtype";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* name */
            $fn["1"] = ispd("fn1"); /* description */
            $fn["2"] = ispd("fn2"); /* input item primitive/multiple */
            $fn["3"] = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")]; /* inputitem multiple list */
            $fn["4"] = is_array(ispd("fn4")) ? ispd("fn4") : [ispd("fn4")]; /* options */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep7");

                $dbres = db_do(self::getsql("getinputtypebyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["description"];
                    $fn["3"] = explode(", ", $item["inputsid"]);
                    $fn["2"] = count($fn["3"]) >= 2 ? "multiple" : $fn["0"];
                    $fn["4"] = explode(", ", $item["options"]);
                }
                $fn["a"] = $item["id"];
                $fn["b"] = $item["name"];
                $fn["z"] = $item["inputs"];

                /* parse options */
                //$optexc = strinstr("exclusive", $fn["4"]);

                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedquestion"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep10");
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
                    $title   = $this->getstr("sep11");
                    $desc    = bbwarn($this->getstr("war4", $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep8");
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

                $dbres = db_do(self::getsql("getinputitems"));
                $soptions = "";
                $multipleopt = "";
                $ignorelist = ["Separator"];
                $validlist = ["Number", "Text", "Long Text", "Date", "Time"];
                $itemslist = [];
                foreach ($dbres as $item) {
                    if ($item["id"] < 100) {
                        if (!in_array($item["name"], $validlist)) /* ignore unknown */
                            continue;
                        $soptions .= bb_af_form_inselboxitem($item["id"], $item["name"],
                        ($item["id"] == $fn["2"] ? "selected" : ""));
                    } else {
                        $used = in_array($item["id"], $fn["3"]) ? true : false;
                        $itemslist[] = [$item["id"], $item["name"], "", $used];
                    }
                }
                $itemslist = bbbuildexclusivelist("fn3[]", $itemslist, "fn3");

                $soptions .= bb_af_form_inselboxitem("multiple", $this->getstr("lbl11"), ("multiple" == $fn["2"] ? "selected" : ""));
                $soptrem   = "multiple" == $fn["2"] ? "" : "removethis";
                $fn["2"] = bb_af_form_inselbox("fn2", $soptions, "", "", 'onchange="qtdtsel(this)"');
                $fn["3"] = $multipleopt;
                $optexc = in_array("exclusive", $fn["4"]) ? "l" : "r";

                $subform =
                    bb_af_sep($this->getstr("lbl7"), "",       "", "bbafpsep") .
                    bb_af_sep($this->getstr("lbl8"), $fn["2"], "", "bbafsitem afhover") .
                    bb_af_txt(
                        bb_af_sep($this->getstr("lblopt1"),   bb_af_form_inbool("fn4[]", "exclusive", "", $this->getstr("lblopt2"), $this->getstr("lblopt3"), $optexc), "", "bbafsitem afhover") .
                        $itemslist
                        ,"", $soptrem, 'id="qtdtselitems"');
            } else {
                $subform = "";
                if ($fn["2"] === "multiple" && in_array("exclusive", $fn["4"])) {
                    $subform.= bb_af_sep($this->getstr("lblopt1"), $this->getstr("lblopt2"), "", $classes);
                }
                //
                
                /* list of accepted values */
                $dbres = db_do(self::getsql("getinputitemsbyitid"), ["id" => $id]);
                if (count($dbres) > 1) {
                    $subform .= bb_af_sep($this->getstr("lbl7"), "", "", "bbafpsep");
                    foreach ($dbres as $item) {
                        $subform .= bb_listitem($item["name"]);
                    }
                } else {
                $subform .= bb_af_sep($this->getstr("lbl7"), $fn["z"], "", $classes);
                }
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna",          $fn["a"]) .
                bb_af_form_hidden("fnb",          $fn["b"]) .
                bb_af_sep($this->getstr("lbl5") , $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl6"),  $fn["1"], "", $classes) .
                $subform .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }











        /* * * * * * * * * * * * * * * INPUTITEM * * * * * * * * * * * * * * */
        private function dv_inputitem($home) {
            $res      = "";
            $res     .= bbbtitlef($this->getstr("sep12"));
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";

            /* form */
            $trs = "";
            if ($this->cando("manage")) {
                $trs = bb_af_form_tabler(bb_atablecs(
                    bb_af_form_intxt("fn0", "", "", "bbaffot"),
                    bb_af_form_intxt("fn1", "", "", "bbaffot"),
                    bb_af_form_submit("", bb_gets("+"), $addurl,    "", "bbaffot bbafroundbtn bbacgreen fleft")
                ));
            }
            $trs .= bb_atabler_fromquery(self::getsql("getinputitems"), "", ["name", "description", ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl9"),  "",
                $this->getstr("lbl10"), "",
                "",                     "width: 40px;"
            ], $trs);

            return $res;
        }

        private function viewaddeditdel_inputitem($home, $id = FALSE, $action = FALSE) {
            $res      = "";
            $postpos  = "inputitem";
            $getpos  = "";
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);


            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = isgd("fnb"); /* old name */
            $fn["0"] = ispd("fn0"); /* name */
            $fn["1"] = ispd("fn1"); /* description */
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep13");

                $dbres = db_do(self::getsql("getinputitembyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit$postpos") && !ispd("add$postpos")) {
                    $fn["b"] = $item["name"];
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["description"];
                }
                $fn["a"] = $item["id"];
                $editurl = gettourl($home, ["edit$getpos" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isusedinputitem"), ["id" => $fn["a"]]));

                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep15");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war5"));

                    $delurl  = gettourl($home, ["delete$getpos" => $id]);
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("edit$postpos", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        (!$editwarning ? bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred") : "");
                } else
                if ($action === "delete" && !$editwarning) { /* and delete */
                    $title   = $this->getstr("sep16");
                    $desc    = bbwarn($this->getstr("war6", $fn["0"]));
                    $subline =
                        bb_af_a($this->getstr("btncancel"), $thisurl, "margin-right: 10px;", "bbafin bfafbtn rmbg") .
                        bb_af_form_submit("delete$postpos", $this->getstr("btnconfirm"), FALSE, "", "bbafin bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep14");
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
            } else {
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna",          $fn["a"]) .
                bb_af_form_hidden("fnb",          $fn["b"]) .
                bb_af_sep($this->getstr("lbl9"),  $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl10"), $fn["1"], "", $classes) .
                ($this->cando("manage") ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }












        /* * * * * * * * * * * * * * * CHECKPOST * * * * * * * * * * * * * * */
        private function checkpost($home) {
            $res = "";
            if ((issetp("addquestion") || issetp("editquestion") || issetp("deletequestion")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getquestionbyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err2")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getquestionbyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedactivity"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("addquestion")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addquestion"), [
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                        "question"    => ispd("fn2"),
                        "inputtypeid" => ispd("fn3"),
                        "options"     => ispd("fn4"),
                    ], $this->getstr("log1", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf1", ispd("fn0")));
                }
                if (issetp("editquestion")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editquestion"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                        "question"    => ispd("fn2"),
                        "inputtypeid" => ispd("fn3"),
                        "options"     => ispd("fn4"),
                    ], $this->getstr("log2", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf2", ispd("fn0")));
                }
                if (issetp("deletequestion")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("delquestion"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log3", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf3", ispd("fnb")));
                }
            } else
            if ((issetp("addinputtype") || issetp("editinputtype") || issetp("deleteinputtype")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err3")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getinputtypebyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err4")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getinputtypebyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedquestion"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("deleteinputtype")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("deleteinputtype"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log6", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf6", ispd("fnb")));
                }
                if (issetp("addinputtype"))
                    $nexthome = arraddrem($home, "add");
                else if (issetp("editinputtype"))
                    $nexthome = arraddrem($home, ["edit" => ispd("fna")]);
                else
                    return bberror($this->getstr("errsql", __LINE__));

                /* to add or to edit, get the needed things */
                $opts = is_array(ispd("fn4")) ? ispd("fn4") : [ispd("fn4")];
                $opts = implode(", ", $opts);

                /* prepare input: (fn3) inputitem list */
                $_POST["fn3"] = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")];
                foreach (ispd("fn3") as $key => $val) {
                    if ($val === "")
                        unset($_POST["fn3"][$key]);
                }

                /* get the input */
                $iis  = [];
                if (ispd("fn2") === "multiple") {
                    $iis = is_array(ispd("fn3")) ? ispd("fn3") : [ispd("fn3")];
                    if (count($iis) < 2) {
                        $_GET = $nexthome;
                        formalert("fn3");
                        return bberror($this->getstr("err7"));
                    }
                } else {
                    $iis = [ispd("fn2")];
                }

                /* update name, description and options */
                if (issetp("addinputtype")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = $nexthome;
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addinputype"), [
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                        "options"     => $opts,
                    ], $this->getstr("log4", ispd("fn0")));
                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $id = $dbres[0]["id"];
                }
                else if (issetp("editinputtype")) {
                    if ((($validate = validate_form($valexists, $validate)) !== "")) {
                        $_GET = $nexthome;
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editinputtype"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                        "options"     => $opts,
                    ], $this->getstr("log5", ispd("fn0")));
                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    $id = ispd("fna");
                } else {
                    return bberror($this->getstr("errsql", __LINE__));
                }

                /* build query */
                $itiisquery = "";
                $itiisargs  = [];
                $ct = 0;
                foreach ($iis as $ii) {
                    $itiisquery .= "(:it$ct, :ii$ct, :p$ct), ";
                    $itiisargs["it$ct"] = $id;
                    $itiisargs["ii$ct"] = $ii;
                    $itiisargs["p$ct"]  = $ct;
                    $ct++;
                }
                $itiisquery = substr($itiisquery, 0, -2);

                /* update inputtypeinputitem */
                $dbdel = db_do(self::getsql("deleteinputtypeinputitems"), ["id" => $id],
                    $this->getstr("log10", ispd("fn0")));
                $dbadd = db_do(self::getsql("addinputtypeinputitems", $itiisquery), $itiisargs,
                    $this->getstr("log11", ispd("fn0")));
                if (!valdbres($dbdel, 0) || !valdbres($dbadd))
                    return bberror($this->getstr("errsql", __LINE__));

                if (issetp("addinputtype"))
                    return bbinfo($this->getstr("inf4", ispd("fn0")));
                else if (issetp("editinputtype"))
                    return bbinfo($this->getstr("inf5", ispd("fn0")));
            } else
            if ((issetp("addinputitem") || issetp("editinputitem") || issetp("deleteinputitem")) && $this->cando("manage")) {
                $validate = [
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err5")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getinputitembyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err6")], "formalert", ["fn0"]],
                ];
                $valexists = [
                    /* if id is correct */
                    ["valdb_e", [self::getsql("getinputitembyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                $valdelete = [
                    /* if this questions is used anywhere */
                    ["valdb_ne", [self::getsql("isusedinputitem"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando")]]
                ];
                if (issetp("addinputitem")) {
                    if ((($validate = validate_form($validate)) !== "")) {
                        $_GET = arraddrem($home, "add");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addinputitem"), [
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log7", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf7", ispd("fn0")));
                }
                if (issetp("editinputitem")) {
                    if ((($validate = validate_form($validate, $valexists)) !== "")) {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("editinputitem"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "description" => ispd("fn1"),
                    ], $this->getstr("log8", ispd("fn0")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf8", ispd("fn0")));
                }
                if (issetp("deleteinputitem")) {
                    if ((($validate = validate_form($valexists, $valdelete)) !== "")) {
                        $_GET = arraddrem($home, ["view" => ispd("fna")]);
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("deleteinputitem"), [
                        "id"          => ispd("fna"),
                    ], $this->getstr("log9", ispd("fnb")));

                    if (!valdbres($dbres)) {
                        return bberror($this->getstr("errsql", __LINE__));
                    }
                    return bbinfo($this->getstr("inf9", ispd("fnb")));
                }
            }
            return $res;
        }
        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what, $pager = "") {
            switch ($what) {
                /* * * * * * * * * * * * * * * * * * * * * QUESTIONS * * * * */
                case "getquestions": return <<<EOFF
                SELECT question.*, inputtype.name AS itname, inputtype.description AS itdescription, inputtype.id AS itid
                    FROM question
                    JOIN inputtype
                      ON question.inputtypeid = inputtype.id
                    ORDER BY question.name
                    $pager;
EOFF;
                case "getquestionbyid": return <<<EOFF
                SELECT question.*, inputtype.name AS itname, inputtype.description AS itdescription, inputtype.id AS itid
                    FROM question
                    JOIN inputtype
                      ON question.inputtypeid = inputtype.id
                    WHERE question.id = :id;
EOFF;
                case "getquestionbyname": return <<<EOFF
                SELECT * FROM question WHERE name = :name;
EOFF;
                case "isusedactivity": return <<<EOFF
                SELECT * FROM activityquestion
                    WHERE questionid = :id;
EOFF;
                case "isusedanswer": return <<<EOFF
                SELECT * FROM answer
                    WHERE questionid = :id;
EOFF;
                case "addquestion": return <<<EOFF
                INSERT INTO question (name, description, question, inputtypeid, options)
                    VALUES (:name, :description, :question, :inputtypeid, :options);
EOFF;
                case "editquestion": return <<<EOFF
                UPDATE question SET
                        name        = :name,
                        description = :description,
                        question    = :question,
                        inputtypeid = :inputtypeid,
                        options     = :options
                    WHERE id = :id;
EOFF;
                case "delquestion": return <<<EOFF
                DELETE FROM question WHERE id = :id;
EOFF;
                case "": return <<<EOFF

EOFF;
                /* * * * * * * * * * * * * * * * * * * * * INPUTYPE * * * * */
                case "getinputtypes": return <<<EOFF
                SELECT inputtype.id, inputtype.name, inputtype.description, inputtype.options,
                    STRING_AGG(inputitem.name, ', ' ORDER BY inputtypeinputitem.precedence ASC) AS inputs
                    FROM inputtype
                        FULL OUTER JOIN inputtypeinputitem
                            ON inputtypeinputitem.inputtypeid = inputtype.id
                        FULL OUTER JOIN inputitem
                            ON inputtypeinputitem.inputitemid = inputitem.id
                     WHERE inputtype.id IS NOT NULL
                     GROUP BY inputtype.id
                     ORDER BY inputtype.name ASC
                     $pager;
EOFF;
                case "getinputtypebyid": return <<<EOFF
                SELECT inputtype.id, inputtype.name, inputtype.description, inputtype.options,
                    STRING_AGG(inputitem.name,     ', ' ORDER BY inputtypeinputitem.precedence ASC) AS inputs,
                    STRING_AGG(inputitem.id::text, ', ' ORDER BY inputtypeinputitem.precedence ASC) AS inputsid
                    FROM inputtype
                        FULL OUTER JOIN inputtypeinputitem
                            ON inputtypeinputitem.inputtypeid = inputtype.id
                        FULL OUTER JOIN inputitem
                            ON inputtypeinputitem.inputitemid = inputitem.id
                     WHERE inputtype.id IS NOT NULL
                       AND inputtype.id = :id
                     GROUP BY inputtype.id
                     ORDER BY inputtype.name ASC
EOFF;
                case "getinputtypebyname": return <<<EOFF
                SELECT *
                    FROM inputtype
                    WHERE name = :name;
EOFF;
                case "isusedquestion": return <<<EOFF
                SELECT * FROM question WHERE inputtypeid = :id;
EOFF;
                case "addinputype": return <<<EOFF
                INSERT INTO inputtype (name, description, options)
                    VALUES (:name, :description, :options)
                RETURNING id;
EOFF;
                case "editinputtype": return <<<EOFF
                UPDATE inputtype SET
                        name        = :name,
                        description = :description,
                        options     = :options
                    WHERE id = :id;
EOFF;
                case "addinputtypeinputitems": return <<<EOFF
                INSERT INTO inputtypeinputitem (inputtypeid, inputitemid, precedence)
                    VALUES $pager;
EOFF;
                case "deleteinputtype": return <<<EOFF
                DELETE FROM inputtype
                    WHERE id = :id;
EOFF;
                case "deleteinputtypeinputitems": return <<<EOFF
                DELETE FROM inputtypeinputitem
                    WHERE inputtypeid = :id;
EOFF;
                /* * * * * * * * * * * * * * * * * * * * * INPUTITEM * * * * */
                case "getinputitems": return <<<EOFF
                SELECT * FROM inputitem
                    ORDER BY name
                    $pager;
EOFF;
                case "getinputitemprimitives": return <<<EOFF
                SELECT * FROM inputitem
                    WHERE id <= 99
                    ORDER BY name;
EOFF;
                case "getinputitemuser": return <<<EOFF
                SELECT * FROM inputitem
                    WHERE id >= 100
                    ORDER BY name;
EOFF;
                case "getinputitemsbyitid": return <<<EOFF
                SELECT inputitem.*, inputtypeinputitem.inputitemid, inputtypeinputitem.inputtypeid
                    FROM inputitem
                        JOIN inputtypeinputitem
                          ON inputitem.id = inputtypeinputitem.inputitemid
                    WHERE inputtypeinputitem.inputtypeid = :id
                    ORDER BY inputtypeinputitem.precedence ASC
EOFF;
                case "getinputitembyid": return <<<EOFF
                SELECT * FROM inputitem WHERE id = :id;
EOFF;
                case "getinputitembyname": return <<<EOFF
                SELECT * FROM inputitem WHERE name = :name;
EOFF;
                case "isusedinputitem": return <<<EOFF
                SELECT * FROM inputtypeinputitem WHERE inputitemid = :id;
EOFF;
                case "addinputitem": return <<<EOFF
                INSERT INTO inputitem (name, description) VALUES (:name, :description);
EOFF;
                case "editinputitem": return <<<EOFF
                UPDATE inputitem SET
                        name        = :name,
                        description = :description
                    WHERE id = :id;
EOFF;
                case "deleteinputitem": return <<<EOFF
                DELETE FROM inputitem WHERE id = :id;
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
    return new uiquestions();
?>
