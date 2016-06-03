<?php
    class uisysadmin extends uimodule {
        protected $moduleid    = 'sysadmin';
        protected $modulename  = 'System administration';
        protected $menutxt     = 'Sysadmin';
        protected $url         = 'sysadmin';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",
                    $this->getstr("perm1")],
            ];
        }

        public function getpage($home) {
            $ctt = "";
            if ($this->cando("dv")) {
                if (!valg("up"))
                    $_GET["up"] = "roles";
                $ctt = upmenu($home, isgd("up"), [
                    $this->getstr("menu1"), "roles",
                    $this->getstr("menu2"), "permissions",
                    ""]);
                $home["up"] = $_GET["up"];
                /* check for POST stuff */
                db_beggin();
                $ctt .= $this->checkpost($home);
                db_commit();

                if (valg("up", "roles")) {
                    if (issetg("add")) {
                        $ctt .= $this->viewaddeditdel_roles($home);
                    } else
                    if (issetg("view")) {
                        $ctt .= $this->viewaddeditdel_roles($home, isgd("view"));
                    } else
                    if (issetg("edit")) {
                        $ctt .= $this->viewaddeditdel_roles($home, isgd("edit"), "edit");
                    } else
                    if (issetg("delete")) {
                        $ctt .= $this->viewaddeditdel_roles($home, isgd("delete"), "delete");
                    } else {
                        $ctt .= $this->dv_roles($home);
                    }
                } else
                if (valg("up", "permissions")) {
                    $ctt .= $this->dv_permissions($home);
                } else {
                    return error404();
                }
            } else { /* no permission */
                return error404();
            }
            return $ctt;
        }

        private function dv_roles($home) {
            $res = "";
            $res .= bb_af_sep($this->getstr("sep1"), "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $addurl   = gettourl($home, "add");
            $viewurl  = gettourl($home, "view") . "=";
            $trs = bb_af_form_tabler(bb_atablecs(
                bb_af_form_intxt("fn0", "", "", "bbaffot"),
                bb_af_form_intxt("fn1", "", 'maxlength="1"', "bbaffot"),
                bb_af_form_intxt("fn2", "", "", "bbaffot"),
                bb_af_form_submit("", bb_gets("+"), FALSE, "", "bbaffot bbafroundbtn bbacgreen")
            ), $addurl);
            $trs .= bb_atabler_fromquery(self::getsql("getroleslist"), "", ["name", "letter", "description", ""],
            $viewurl);
            /* the table */
            $res .= bb_atable_hfi([
                $this->getstr("lbl1"), "width: 125px;",
                $this->getstr("lbl2"), "width: 30px;",
                $this->getstr("lbl3"), "",
                "",            "width: 40px;"
            ], $trs);
            return $res;
        }

        private function viewaddeditdel_roles($home, $id = FALSE, $action = FALSE) {
            $res = "";
            
            $homeurl  = gettourl($home);
            $thisurl  = gettourl($home, ["view" => $id]);

            $fn["a"] = ispd("fna"); /* id */
            $fn["b"] = ispd("fnb"); /* old name */
            $fn["c"] = ispd("fnc"); /* old letter */
            $fn["0"] = ispd("fn0");
            $fn["1"] = ispd("fn1");
            $fn["2"] = ispd("fn2");
            $subline = "";
            $desc    = "";
            $classes = "bbafstxt";
            $fclass  = "";
            $faction = $homeurl;

            if ($id !== FALSE) { /* view */
                $title  = $this->getstr("sep2");

                $dbres = db_do(self::getsql("getrolebyid"), ["id" => $id]);
                if (!valdbres($dbres, 1, 1))
                    return bberror($this->getstr("errnoexist"));
                $item = $dbres[0];

                if (!ispd("edit") && !ispd("add")) {
                    $fn["0"] = $item["name"];
                    $fn["1"] = $item["letter"];
                    $fn["2"] = $item["description"];
                }
                $fn["a"] = $id;
                $fn["b"] = $item["name"];
                $fn["c"] = $item["letter"];
                $editurl = gettourl($home, ["edit" => $id]);
                $subline =
                    bb_af_a($this->getstr("btnedit"), $editurl, "", "bbafin bfafbtn");

                $editwarning = valdbres(db_do(self::getsql("isrolebeeingused"), ["id" => $fn["a"]]));
                if ($action === "edit") { /* and edit */
                    $title  = $this->getstr("sep4");
                    $faction = $thisurl;
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war1"));
                    $delurl  = gettourl($home, ["delete" => $id], ["add", "edit"]);
                    $subline =
                        bb_af_form_submit("editrole", $this->getstr("btnupdate"), FALSE, "", "bbacgreen") .
                        bb_af_a($this->getstr("btndelete"), $delurl, "", "bfafbtn fleft bbacred");
                } else
                if ($action === "delete") { /* and delete */
                    $title   = $this->getstr("sep5");
                    if ($editwarning)
                        $desc = bbwarn($this->getstr("war2"));
                    $subline =
                        bb_af_form_submit("deleterole", $this->getstr("btnconfirm"), FALSE, "", "bbacred");
                }

            } else { /* add */
                $title  = $this->getstr("sep3");
                $fclass  = "bbfedit";
                $subline =
                    bb_af_form_submit("addrole", $this->getstr("btncreate"), FALSE, "", "bbacgreen");
            }

            /* formify */
            if ($id == FALSE || $action === "edit") {
                $classes = "bbafsitem afhover";
                $fclass  = "bbfedit";
                $fn["0"] = bb_af_form_intxt("fn0", $fn["0"], "", fia("fn0"));
                $fn["1"] = bb_af_form_intxt("fn1", $fn["1"], 'maxlength="1"', fia("fn1"));
                $fn["2"] = bb_af_form_intxt("fn2", $fn["2"], "", "");
            }

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $desc;
            $res .= bb_af_form(
                bb_af_form_hidden("fna", $fn["a"]) .
                bb_af_form_hidden("fnb", $fn["b"]) .
                bb_af_form_hidden("fnc", $fn["c"]) .
                bb_af_sep($this->getstr("lbl1"), $fn["0"], "", $classes) .
                bb_af_sep($this->getstr("lbl2"), $fn["1"], "", $classes) .
                bb_af_sep($this->getstr("lbl3"), $fn["2"], "", $classes) .
                bb_af_txt($subline, "", "bbaftxtsubmit"),
                $faction, "POST", "", $fclass);
            return $res;
        }

        private function dv_permissions() {
            $res = "";
            $res .= bb_af_sep($this->getstr("sep6"), "", "", "bbafpsep bbafpsepbig bbafpsepf");
            global $loadedmodules;

            $dbres = db_do(self::getsql("getroleslistsys"));
            $roles = [];
            foreach ($dbres as $role) {
                $roles[$role["id"]]["name"] = $role["name"];
                $roles[$role["id"]]["letter"] = $role["letter"];
            }

            $modules = bb_af_sep($this->getname(), "", "", "bbafpsep bbafpsepf");
            $modules .= $this->buildpermform($this, $roles, FALSE);
            foreach ($loadedmodules as $module) {
                if ($module === $this || !is_array($module->getpermissions()))
                    continue;
                $modules .= bb_af_sep($module->getname(), "", "", "bbafpsep bbafpsepf");
                $modules .= $this->buildpermform($module, $roles);
            }

            $res .= bb_af_form(
                $modules .
                bb_af_txt(bb_af_form_submit("updateperms", $this->getstr("btnupdate"), FALSE, "", "bbacgreen"), "", "bbaftxtsubmit"),
                "", "POST", "", "bbfedit");

            return $res;
        }

        private function checkpost($home) {
            $res = "";
            if (issetp("updateperms")) {
                $ct = 0;
                $args = [];
                $insertq = "";
                foreach ($_POST as $key => $val) {
                    if (strinstr("fn", $key)) {
                        $module = $val["moduleid"];
                        $mode   = $val["mode"];
                        unset($val["moduleid"]);
                        unset($val["mode"]);
                        foreach ($val as $roleid) {
                            $insertq .= "(:module$ct, :roleid$ct, :mode$ct), ";
                            $args["module$ct"] = "uim_" . $module;
                            $args["roleid$ct"] = $roleid;
                            $args["mode$ct"] = $mode;
                            $ct++;
                        }
                    }
                }
                $insertq = substr($insertq, 0, -2);
                $insertq = <<<EOFL
                INSERT INTO permissions (module, roleid, mode) VALUES $insertq;
EOFL;

                db_do("DELETE FROM permissions WHERE module LIKE 'uim_%' AND NOT (roleid = 0 AND module = 'uim_sysadmin');", "",
                    $this->getstr("log4"));
                db_do($insertq, $args,
                    $this->getstr("log5"));
            } else
            if (issetp("addrole") || issetp("editrole")) {
                $validate = validate_form([
                    /* name */
                    ["valp", ["fn0"],
                        "bberror", [$this->getstr("err1")], "formalert", ["fn0"]],
                    ["valdb_upname", [ispd("fnb"), ispd("fn0"), self::getsql("getrolebyname"), ["name" => ispd("fn0")]],
                        "bberror", [$this->getstr("err3")], "formalert", ["fn0"]],
                    /* letter */
                    ["valp", ["fn1"],
                        "bberror", [$this->getstr("err2"), "", "bbreterror bbacred"], "formalert", ["fn1"]],
                    ["valdb_upname", [ispd("fnc"), ispd("fn1"), self::getsql("getrolebyletter"), ["letter" => ispd("fn1")]],
                        "bberror", [$this->getstr("err4")], "formalert", ["fn1"]],
                ]);
                $valexists = [
                    /* if role exists */
                    ["valdb_e", [self::getsql("getrolebyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando", "")]],
                ];
                if (issetp("addrole")) {
                    if ($validate !== "") {
                        $_GET = arraddrem($home, "add", "view");
                        return $validate;
                    }
                    $dbres = db_do(self::getsql("addrole"), [
                        "name"        => ispd("fn0"),
                        "letter"      => ispd("fn1"),
                        "description" => ispd("fn2"),
                    ], $this->getstr("log1", ispd("fn0")));
                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", 1));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf1", ispd("fn0")));
                } else
                if (issetp("editrole")) {
                    if (($validate = validate_form($valexists, $validate)) !== "") {
                        $_GET = arraddrem($home, ["edit" => ispd("fna")], "view");
                        return $validate;
                    }
                    if (ispd("fna") === 0) return bberror($this->getstr("errfky1"));
                    $dbres = db_do(self::getsql("editrole"), [
                        "id"          => ispd("fna"),
                        "name"        => ispd("fn0"),
                        "letter"      => ispd("fn1"),
                        "description" => ispd("fn2"),
                    ], $this->getstr("log2", ispd("fn0")));
                    if (!valdbres($dbres)) {
                        $res .= bberror($this->getstr("errsql", ""));
                        return $res;
                    }
                    $res .= bbinfo($this->getstr("inf2"), "", "bbreterror bbacgreen");
                }

            } else
            if (issetp("deleterole")) {
                $valexists = [
                    /* if role exists */
                    ["valdb_e", [self::getsql("getrolebyid"), ["id" => ispd("fna")]],
                        "bberror", [$this->getstr("errnocando", "")]],
                ];
                if (($validate = validate_form($valexists)) !== "") {
                    $_GET = arraddrem($home, ["view" => ispd("fna")]);
                    return $validate;
                }
                $dbres = db_do(self::getsql("deleterole"), ["id" => ispd("fna")], $this->getstr("log3", ispd("fnb")));
                if (!valdbres($dbres)) {
                    $res .= bberror($this->getstr("errsql", 2));
                    return $res;
                }
                $res .= bbinfo($this->getstr("inf3", ispd("fnb")));
            }
            return $res;
        }


        private function buildpermform($module, $roles, $edit = TRUE) {
            $res = "";
            if (!is_array($module->getpermissions())) {
                return "";
            }
            /* go to DB get the roles permissions */
            $dbres = db_do(self::getsql("getmodulesperms"), ["module" => "uim_" . $module->getid()]);
            $perms = [];
            foreach ($dbres as $p) {
                $perms[$p["roleid"]][$p["mode"]] = TRUE;
            }
            foreach ($module->getpermissions() as $perm) {
                $arrname = "fn" . $module->getid() . "_" . $perm[0];
                $permsform = "";
                foreach ($roles as $id => $role) {
                    $check = "";
                    if (isset($perms[$id][$perm[0]]))
                        $check = "checked";
                    if ($edit === TRUE) {
                        $permsform .= bb_af_form_hidden($arrname . "[moduleid]", $module->getid());
                        $permsform .= bb_af_form_hidden($arrname . "[mode]", $perm[0]);
                        $permsform .= bb_af_form_incheck(
                            $arrname . "[]", $id, $role["letter"],
                            "", "", "bfafbtn bbafroundbtn bbacyel", "", $check);
                    } else {
                        $permsform .= bb_af_txt($role["letter"], "margin-left: 5px; margin-right: 5px", "bbafin bbafroundbtn " . $check);
                    }
                }
                $res .= bb_af_sep($perm[1],  $permsform, "", "bbafsitem " . ($edit ? "afhover" : ""));
            }
            return $res;
        }


        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what) {
            if ($what === "getroleslist") return <<<EOFF
            SELECT * FROM role
                WHERE id > 0
                ORDER BY name;
EOFF;
            else if ($what === "getroleslistsys") return <<<EOFF
            SELECT * FROM role
                ORDER BY name;
EOFF;
            else if ($what === "getmodulesperms") return <<<EOFF
            SELECT permissions.mode, role.id AS roleid, role.name, role.letter
                FROM permissions
                    JOIN role
                        ON role.id = permissions.roleid
            WHERE module = :module;
EOFF;
            else if ($what === "getrolebyid") return <<<EOFF
            SELECT * FROM role
                WHERE id = :id
                  AND id > 0;
EOFF;
            else if ($what === "getrolebyname") return <<<EOFF
            SELECT * FROM role
                WHERE name = :name;
EOFF;
            else if ($what === "getrolebyletter") return <<<EOFF
            SELECT * FROM role
                WHERE letter = :letter;
EOFF;
            else if ($what === "addrole") return <<<EOFF
            INSERT INTO role (name, description, letter)
                VALUES (:name, :description, :letter);
EOFF;
            else if ($what === "editrole") return <<<EOFF
            UPDATE role SET
                    name        = :name,
                    description = :description,
                    letter      = :letter
                WHERE id = :id;
EOFF;
            else if ($what === "deleterole") return <<<EOFF
            DELETE FROM role WHERE id = :id;
EOFF;
            else if ($what === "isrolebeeingused") return <<<EOFF
            SELECT * FROM userroles WHERE roleid = :id;
EOFF;
        return "";
        }


    }
    return new uisysadmin();
?>
