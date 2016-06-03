<?php
    class uidbdump extends uimodule {
        protected $moduleid    = 'dbdump';
        protected $modulename  = 'DB Dump';
        protected $menutxt     = 'DB Dump';
        protected $url         = 'dbdump';
        protected $mpref       = 0;

        public function getpermissions() {
            /* possible permissions */
            return [
                ["dv",          $this->getstr("perm1")],
                ["download",    $this->getstr("perm2")],
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


            if (issetg("downloadfile") && $this->cando("download")) {
                $this->download($home);
                return NULL;
            } else
            if ($this->cando("dv")) {
                $ctt .= $this->defaultview($home);
            } else {
                return error404();
            }
            return $ctt;
        }












        /* * * * * * * * * * * * * * * DUMP * * * * * * * * * * * * * * */
        private function defaultview($home) {
            $res      = "";
            $homeurl  = gettourl($home);
            $dlurl    = gettourl($home, "download");

            $title    = $this->getstr("sep1");
            $subline  = "";
            $preform  = "";
            $classes  = "bbafsitem afhover";
            $fclass   = "bbfedit";
            $faction  = $homeurl;
            $fcontent = "";

            $fn["0"] = ispd("fn0");
            $fn["1"] = ispd("fn1");
            $fn["2"] = ispd("fn2");
            $fn["3"] = ispd("fn3");
            $fn["4"] = ispd("fn4");
            $fn["5"] = ispd("fn5");

            /* patient filter */
            $dbres = db_do(self::getsql("sel-patient"));
            if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", 1));
            $selopts = bb_af_form_inselbox_auto("fn0", $dbres,
                "pid", "pidentifier", $fn["0"], bb_af_form_inselboxitem("-1", ""));
            $fcontent .= bb_af_sep($this->getstr("lbl1"), $selopts, "", $classes);

            /* activity filter */
            $dbres = db_do(self::getsql("sel-activity"));
            if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", 1));
            $selopts = bb_af_form_inselbox_auto("fn1", $dbres,
                "activityid", "aname", $fn["1"], bb_af_form_inselboxitem("-1", ""));
            $fcontent .= bb_af_sep($this->getstr("lbl2"), $selopts, "", $classes);

            /* user filter */
            $dbres = db_do(self::getsql("sel-user"));
            if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", 1));
            $selopts = bb_af_form_inselbox_auto("fn2", $dbres,
                "uid", "uname", $fn["2"], bb_af_form_inselboxitem("-1", ""));
            $fcontent .= bb_af_sep($this->getstr("lbl3"), $selopts, "", $classes);

            /* question filter */
            $dbres = db_do(self::getsql("sel-question"));
            if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", 1));
            $selopts = bb_af_form_inselbox_auto("fn3", $dbres,
                "qid", "question", $fn["3"], bb_af_form_inselboxitem("-1", ""));
            $fcontent .= bb_af_sep($this->getstr("lbl4"), $selopts, "", $classes);

            /* answeritems filters */
            $dbres = db_do(self::getsql("sel-answer"));
            if (!valdbres($dbres, 0)) return bberror($this->getstr("errsql", 1));
            $selopts = bb_af_form_inselbox_auto("fn4", $dbres,
                "val", "val", $fn["4"], bb_af_form_inselboxitem("-1", ""));
            $fcontent .= bb_af_sep($this->getstr("lbl5"), $selopts, "", $classes);
            $fcontent .= bb_af_sep($this->getstr("lbl6"), bb_af_form_intxt("fn5", $fn["5"]), "", $classes);


            $subline .=
                bb_af_form_submit("download", $this->getstr("btndownload"), FALSE, "", "bbacgreen", '', '')
                ;

            $res .= bb_af_sep($title, "", "", "bbafpsep bbafpsepbig bbafpsepf");
            $res .= $preform;
            $res .= bb_af_form(
                $fcontent .
                ($subline !== "" ? bb_af_txt($subline, "", "bbaftxtsubmit") : ""),
                $faction, "POST", "", $fclass);
            return $res;
        }







        /* * * * * * * * * * * * * * * GENFILE * * * * * * * * * * * * * * */
        private function download($home) {
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=dbdump.csv');
            /* * * * * * * * * * * * * * * * * * * *
             *          DOWNLOAD HEADERS END
             * * * * * * * * * * * * * * * * * * * */

            $r = "";
            $args = [];

            /* patient filter */
            if (ispd("fn0") != "-1") {
                $w = "pid = :pid";
                $args["pid"] = ispd("fn0");
                if ($r != "") $r .= " AND $w";
                else          $r .= "$w";
            }

            /* activity filter */
            if (ispd("fn1") != "-1") {
                $w = "activityid = :activityid";
                $args["activityid"] = ispd("fn1");
                if ($r != "") $r .= " AND $w";
                else          $r .= "$w";
            }

            /* user filter */
            if (ispd("fn2") != "-1") {
                $w = "uid = :uid";
                $args["uid"] = ispd("fn2");
                if ($r != "") $r .= " AND $w";
                else          $r .= "$w";
            }

            /* question filter */
            if (ispd("fn3") != "-1") {
                $w = "qid = :qid";
                $args["qid"] = ispd("fn3");
                if ($r != "") $r .= " AND $w";
                else          $r .= "$w";
            }

            /* answeritems filter */
            if (ispd("fn4") != "-1" || ispd("fn5") != "") {
                $w = "";
                if (ispd("fn4") != "-1" ) {
                    $w .= "answeritems ILIKE :ai1";
                    $args["ai1"] = "%". ispd("fn4") ."%";
                }
                if (ispd("fn5") != "" ) {
                    $w2 = "answeritems ILIKE :ai2";
                    $args["ai2"] = "%". ispd("fn5") ."%";
                    $w .= ($w != "") ? " OR $w2" : $w2;
                }
                $w = "($w)";
                if ($r != "") $r .= " AND $w";
                else          $r .= "$w";
            }


            if ($r != "") {
                $r = "WHERE $r";
            }


            $tmp = fopen("php://output", "r+");
            fputcsv($tmp, ["Patient",
                "Activity Name", "Date", "Created By",
                "Question", "Answer Type", "Answer", "Info"]);
            $dbres = db_do(self::getsql("download", $r), $args);
            foreach ($dbres as $row) {
                $line = [
                    $row["pidentifier"],
                    $row["aname"], $row["adate"], $row["uname"],
                    $row["question"], $row["itname"], $row["answeritems"], $row["info"],
                ];
                fputcsv($tmp, $line);
            }

            die();
        }











        /* * * * * * * * * * * * * * * CHECKPOST * * * * * * * * * * * * * * */
        private function checkpost($home) {
            $res = "";

            if (issetp("download") && $this->cando("download")) {
                $this->download($home);
            }

            return $res;
        }























        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what, $pager = "") {
            switch ($what) {
                case "download": return <<<EOFF
        SELECT
            pidentifier, aname, adate, uname, question, itname, answeritems, info
            FROM actionanswers
            $pager
            ORDER BY adate
            ;
EOFF;
                case "sel-patient": return <<<EOFF
        SELECT DISTINCT pid, pidentifier
            FROM actionanswers
            ORDER BY pidentifier;
EOFF;
                case "sel-activity": return <<<EOFF
        SELECT DISTINCT activityid, aname
            FROM actionanswers
            ORDER BY aname;
EOFF;
                case "sel-user": return <<<EOFF
        SELECT DISTINCT uid, uname
            FROM actionanswers
            ORDER BY uname;
EOFF;
                case "sel-question": return <<<EOFF
        SELECT DISTINCT qid, question
            FROM actionanswers
            ORDER BY question;
EOFF;
                case "sel-answer": return <<<EOFF
        SELECT DISTINCT val
            FROM answeritem
            ORDER BY val;
EOFF;

            }
            return "";
        }
    }
    return new uidbdump();
?>
